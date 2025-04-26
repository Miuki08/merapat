<?php
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    echo "<script>alert('Anda telah berhasil logout.');</script>";
}
?>  

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="form_style.css" type="text/CSS">
    <link rel="shortcut icon" href="logoweb.png" type="image/x-icon">
    <title>Merapat ID</title>
</head>

<body>
    <button class="back-button" id="backButton">Kembali</button>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form action="users_action.php?action=register" method="post" id="signupForm">
                <h1>Create Account</h1>
                <span>Masukkan informasi berikut untuk membuat akun baru</span>
                <input type="text" placeholder="Name" name="name">
                <div class="error-message" id="nameError"></div>
                <input type="email" placeholder="Email" name="email">
                <div class="error-message" id="emailErrorUp"></div>
                <input type="password" placeholder="Password" name="password">
                <div class="error-message" id="passwordErrorUp"></div>
                <button type="submit">Sign Up</button>
            </form>
        </div>
        <div class="form-container sign-in">
            <form action="users_action.php?action=login" method="post" name="f" id="signinForm">
                <h1>Sign In</h1>
                <span>Masukkkan informasi berikut untuk masuk ke akun</span>
                <input type="email" placeholder="Email" name="email">
                <div class="error-message" id="emailErrorIn"></div>
                <input type="password" placeholder="Password" name="password">
                <div class="error-message" id="passwordErrorIn"></div>
                <button type="submit">Sign In</button>
            </form>
            <p id="statusSignIn" style="color: rgb(255, 81, 0); font-style: italic; margin: 5px 0 5px 0;"></p>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>Enter your personal details to use all of site features</p>
                    <button class="hidden" id="login">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Lets join together!</h1>
                    <p>Register with your personal details to use all of site features</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validasi Form
        const validateForm = (formType) => {
            let isValid = true;
            const inputs = document.querySelectorAll(`#${formType} input`);
            
            inputs.forEach(input => {
                const errorDiv = document.getElementById(`${input.name}Error${formType === 'signupForm' ? 'Up' : 'In'}`);
                if (!input.value.trim()) {
                    input.classList.add('input-error');
                    errorDiv.textContent = 'Field ini harus diisi';
                    errorDiv.style.display = 'block';
                    isValid = false;
                } else {
                    input.classList.remove('input-error');
                    errorDiv.style.display = 'none';
                }
            });

            return isValid;
        };

        // Event Listeners untuk Form
        document.getElementById('signupForm').addEventListener('submit', (e) => {
            if (!validateForm('signupForm')) {
                e.preventDefault();
            }
        });

        document.getElementById('signinForm').addEventListener('submit', (e) => {
            if (!validateForm('signinForm')) {
                e.preventDefault();
            }
        });

        // Toggle Container
        const container = document.getElementById('container');
        const registerBtn = document.getElementById('register');
        const loginBtn = document.getElementById('login');
        const backButton = document.getElementById('backButton');

        registerBtn.addEventListener('click', () => {
            container.classList.add("active");
        });

        loginBtn.addEventListener('click', () => {
            container.classList.remove("active");
        });

        backButton.addEventListener('click', () => {
            window.history.back();
        });

        // Real-time Validation
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', () => {
                const formType = input.closest('form').id;
                const errorDiv = document.getElementById(`${input.name}Error${formType === 'signupForm' ? 'Up' : 'In'}`);
                
                if (!input.value.trim()) {
                    input.classList.add('input-error');
                    errorDiv.textContent = 'Field ini harus diisi';
                    errorDiv.style.display = 'block';
                } else {
                    input.classList.remove('input-error');
                    errorDiv.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>