<?php
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/../learner/includes/learner_data.php');

$activeLearner = null;
$activeLearnerId = 0;
if (ems_is_logged_in() && ems_current_role() === 'learner') {
    $activeLearner = ems_load_portal_user($conn);
    $activeLearnerId = (int)($activeLearner['id'] ?? 0);
}

$selectedCourseId = (int)($_GET['course_id'] ?? 0);
$summaryCurrency = 'USD';
$courseCount = 1;
$originalAmount = 49.99;
$discountAmount = 38.00;
$totalAmount = 11.99;
$checkoutMode = 'single';
$checkoutDisabled = false;
$checkoutMessage = '';

if ($activeLearnerId > 0) {
    if ($selectedCourseId > 0) {
        $checkoutMode = 'single';
        $selectedCourse = ems_learner_course_is_eligible($conn, $selectedCourseId);

        if (!$selectedCourse) {
            $checkoutDisabled = true;
            $checkoutMessage = 'Selected course is unavailable for enrollment.';
            $originalAmount = 0.0;
            $discountAmount = 0.0;
            $totalAmount = 0.0;
        } elseif (ems_learner_is_enrolled_in_course($conn, $activeLearnerId, $selectedCourseId)) {
            $checkoutDisabled = true;
            $checkoutMessage = 'You are already enrolled in this course.';
            $originalAmount = 0.0;
            $discountAmount = 0.0;
            $totalAmount = 0.0;
            $summaryCurrency = strtoupper(trim((string)($selectedCourse['currency_code'] ?? 'USD')));
        } else {
            $isPaid = strtolower((string)($selectedCourse['access_type'] ?? 'free')) === 'paid';
            $summaryCurrency = strtoupper(trim((string)($selectedCourse['currency_code'] ?? 'USD')));
            $originalAmount = $isPaid ? (float)($selectedCourse['price_amount'] ?? 0) : 0.0;
            $discountAmount = 0.0;
            $totalAmount = max(0.0, $originalAmount - $discountAmount);
            $courseCount = 1;
        }
    } else {
        $checkoutMode = 'cart';
        $cartPayload = ems_learner_fetch_cart_items($conn, $activeLearnerId, 500);
        $cartItems = $cartPayload['items'] ?? [];
        $cartSummary = $cartPayload['summary'] ?? [];

        $courseCount = count($cartItems);
        $summaryCurrency = strtoupper(trim((string)($cartSummary['currency_code'] ?? 'USD')));
        $originalAmount = (float)($cartSummary['subtotal'] ?? 0);
        $discountAmount = (float)($cartSummary['discount'] ?? 0);
        $totalAmount = (float)($cartSummary['total'] ?? max(0.0, $originalAmount - $discountAmount));

        if ($courseCount <= 0) {
            $checkoutDisabled = true;
            $checkoutMessage = 'Your cart is empty. Add courses to continue.';
        }
    }
}

if ($summaryCurrency === '') {
    $summaryCurrency = 'USD';
}

$originalText = ems_learner_currency_format($originalAmount, $summaryCurrency);
$discountText = ems_learner_currency_format($discountAmount, $summaryCurrency);
$totalText = ems_learner_currency_format($totalAmount, $summaryCurrency);

$discountPercent = 0;
if ($originalAmount > 0 && $discountAmount > 0) {
    $discountPercent = (int)round(($discountAmount / $originalAmount) * 100);
}

