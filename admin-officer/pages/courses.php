<div class="admin-page-header">
    <h1 class="page-title">Courses Management</h1>
    <p class="page-subtitle">Review, approve, and manage all platform courses</p>
</div>

<div class="admin-filters">
    <input type="text" placeholder="Search courses by title..." class="filter-input">
    <select class="filter-select">
        <option>All Courses</option>
        <option>Pending Approval</option>
        <option>Published</option>
        <option>Suspended</option>
    </select>
    <button class="filter-btn">Filter</button>
</div>

<div class="dashboard-table-wrapper">
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Course Title</th>
                <th>Instructor</th>
                <th>Category</th>
                <th>Enrollments</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>JavaScript for Beginners</td>
                <td>Sarah Smith</td>
                <td>Programming</td>
                <td>342</td>
                <td><span class="status-active">Published</span></td>
                <td>
                    <button class="action-btn" title="View">👁️</button>
                    <button class="action-btn" title="Edit">✏️</button>
                </td>
            </tr>
            <tr>
                <td>Advanced Python</td>
                <td>Mike Johnson</td>
                <td>Programming</td>
                <td>0</td>
                <td><span class="status-pending">Pending</span></td>
                <td>
                    <button class="action-btn" title="Approve">✅</button>
                    <button class="action-btn" title="Reject">❌</button>
                </td>
            </tr>
            <tr>
                <td>Figma UI Design</td>
                <td>Alex Lee</td>
                <td>Design</td>
                <td>156</td>
                <td><span class="status-active">Published</span></td>
                <td>
                    <button class="action-btn" title="View">👁️</button>
                    <button class="action-btn" title="Edit">✏️</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
