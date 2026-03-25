<?php
$learnerProfileName = ems_profile_text($portalUser['full_name'] ?? '', 'Learner');
$learnerProfileEmail = ems_profile_text($portalUser['email'] ?? '', 'Not provided');
$learnerProfilePhone = ems_profile_text($portalUser['mobile_number'] ?? '', 'Not provided');
$learnerProfileCurrentRole = ems_profile_text($portalUser['current_role'] ?? '', 'Not provided');
$learnerProfileInterest = ems_profile_text($portalUser['learning_interest'] ?? '', 'Not provided');
$learnerProfileExperience = ems_profile_text($portalUser['experience_level'] ?? '', 'Not provided');
$learnerProfileGoal = ems_profile_text($portalUser['learning_goal'] ?? '', 'Not provided');
?>

<main class="provider-main-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">My Profile</h1>
        <p class="dashboard-subtitle">View your learner profile information.</p>
    </div>

    <section class="dashboard-section">
        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <tbody>
                    <tr><th>Full Name</th><td><?php echo ems_e($learnerProfileName); ?></td></tr>
                    <tr><th>Email</th><td><?php echo ems_e($learnerProfileEmail); ?></td></tr>
                    <tr><th>Phone</th><td><?php echo ems_e($learnerProfilePhone); ?></td></tr>
                    <tr><th>Current Role</th><td><?php echo ems_e($learnerProfileCurrentRole); ?></td></tr>
                    <tr><th>Learning Interest</th><td><?php echo ems_e($learnerProfileInterest); ?></td></tr>
                    <tr><th>Experience Level</th><td><?php echo ems_e($learnerProfileExperience); ?></td></tr>
                    <tr><th>Learning Goal</th><td><?php echo ems_e($learnerProfileGoal); ?></td></tr>
                </tbody>
            </table>
        </div>
    </section>
</main>