$discountLabel = $discountPercent > 0 ? ('Discounts (' . $discountPercent . '% Off):') : 'Discounts:';
$totalLabel = 'Total (' . (int)$courseCount . ' course' . ((int)$courseCount === 1 ? '' : 's') . '):';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Process | EduSkill Marketplace</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .payment-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.08);
            margin: 40px auto;
            max-width: 900px;
            padding: 32px 24px;
        }
        .order-summary {
            background: #f7f8fa;
            border-radius: 8px;
            padding: 24px 18px;
        }
        .pay-btn {
            background: #6c2eb5;
            color: #fff;
            font-weight: 600;
            font-size: 1.2rem;
            border-radius: 8px;
            padding: 12px 0;
        }
        .pay-btn i {
            margin-right: 8px;
        }
        .payment-methods .nav-tabs .nav-link.active {
            background: #f7f8fa;
            border-color: #dee2e6 #dee2e6 #fff;
        }
        .payment-methods .tab-content {
            margin-top: 16px;
        }
        @media (max-width: 768px) {
            .payment-container {
                padding: 16px 4px;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container payment-container">
        <div id="paymentStatusAlert" class="alert d-none" role="alert"></div>
        <div class="row g-4">
            <div class="col-md-7">
                <h5 class="mb-3">Country</h5>
                <select class="form-select mb-4" aria-label="Country select">
                    <option selected>Nepal</option>
                </select>
                <div class="payment-methods">
                    <h5 class="mb-3">Payment method</h5>
                    <ul class="nav nav-tabs" id="paymentTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="card-tab" data-bs-toggle="tab" data-bs-target="#card" type="button" role="tab">Cards</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="paypal-tab" data-bs-toggle="tab" data-bs-target="#paypal" type="button" role="tab">PayPal</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="paymentTabContent">
                        <div class="tab-pane fade show active" id="card" role="tabpanel">
                            <form>
                                <div class="mb-3">
                                    <label class="form-label">Card number</label>
                                    <input type="text" class="form-control" placeholder="1234 5678 9012 3456">
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="form-label">Expiry date</label>
                                        <input type="text" class="form-control" placeholder="MM/YY">
                                    </div>
                                    <div class="col">
                                        <label class="form-label">CVC/CVV</label>
                                        <input type="text" class="form-control" placeholder="CVC">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Name on card</label>
                                    <input type="text" class="form-control" placeholder="Name on card">
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="saveCard">
                                    <label class="form-check-label" for="saveCard">Securely save this card for my later purchase</label>
                                </div>
                                <div class="mb-3">
                                    <img src="https://img.icons8.com/color/48/000000/visa.png" height="32" alt="Visa">
                                    <img src="https://img.icons8.com/color/48/000000/mastercard.png" height="32" alt="Mastercard">
                                    <img src="https://img.icons8.com/color/48/000000/amex.png" height="32" alt="Amex">
                                    <img src="https://img.icons8.com/color/48/000000/discover.png" height="32" alt="Discover">
                                    <img src="https://img.icons8.com/color/48/000000/jcb.png" height="32" alt="JCB">
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="paypal" role="tabpanel">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://img.icons8.com/color/48/000000/paypal.png" height="32" alt="PayPal">
                                <span class="ms-2">PayPal</span>
                            </div>
                            <button class="btn btn-outline-primary w-100">Continue with PayPal</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="order-summary mb-4">
                    <h5>Order summary</h5>
                    <div class="d-flex justify-content-between mt-3">
                        <span>Original Price:</span>
                        <span><?php echo ems_e($originalText); ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><?php echo ems_e($discountLabel); ?></span>
                        <span class="text-success">-<?php echo ems_e($discountText); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span><?php echo ems_e($totalLabel); ?></span>
                        <span><?php echo ems_e($totalText); ?></span>
                    </div>
                    <button
                        id="payNowButton"
                        class="btn pay-btn w-100 mt-4"
                        type="button"
                        <?php echo $checkoutDisabled ? 'disabled' : ''; ?>
                    ><i class="fas fa-lock"></i>Pay <?php echo ems_e($totalText); ?></button>
                </div>
                <div class="text-center mt-2">
                    <strong>30-Day Money-Back Guarantee</strong>
                    <p class="mb-0">Not satisfied? Get a full refund within 30 days.<br>Simple and straightforward!</p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    window.eduSkillPaymentContext = {
        isLearnerLoggedIn: <?php echo $activeLearnerId > 0 ? 'true' : 'false'; ?>,
        learnerUserId: <?php echo (int)$activeLearnerId; ?>,
        csrfToken: <?php echo json_encode((string)($activeLearnerId > 0 ? ems_csrf_token() : ''), JSON_UNESCAPED_UNICODE); ?>,
        learnerApiUrl: <?php echo json_encode((string)(BASE_URL . 'learner/api.php'), JSON_UNESCAPED_UNICODE); ?>,
        loginUrl: <?php echo json_encode((string)(BASE_URL . 'auth/login.php'), JSON_UNESCAPED_UNICODE); ?>,
        redirectUrl: <?php echo json_encode((string)(BASE_URL . 'learner/?page=courses'), JSON_UNESCAPED_UNICODE); ?>,
        checkoutMode: <?php echo json_encode((string)$checkoutMode, JSON_UNESCAPED_UNICODE); ?>,
        courseId: <?php echo (int)$selectedCourseId; ?>,
        checkoutDisabled: <?php echo $checkoutDisabled ? 'true' : 'false'; ?>,
        checkoutMessage: <?php echo json_encode((string)$checkoutMessage, JSON_UNESCAPED_UNICODE); ?>
    };

    (function () {
        var ctx = window.eduSkillPaymentContext || {};
        var payBtn = document.getElementById('payNowButton');
        var statusAlert = document.getElementById('paymentStatusAlert');

        function resolvePaymentMethod() {
            var activeTab = document.querySelector('#paymentTab .nav-link.active');
            if (activeTab && activeTab.id === 'paypal-tab') {
                return 'paypal';
            }
            return 'card';
        }

        function showStatus(type, message) {
            if (!statusAlert) {
                return;
            }
            statusAlert.className = 'alert alert-' + type;
            statusAlert.textContent = String(message || '');
        }

        function setBusy(busy, text) {
            if (!payBtn) {
                return;
            }
            if (busy) {
                payBtn.disabled = true;
                payBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>' + String(text || 'Processing...');
            } else {
                payBtn.disabled = !!ctx.checkoutDisabled;
                payBtn.innerHTML = '<i class="fas fa-lock"></i>Pay <?php echo ems_e($totalText); ?>';
            }
        }

        if (!payBtn) {
            return;
        }

        payBtn.addEventListener('click', function (event) {
            event.preventDefault();

            if (!ctx.isLearnerLoggedIn) {
                window.location.href = ctx.loginUrl || 'auth/login.php';
                return;
            }

            if (ctx.checkoutDisabled) {
                showStatus('warning', ctx.checkoutMessage || 'Checkout is unavailable for this selection.');
                return;
            }

            var fd = new FormData();
            fd.set('action', 'checkout');
            fd.set('csrf_token', String(ctx.csrfToken || ''));
            fd.set('payment_method', resolvePaymentMethod());

            var courseId = Number(ctx.courseId || 0);
            if (courseId > 0 && String(ctx.checkoutMode || '') === 'single') {
                fd.set('course_id', String(courseId));
            }

            setBusy(true, 'Processing payment...');
            showStatus('info', 'Processing your payment securely...');

            fetch(String(ctx.learnerApiUrl || ''), {
                method: 'POST',
                credentials: 'same-origin',
                body: fd
            }).then(function (response) {
                return response.json().catch(function () {
                    throw new Error('Invalid payment API response.');
                }).then(function (payload) {
                    if (!response.ok || !payload || payload.ok !== true) {
                        throw new Error((payload && payload.message) || 'Payment request failed.');
                    }
                    return payload.data || {};
                });
            }).then(function (data) {
                showStatus('success', 'Payment successful. Redirecting to your learning portal...');
                var redirectTo = (data && data.redirect_url) ? data.redirect_url : (ctx.redirectUrl || '/');
                window.setTimeout(function () {
                    window.location.href = redirectTo;
                }, 1000);
            }).catch(function (error) {
                showStatus('danger', error.message || 'Unable to process payment right now.');
                setBusy(false, '');
            });
        });

        if (ctx.checkoutDisabled && String(ctx.checkoutMessage || '') !== '') {
            showStatus('warning', ctx.checkoutMessage);
        }
    })();
    </script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
