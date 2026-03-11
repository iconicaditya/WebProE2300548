<div class="admin-page-header">
    <h1 class="page-title">Transactions Management</h1>
    <p class="page-subtitle">Monitor all payments and financial transactions</p>
</div>

<div class="admin-filters">
    <input type="text" placeholder="Search by transaction ID or user..." class="filter-input">
    <select class="filter-select">
        <option>All Transactions</option>
        <option>Completed</option>
        <option>Pending</option>
        <option>Failed</option>
    </select>
    <button class="filter-btn">Filter</button>
</div>

<div class="dashboard-table-wrapper">
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>User</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>#TXN-202412-0001</td>
                <td>John Doe</td>
                <td>$99.00</td>
                <td>Course Purchase</td>
                <td><span class="status-active">Completed</span></td>
                <td>Dec 10, 2024</td>
                <td>
                    <button class="action-btn" title="View">👁️</button>
                </td>
            </tr>
            <tr>
                <td>#TXN-202412-0002</td>
                <td>Sarah Smith</td>
                <td>$199.00</td>
                <td>Instructor Payout</td>
                <td><span class="status-active">Completed</span></td>
                <td>Dec 09, 2024</td>
                <td>
                    <button class="action-btn" title="View">👁️</button>
                </td>
            </tr>
            <tr>
                <td>#TXN-202412-0003</td>
                <td>Mike Johnson</td>
                <td>$149.00</td>
                <td>Course Purchase</td>
                <td><span class="status-pending">Pending</span></td>
                <td>Dec 08, 2024</td>
                <td>
                    <button class="action-btn" title="View">👁️</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
