<main class="provider-main-content">
    <!-- HEADER SECTION -->
    <div class="dashboard-header">
        <h1 class="dashboard-title">Dashboard Overview</h1>
        <p class="dashboard-subtitle">Welcome back! Here's your performance summary.</p>
    </div>

    <!-- OVERVIEW CARDS SECTION -->
    <section class="dashboard-section" id="dashboard">
        <div class="overview-grid">
            <!-- Total Courses Card -->
            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">📚</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Total Courses</p>
                        <p class="overview-card-value">12</p>
                    </div>
                </div>
                <p class="overview-card-footer">3 courses this month</p>
            </div>

            <!-- Total Students Card -->
            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">👥</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Total Students</p>
                        <p class="overview-card-value">340</p>
                    </div>
                </div>
                <p class="overview-card-footer">+45 new this month</p>
            </div>

            <!-- Monthly Revenue Card -->
            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">💰</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Monthly Revenue</p>
                        <p class="overview-card-value">$4,520</p>
                    </div>
                </div>
                <p class="overview-card-footer">+12% from last month</p>
            </div>

            <!-- Avg Rating Card -->
            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">⭐</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Avg Rating</p>
                        <p class="overview-card-value">4.5</p>
                    </div>
                </div>
                <p class="overview-card-footer">Based on 128 reviews</p>
            </div>

            <!-- Completion Rate Card -->
            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">✓</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Completion %</p>
                        <p class="overview-card-value">76%</p>
                    </div>
                </div>
                <p class="overview-card-footer">Average across all courses</p>
            </div>
        </div>
    </section>

    <!-- COURSE MANAGEMENT SECTION -->
    <section class="dashboard-section" id="courses">
        <div class="section-header">
            <h2 class="section-title">Course Management</h2>
            <button class="btn btn-create-course">+ Create New Course</button>
        </div>

        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Price</th>
                        <th>Students</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Web Development</td>
                        <td>$120</td>
                        <td>85</td>
                        <td>
                            <span class="rating-stars">⭐⭐⭐⭐⭐</span>
                            <span class="rating-value">4.6</span>
                        </td>
                        <td><span class="status-badge status-active">Active</span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn edit-btn" title="Edit">✏️</button>
                                <button class="action-btn delete-btn" title="Delete">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Data Analytics</td>
                        <td>$150</td>
                        <td>60</td>
                        <td>
                            <span class="rating-stars">⭐⭐⭐⭐</span>
                            <span class="rating-value">4.4</span>
                        </td>
                        <td><span class="status-badge status-active">Active</span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn edit-btn" title="Edit">✏️</button>
                                <button class="action-btn delete-btn" title="Delete">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>UI/UX Design</td>
                        <td>$100</td>
                        <td>45</td>
                        <td>
                            <span class="rating-stars">⭐⭐⭐⭐⭐</span>
                            <span class="rating-value">4.7</span>
                        </td>
                        <td><span class="status-badge status-active">Active</span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn edit-btn" title="Edit">✏️</button>
                                <button class="action-btn delete-btn" title="Delete">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Python Basics</td>
                        <td>$80</td>
                        <td>92</td>
                        <td>
                            <span class="rating-stars">⭐⭐⭐⭐</span>
                            <span class="rating-value">4.5</span>
                        </td>
                        <td><span class="status-badge status-active">Active</span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn edit-btn" title="Edit">✏️</button>
                                <button class="action-btn delete-btn" title="Delete">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Advanced JavaScript</td>
                        <td>$130</td>
                        <td>38</td>
                        <td>
                            <span class="rating-stars">⭐⭐⭐⭐⭐</span>
                            <span class="rating-value">4.8</span>
                        </td>
                        <td><span class="status-badge status-inactive">Inactive</span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn edit-btn" title="Edit">✏️</button>
                                <button class="action-btn delete-btn" title="Delete">🗑️</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- STUDENT ENROLLMENT SECTION -->
    <section class="dashboard-section" id="students">
        <div class="section-header">
            <h2 class="section-title">Student Enrollment List</h2>
        </div>

        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Enroll Date</th>
                        <th>Payment</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>John Smith</td>
                        <td>john@gmail.com</td>
                        <td>Web Dev</td>
                        <td>12 Mar 2026</td>
                        <td><span class="payment-badge payment-paid">Paid</span></td>
                        <td><span class="status-badge status-active">Active</span></td>
                    </tr>
                    <tr>
                        <td>Anna Lee</td>
                        <td>anna@gmail.com</td>
                        <td>UI/UX</td>
                        <td>10 Mar 2026</td>
                        <td><span class="payment-badge payment-paid">Paid</span></td>
                        <td><span class="status-badge status-active">Active</span></td>
                    </tr>
                    <tr>
                        <td>David Tan</td>
                        <td>david@gmail.com</td>
                        <td>Data</td>
                        <td>08 Mar 2026</td>
                        <td><span class="payment-badge payment-paid">Paid</span></td>
                        <td><span class="status-badge status-active">Active</span></td>
                    </tr>
                    <tr>
                        <td>Sara Wilson</td>
                        <td>sara@gmail.com</td>
                        <td>Python Basics</td>
                        <td>05 Mar 2026</td>
                        <td><span class="payment-badge payment-pending">Pending</span></td>
                        <td><span class="status-badge status-inactive">Inactive</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- ANALYTICS & REPORTS SECTION -->
    <section class="dashboard-section" id="analytics">
        <div class="section-header">
            <h2 class="section-title">Analytics & Reports</h2>
        </div>

        <div class="charts-grid">
            <!-- Monthly Enrollments Chart -->
            <div class="chart-container">
                <h3 class="chart-title">Monthly Enrollments Chart</h3>
                <div class="chart-placeholder">
                    <div class="chart-bars">
                        <div class="chart-bar" style="height: 35%;"></div>
                        <div class="chart-bar" style="height: 55%;"></div>
                        <div class="chart-bar" style="height: 45%;"></div>
                        <div class="chart-bar" style="height: 65%;"></div>
                        <div class="chart-bar" style="height: 50%;"></div>
                        <div class="chart-bar" style="height: 75%;"></div>
                    </div>
                    <div class="chart-labels">
                        <span>Jan</span>
                        <span>Feb</span>
                        <span>Mar</span>
                        <span>Apr</span>
                        <span>May</span>
                        <span>Jun</span>
                    </div>
                </div>
            </div>

            <!-- Revenue Trend Chart -->
            <div class="chart-container">
                <h3 class="chart-title">Revenue Trend (Line Chart)</h3>
                <div class="chart-placeholder">
                    <div class="revenue-chart">
                        <svg viewBox="0 0 400 200" class="revenue-svg">
                            <polyline points="20,160 70,140 120,155 170,110 220,130 270,90 320,110 370,60" 
                                      stroke="#4186a0" stroke-width="3" fill="none" />
                            <polyline points="20,160 70,140 120,155 170,110 220,130 270,90 320,110 370,60" 
                                      stroke="#4186a0" stroke-width="3" fill="url(#gradient)" opacity="0.2" />
                            <defs>
                                <linearGradient id="gradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" style="stop-color:#4186a0;stop-opacity:0.3" />
                                    <stop offset="100%" style="stop-color:#4186a0;stop-opacity:0" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
