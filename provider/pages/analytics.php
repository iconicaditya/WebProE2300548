<main class="provider-main-content">
    <!-- HEADER SECTION -->
    <div class="dashboard-header">
        <h1 class="dashboard-title">Analytics & Reports</h1>
        <p class="dashboard-subtitle">Analyze your course performance and trends.</p>
    </div>

    <!-- ANALYTICS & REPORTS SECTION -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Performance Metrics</h2>
        </div>

        <div class="overview-grid">
            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">📈</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Total Views</p>
                        <p class="overview-card-value">2,450</p>
                    </div>
                </div>
                <p class="overview-card-footer">+18% from last month</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">👥</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">New Enrollments</p>
                        <p class="overview-card-value">145</p>
                    </div>
                </div>
                <p class="overview-card-footer">+12% from last month</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">⏱️</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Avg Duration</p>
                        <p class="overview-card-value">2h 30m</p>
                    </div>
                </div>
                <p class="overview-card-footer">Per course session</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">💯</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Completion Rate</p>
                        <p class="overview-card-value">73%</p>
                    </div>
                </div>
                <p class="overview-card-footer">Overall average</p>
            </div>
        </div>
    </section>

    <section class="dashboard-section">
        <div class="charts-grid">
            <!-- Monthly Enrollments Chart -->
            <div class="chart-container">
                <h3 class="chart-title">Monthly Enrollments Trend</h3>
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
                <h3 class="chart-title">Revenue Trend (Last 6 Months)</h3>
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
