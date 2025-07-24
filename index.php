<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kabarak Hostel Booking</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f2f5;
      padding: 40px;
    }

    .form-container {
      max-width: 400px;
      margin: 0 auto;
      background: #e2dddd;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .form-container h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .form-container form {
      display: none;
      flex-direction: column;
      gap: 15px;
    }

    .form-container form.active {
      display: flex;
    }

    .role-toggle {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-bottom: 15px;
    }

    .form-container label {
      font-weight: bold;
    }

    .form-container input,
    .form-container select,
    .form-container button {
      padding: 10px;
      border: 1px solid #aaa;
      border-radius: 5px;
      font-size: 14px;
    }

    .form-container button {
      background: #007BFF;
      color: white;
      border: none;
      cursor: pointer;
    }

    .form-container button:hover {
      background: #0056b3;
    }

    .form-container a {
      text-align: center;
      display: block;
      margin-top: 10px;
      color: #007BFF;
      text-decoration: none;
    }

    .form-container a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="form-container">
  <h2>Kabarak Hostel Booking</h2>

  <!-- LOGIN FORM -->
  <form id="loginForm" class="active" method="POST" action="auth/login.php">
    <div class="role-toggle">
      <label><input type="radio" name="role" value="student" checked onclick="updateLoginLabel()"> Student</label>
      <label><input type="radio" name="role" value="admin" onclick="updateLoginLabel()"> Admin</label>
    </div>
    <label id="loginIdLabel">Registration Number</label>
    <input type="text" name="user_id_number" id="loginIdInput" placeholder="Registration Number" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Log in</button>
    <a href="#" onclick="showForm('register')">Don't have an account? Register here.</a>
  </form>


  <!-- REGISTER FORM -->
  <form id="registerForm" method="POST" action="auth/register.php">
    <div class="role-toggle">
      <label><input type="radio" name="role" value="student" checked onclick="updateRegisterLabel()"> Student</label>
      <label><input type="radio" name="role" value="admin" onclick="updateRegisterLabel()"> Admin</label>
    </div>
    <input type="text" name="name" placeholder="Full Name" required>
    <label id="regIdLabel">Registration Number</label>
    <input type="text" name="user_id_number" id="regIdInput" placeholder="Registration Number" required>
    <input type="email" name="email" placeholder="Email" required>
    <select name="gender" id="genderSelect" required>
      <option value="">Select Gender</option>
      <option>Male</option>
      <option>Female</option>
    </select>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
    <a href="#" onclick="showForm('login')">Already have an account? Log in here.</a>
  </form>
</div>

<script>
  function showForm(formType) {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    if (formType === 'register') {
      loginForm.classList.remove('active');
      registerForm.classList.add('active');
    } else {
      registerForm.classList.remove('active');
      loginForm.classList.add('active');
    }
  }

  function updateLoginLabel() {
    const role = document.querySelector('#loginForm input[name="role"]:checked').value;
    const label = document.getElementById('loginIdLabel');
    const input = document.getElementById('loginIdInput');

    if (role === 'admin') {
      label.textContent = 'Staff ID';
      input.placeholder = 'Staff ID';
    } else {
      label.textContent = 'Registration Number';
      input.placeholder = 'Registration Number';
    }
  }

  function updateRegisterLabel() {
    const role = document.querySelector('#registerForm input[name="role"]:checked').value;
    const label = document.getElementById('regIdLabel');
    const input = document.getElementById('regIdInput');
    const genderSelect = document.getElementById('genderSelect');

    if (role === 'admin') {
      label.textContent = 'Staff ID';
      input.placeholder = 'Staff ID';
      genderSelect.disabled = true;
    } else {
      label.textContent = 'Registration Number';
      input.placeholder = 'Registration Number';
      genderSelect.disabled = false;
    }
  }

  // Initialize toggle states
  updateLoginLabel();
  updateRegisterLabel();
</script>

</body>
</html>
