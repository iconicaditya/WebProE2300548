<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complete Profile</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <style>
        .profile-section { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 2rem; margin-bottom: 2rem; }
        .profile-section h2 { margin-top: 0; }
        .profile-form label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .profile-form input, .profile-form textarea, .profile-form select { width: 100%; padding: 0.5rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 4px; }
        .profile-form .form-row { display: flex; gap: 1rem; }
        .profile-form .form-row > div { flex: 1; }
        .profile-form button { background: #16c1d6; color: #fff; border: none; border-radius: 5px; padding: 0.7rem 1.5rem; font-size: 1rem; font-weight: 500; cursor: pointer; }
        .profile-form button:hover { background: #139ab6; }
        .profile-list { margin-top: 2rem; }
        .profile-list table { width: 100%; border-collapse: collapse; }
        .profile-list th, .profile-list td { padding: 0.75rem 1rem; border-bottom: 1px solid #eee; text-align: left; }
        .profile-list th { background: #f3f7fa; }
        .profile-list tr:last-child td { border-bottom: none; }
    </style>
</head>
<body>
    <div class="profile-section">
        <h2>Education</h2>
        <form class="profile-form" id="education-form">
            <div class="form-row">
                <div>
                    <label for="degree">Degree</label>
                    <input type="text" id="degree" name="degree" required>
                </div>
                <div>
                    <label for="institution">Institution</label>
                    <input type="text" id="institution" name="institution" required>
                </div>
                <div>
                    <label for="year">Year</label>
                    <input type="number" id="year" name="year" min="1950" max="2100" required>
                </div>
            </div>
            <button type="submit">Add Education</button>
        </form>
        <div class="profile-list" id="education-list">
            <h3>Education List</h3>
            <table>
                <thead>
                    <tr><th>Degree</th><th>Institution</th><th>Year</th></tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div class="profile-section">
        <h2>Experience</h2>
        <form class="profile-form" id="experience-form">
            <div class="form-row">
                <div>
                    <label for="position">Position</label>
                    <input type="text" id="position" name="position" required>
                </div>
                <div>
                    <label for="company">Company</label>
                    <input type="text" id="company" name="company" required>
                </div>
                <div>
                    <label for="exp-years">Years</label>
                    <input type="number" id="exp-years" name="exp-years" min="0" max="60" required>
                </div>
            </div>
            <button type="submit">Add Experience</button>
        </form>
        <div class="profile-list" id="experience-list">
            <h3>Experience List</h3>
            <table>
                <thead>
                    <tr><th>Position</th><th>Company</th><th>Years</th></tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div class="profile-section">
        <h2>Certificates</h2>
        <form class="profile-form" id="certificate-form">
            <div class="form-row">
                <div>
                    <label for="cert-name">Certificate Name</label>
                    <input type="text" id="cert-name" name="cert-name" required>
                </div>
                <div>
                    <label for="cert-org">Issued By</label>
                    <input type="text" id="cert-org" name="cert-org" required>
                </div>
                <div>
                    <label for="cert-year">Year</label>
                    <input type="number" id="cert-year" name="cert-year" min="1950" max="2100" required>
                </div>
            </div>
            <button type="submit">Add Certificate</button>
        </form>
        <div class="profile-list" id="certificate-list">
            <h3>Certificate List</h3>
            <table>
                <thead>
                    <tr><th>Name</th><th>Issued By</th><th>Year</th></tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <script>
        // Simple JS to add entries to the lists (frontend only)
        function addToList(formId, listId, fields) {
            document.getElementById(formId).addEventListener('submit', function(e) {
                e.preventDefault();
                const row = document.createElement('tr');
                fields.forEach(f => {
                    const val = this.querySelector(`[name="${f}"]`).value;
                    const td = document.createElement('td');
                    td.textContent = val;
                    row.appendChild(td);
                });
                document.querySelector(`#${listId} tbody`).appendChild(row);
                this.reset();
            });
        }
        addToList('education-form', 'education-list', ['degree', 'institution', 'year']);
        addToList('experience-form', 'experience-list', ['position', 'company', 'exp-years']);
        addToList('certificate-form', 'certificate-list', ['cert-name', 'cert-org', 'cert-year']);
    </script>
</body>
</html>
