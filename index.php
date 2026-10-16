<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Practical 13: AJAX & Fetch-Based CRUD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="header-flex">
        <h2>AJAX Student Management (No Page Reload)</h2>
        <button class="btn btn-success" onclick="openAddModal()">+ Add New Student</button>
    </div>

    <!-- Alert Box for Success/Error Messages -->
    <div id="alert-box"></div>

    <!-- Students Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="student-table-body">
            <!-- Data loaded dynamically via JavaScript Fetch API -->
        </tbody>
    </table>
</div>

<!-- Modal for Add / Edit -->
<div id="studentModal" class="modal">
    <div class="modal-content">
        <h3 id="modal-title">Add Student</h3>
        <input type="hidden" id="student_id">
        <div class="form-group">
            <label>Name:</label>
            <input type="text" id="name" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" id="email" required>
        </div>
        <div class="form-group">
            <label>Course:</label>
            <select id="course" required>
                <option value="">Select Course</option>
                <option value="Computer Engineering">Computer Engineering</option>
                <option value="Information Technology">Information Technology</option>
                <option value="Cyber Security">Cyber Security</option>
            </select>
        </div>
        <div class="modal-actions">
            <button class="btn" style="background-color: #6c757d;" onclick="closeModal()">Cancel</button>
            <button class="btn btn-success" onclick="saveStudent()">Save</button>
        </div>
    </div>
</div>

<script>
    const apiUrl = 'api.php';

    // Load students on page load
    document.addEventListener("DOMContentLoaded", fetchStudents);

    function showAlert(message, type) {
        const alertBox = document.getElementById('alert-box');
        alertBox.innerText = message;
        alertBox.className = type === 'success' ? 'alert-success' : 'alert-error';
        alertBox.style.display = 'block';
        setTimeout(() => { alertBox.style.display = 'none'; }, 3000);
    }

    // 1. READ / LIST Students using Fetch API
    function fetchStudents() {
        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                let tbody = document.getElementById('student-table-body');
                tbody.innerHTML = '';
                if(data.length > 0) {
                    data.forEach(student => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${student.id}</td>
                                <td>${student.name}</td>
                                <td>${student.email}</td>
                                <td>${student.course}</td>
                                <td>
                                    <button class="btn btn-warning" onclick="openEditModal(${student.id}, '${student.name}', '${student.email}', '${student.course}')">Edit</button>
                                    <button class="btn btn-danger" onclick="deleteStudent(${student.id})">Delete</button>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    tbody.innerHTML = `<tr><td colspan="5" style="text-align: center;">No records found.</td></tr>`;
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Modal Controls
    function openAddModal() {
        document.getElementById('modal-title').innerText = "Add New Student";
        document.getElementById('student_id').value = '';
        document.getElementById('name').value = '';
        document.getElementById('email').value = '';
        document.getElementById('course').value = '';
        document.getElementById('studentModal').style.display = 'block';
    }

    function openEditModal(id, name, email, course) {
        document.getElementById('modal-title').innerText = "Edit Student Record";
        document.getElementById('student_id').value = id;
        document.getElementById('name').value = name;
        document.getElementById('email').value = email;
        document.getElementById('course').value = course;
        document.getElementById('studentModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('studentModal').style.display = 'none';
    }

    // 2. ADD & UPDATE Student using Fetch API (POST / PUT)
    function saveStudent() {
        const id = document.getElementById('student_id').value;
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const course = document.getElementById('course').value;

        if(!name || !email || !course) {
            alert("Please fill all fields!");
            return;
        }

        const method = id ? 'PUT' : 'POST';
        const payload = { id: id, name: name, email: email, course: course };

        fetch(apiUrl, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(result => {
            closeModal();
            showAlert(result.message, result.status ? 'success' : 'error');
            fetchStudents(); // Refresh table asynchronously
        })
        .catch(error => console.error('Error:', error));
    }

    // 3. DELETE Student using Fetch API (DELETE)
    function deleteStudent(id) {
        if(confirm("Are you sure you want to delete this student record?")) {
            fetch(apiUrl, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(result => {
                showAlert(result.message, result.status ? 'success' : 'error');
                fetchStudents(); // Refresh table asynchronously
            })
            .catch(error => console.error('Error:', error));
        }
    }
</script>

</body>
</html>