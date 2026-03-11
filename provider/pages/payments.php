<main class="provider-main-content">
    <!-- HEADER SECTION -->
    <div class="dashboard-header">
        <h1 class="dashboard-title">Payments & Receipts</h1>
        <p class="dashboard-subtitle">Manage your earnings and payment history.</p>
    </div>

    <!-- PAYMENT SUMMARY SECTION -->
    <section class="dashboard-section">
        <div class="overview-grid">
            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">💰</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Total Earnings</p>
                        <p class="overview-card-value">$12,450</p>
                    </div>
                </div>
                <p class="overview-card-footer">All time earnings</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">💸</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Pending Payout</p>
                        <p class="overview-card-value">$2,150</p>
                    </div>
                </div>
                <p class="overview-card-footer">Available for withdrawal</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">✓</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Paid Out</p>
                        <p class="overview-card-value">$10,300</p>
                    </div>
                </div>
                <p class="overview-card-footer">Total withdrawn</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">📅</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">This Month</p>
                        <p class="overview-card-value">$1,850</p>
                    </div>
                </div>
                <p class="overview-card-footer">Current month earnings</p>
            </div>
        </div>
    </section>

    <!-- TRANSACTION HISTORY SECTION -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Transaction History</h2>
        </div>

        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#TXN001</td>
                        <td>John Smith</td>
                        <td>Web Development</td>
                        <td>$120.00</td>
                        <td>12 Mar 2026</td>
                        <td><span class="status-badge status-active">Completed</span></td>
                        <td><a href="#" class="receipt-link">📄 Download</a></td>
                    </tr>
                    <tr>
                        <td>#TXN002</td>
                        <td>Anna Lee</td>
                        <td>UI/UX Design</td>
                        <td>$100.00</td>
                        <td>10 Mar 2026</td>
                        <td><span class="status-badge status-active">Completed</span></td>
                        <td><a href="#" class="receipt-link">📄 Download</a></td>
                    </tr>
                    <tr>
                        <td>#TXN003</td>
                        <td>David Tan</td>
                        <td>Data Analytics</td>
                        <td>$150.00</td>
                        <td>08 Mar 2026</td>
                        <td><span class="status-badge status-active">Completed</span></td>
                        <td><a href="#" class="receipt-link">📄 Download</a></td>
                    </tr>
                    <tr>
                        <td>#TXN004</td>
                        <td>Sara Wilson</td>
                        <td>Python Basics</td>
                        <td>$80.00</td>
                        <td>05 Mar 2026</td>
                        <td><span class="payment-badge payment-pending">Pending</span></td>
                        <td><span class="text-muted">-</span></td>
                    </tr>
                    <tr>
                        <td>#TXN005</td>
                        <td>Mike Johnson</td>
                        <td>Web Development</td>
                        <td>$120.00</td>
                        <td>02 Mar 2026</td>
                        <td><span class="status-badge status-active">Completed</span></td>
                        <td><a href="#" class="receipt-link">📄 Download</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- WITHDRAWAL SECTION -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Withdraw Earnings</h2>
        </div>

        <div class="withdrawal-box">
            <p>You have <strong>$2,150.00</strong> available for withdrawal.</p>
            <button class="btn btn-primary">Request Payout</button>
        </div>
    </section>
</main>
