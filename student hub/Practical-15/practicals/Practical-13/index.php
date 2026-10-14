<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 13 - Student CRUD</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 25px;
            color: #1e293b;
        }
        .container { max-width: 1000px; margin: auto; }
        h1 { color: #1d4ed8; }
        .panel {
            background: #fff;
            padding: 22px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 3px 12px #0000000d;
        }
        label { display: block; font-weight: bold; margin-top: 8px; }
        input {
            width: 100%;
            padding: 11px;
            margin: 7px 0 13px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }
        button {
            padding: 10px 14px;
            margin: 3px;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
            color: white;
            background: #2563eb;
        }
        button:disabled { opacity: .6; cursor: not-allowed; }
        .delete { background: #dc2626; }
        .cancel { background: #64748b; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #eff6ff; }
        #message { margin: 12px 0; }
        .error { color: #b91c1c; }
        .success { color: #15803d; }
    </style>
</head>
<body>
<div class="container">
    <h1>Student Management System</h1>
    <p>Practical 13: PHP JSON API and Fetch-Based CRUD without Page Reload</p>

    <section class="panel">
        <h2 id="formTitle">Add Student</h2>
        <form id="studentForm">
            <input type="hidden" id="studentId">

            <label for="name">Student Name</label>
            <input id="name" name="name" maxlength="100" required>

            <label for="email">Email Address</label>
            <input id="email" name="email" type="email" maxlength="150" required>

            <label for="course">Course</label>
            <input id="course" name="course" maxlength="100" required>

            <button id="saveButton" type="submit">Add Student</button>
            <button type="button" class="cancel" id="cancelButton" hidden>Cancel Edit</button>
        </form>
        <p id="message" role="status" aria-live="polite"></p>
    </section>

    <section class="panel">
        <h2>Student Records</h2>
        <button type="button" id="refreshButton">Refresh List</button>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Actions</th>
                </tr>
                </thead>
                <tbody id="studentTable">
                    <tr><td colspan="5">Loading students...</td></tr>
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
const API = "api/students.php";

const form = document.getElementById("studentForm");
const table = document.getElementById("studentTable");
const message = document.getElementById("message");
const saveButton = document.getElementById("saveButton");
const cancelButton = document.getElementById("cancelButton");
const formTitle = document.getElementById("formTitle");

let students = [];
let busy = false;

function showMessage(text, isError = false) {
    message.textContent = text;
    message.className = isError ? "error" : "success";
}

async function request(method, data = null) {
    const options = {
        method,
        headers: { "Accept": "application/json" }
    };

    if (data !== null) {
        options.headers["Content-Type"] = "application/json";
        options.body = JSON.stringify(data);
    }

    const response = await fetch(API, options);
    let result;

    try {
        result = await response.json();
    } catch {
        throw new Error("The server returned an invalid JSON response.");
    }

    if (!response.ok || result.success !== true) {
        throw new Error(result.message || "Request failed.");
    }

    return result;
}

function renderStudents() {
    table.replaceChildren();

    if (students.length === 0) {
        const row = table.insertRow();
        const cell = row.insertCell();
        cell.colSpan = 5;
        cell.textContent = "No students found.";
        return;
    }

    students.forEach(student => {
        const row = table.insertRow();

        [student.id, student.name, student.email, student.course].forEach(value => {
            const cell = row.insertCell();
            cell.textContent = value;
        });

        const actions = row.insertCell();

        const editButton = document.createElement("button");
        editButton.type = "button";
        editButton.textContent = "Edit";
        editButton.addEventListener("click", () => editStudent(student));

        const deleteButton = document.createElement("button");
        deleteButton.type = "button";
        deleteButton.textContent = "Delete";
        deleteButton.className = "delete";
        deleteButton.addEventListener("click", () => deleteStudent(student.id));

        actions.append(editButton, deleteButton);
    });
}

async function loadStudents() {
    table.textContent = "Loading students...";

    try {
        const result = await request("GET");
        students = result.data;
        renderStudents();
    } catch (error) {
        table.replaceChildren();
        showMessage(error.message, true);
    }
}

function resetForm() {
    form.reset();
    document.getElementById("studentId").value = "";
    formTitle.textContent = "Add Student";
    saveButton.textContent = "Add Student";
    cancelButton.hidden = true;
}

function editStudent(student) {
    document.getElementById("studentId").value = student.id;
    document.getElementById("name").value = student.name;
    document.getElementById("email").value = student.email;
    document.getElementById("course").value = student.course;

    formTitle.textContent = "Update Student";
    saveButton.textContent = "Update Student";
    cancelButton.hidden = false;
    showMessage("Edit the details and click Update Student.");
    document.getElementById("name").focus();
}

form.addEventListener("submit", async event => {
    event.preventDefault();
    if (busy) return;

    if (!form.reportValidity()) return;

    const id = document.getElementById("studentId").value;
    const data = {
        name: document.getElementById("name").value.trim(),
        email: document.getElementById("email").value.trim(),
        course: document.getElementById("course").value.trim()
    };

    if (!data.name || !data.email || !data.course) {
        showMessage("Please fill in all fields.", true);
        return;
    }

    busy = true;
    saveButton.disabled = true;
    cancelButton.disabled = true;
    showMessage(id ? "Updating student..." : "Adding student...");

    try {
        const result = await request(
            id ? "PUT" : "POST",
            id ? { ...data, id: Number(id) } : data
        );

        resetForm();
        showMessage(result.message);
        await loadStudents();
    } catch (error) {
        showMessage(error.message, true);
    } finally {
        busy = false;
        saveButton.disabled = false;
        cancelButton.disabled = false;
    }
});

async function deleteStudent(id) {
    if (busy) return;
    if (!confirm("Are you sure you want to delete this student?")) return;

    busy = true;
    showMessage("Deleting student...");

    try {
        const result = await request("DELETE", { id: Number(id) });
        showMessage(result.message);
        await loadStudents();
    } catch (error) {
        showMessage(error.message, true);
    } finally {
        busy = false;
    }
}

cancelButton.addEventListener("click", resetForm);
document.getElementById("refreshButton").addEventListener("click", loadStudents);

loadStudents();
</script>
</body>
</html>
