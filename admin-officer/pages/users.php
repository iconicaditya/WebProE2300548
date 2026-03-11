<div class="admin-page-header">
    <h1 class="page-title">Users Management</h1>
    <p class="page-subtitle">Manage all learners, instructors, and platform users</p>
</div>

<div class="admin-filters">
    <input type="text" placeholder="Search users by name or email..." class="filter-input">
    <select class="filter-select">
        <option>All Users</option>
        <option>Learners</option>
        <option>Instructors</option>
    </select>
    <button class="filter-btn">Filter</button>
</div>

<div class="dashboard-table-wrapper">
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Status</th>
                <th>Joined Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>John Doe</td>
                <td>john.doe@email.com</td>
                <td>Learner</td>
                <td><span class="status-active">Active</span></td>
                <td>Dec 15, 2024</td>
                <td>
                    <button class="action-btn" title="View">👁️</button>
                    <button class="action-btn" title="Edit">✏️</button>
                </td>
            </tr>
            <tr>
                <td>Sarah Smith</td>
                <td>sarah.smith@email.com</td>
                <td>Instructor</td>
                <td><span class="status-active">Active</span></td>
                <td>Nov 20, 2024</td>
                <td>
                    <button class="action-btn" title="View">👁️</button>
                    <button class="action-btn" title="Edit">✏️</button>
                </td>
            </tr>
            <tr>
                <td>Mike Johnson</td>
                <td>mike.johnson@email.com</td>
                <td>Learner</td>
                <td><span class="status-inactive">Inactive</span></td>
                <td>Oct 05, 2024</td>
                <td>
                    <button class="action-btn" title="View">👁️</button>
                    <button class="action-btn" title="Edit">✏️</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
