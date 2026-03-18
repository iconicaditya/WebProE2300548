<?php
require_once(__DIR__ . '/../config/config.php');
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
                        <span>$49.99</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Discounts (76% Off):</span>
                        <span class="text-success">-$38.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total (1 course):</span>
                        <span>$11.99</span>
                    </div>
                    <button class="btn pay-btn w-100 mt-4"><i class="fas fa-lock"></i>Pay $11.99</button>
                </div>
                <div class="text-center mt-2">
                    <strong>30-Day Money-Back Guarantee</strong>
                    <p class="mb-0">Not satisfied? Get a full refund within 30 days.<br>Simple and straightforward!</p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
