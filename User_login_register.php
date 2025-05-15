<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="logoweb.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Cinzel+Decorative:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Merapat ID</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body{
    background-color: #F1E9E9;
    background: linear-gradient(135deg, #4A002A, #6a1b9a);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    height: 100vh;
    position: relative;
    overflow: hidden;
}

/* Corner Patterns */
body::before,
body::after {
    content: "";
    position: absolute;
    width: 200px;
    height: 200px;
    background-image: radial-gradient(circle, rgba(194, 174, 109, 0.2) 2px, transparent 3px);
    background-size: 20px 20px;
    z-index: -1;
}

body::before {
    top: -50px;
    left: -50px;
    transform: rotate(45deg);
}

body::after {
    bottom: -50px;
    right: -50px;
    transform: rotate(45deg);
}

/* Additional corner decorations */
.corner-decoration {
    position: absolute;
    width: 100px;
    height: 100px;
    border: 2px solid rgba(194, 174, 109, 0.3);
    z-index: -1;
}

.corner-decoration.top-left {
    top: 20px;
    left: 20px;
    border-right: none;
    border-bottom: none;
    border-radius: 15px 0 0 0;
}

.corner-decoration.top-right {
    top: 20px;
    right: 20px;
    border-left: none;
    border-bottom: none;
    border-radius: 0 15px 0 0;
}

.corner-decoration.bottom-left {
    bottom: 20px;
    left: 20px;
    border-right: none;
    border-top: none;
    border-radius: 0 0 0 15px;
}

.corner-decoration.bottom-right {
    bottom: 20px;
    right: 20px;
    border-left: none;
    border-top: none;
    border-radius: 0 0 15px 0;
}

/* Ornamental corner elements */
.ornament {
    position: absolute;
    width: 30px;
    height: 30px;
    background: transparent;
    z-index: -1;
}

.ornament::before,
.ornament::after {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    border: 2px solid rgba(194, 174, 109, 0.4);
    border-radius: 50%;
}

.ornament.top-left {
    top: 50px;
    left: 50px;
}

.ornament.top-right {
    top: 50px;
    right: 50px;
}

.ornament.bottom-left {
    bottom: 50px;
    left: 50px;
}

.ornament.bottom-right {
    bottom: 50px;
    right: 50px;
}

.ornament.top-left::before {
    transform: rotate(45deg);
}

.ornament.top-right::after {
    transform: rotate(-45deg);
}

.ornament.bottom-left::before {
    transform: rotate(-45deg);
}

.ornament.bottom-right::after {
    transform: rotate(45deg);
}

.back-button {
    position: absolute;
    top: 20px;
    left: 20px;
    background-color: rgba(74, 0, 42, 0.7);
    color: white;
    padding: 10px 20px;
    border-radius: 30px;
    cursor: pointer;
    font-size: 1em;
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
    font-weight: 500;
    border: 2px solid #C2AE6D;
    z-index: 10;
    text-decoration: none;
}

.back-button:hover {
    background-color: rgba(74, 0, 42, 0.9);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.container{
    background-color: #fff;
    border-radius: 30px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.35);
    position: relative;
    overflow: hidden;
    width: 768px;
    max-width: 100%;
    min-height: 480px;
}

.container p{
    font-size: 14px;
    line-height: 20px;
    letter-spacing: 0.3px;
    margin: 20px 0;
    font-family: 'Poppins', sans-serif;
}

.container span{
    margin-top: 10px;
    text-align: center;
    font-size: 12px;
    font-family: 'Poppins', sans-serif;
}

.container a{
    color: #4A002A;
    font-size: 13px;
    text-decoration: none;
    margin: 15px 0 10px;
    font-family: 'Poppins', sans-serif;
}

.container button{
    background-color: #4A002A;
    color: #fff;
    font-size: 12px;
    padding: 10px 45px;
    border: 1px solid transparent;
    border-radius: 30px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-top: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
}

.container button:hover {
    background-color: #6a1b9a;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.container button.hidden{
    background-color: transparent;
    border-color: #fff;
}

.container form{
    background-color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding: 0 40px;
    height: 100%;
}

.container input{
    background-color: #eee;
    border: none;
    margin: 8px 0;
    padding: 10px 15px;
    font-size: 13px;
    border-radius: 8px;
    width: 100%;
    outline: none;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease;
}

.form-container{
    position: absolute;
    top: 0;
    height: 100%;
    transition: all 0.6s ease-in-out;
}

.sign-in{
    left: 0;
    width: 50%;
    z-index: 2;
}

.container.active .sign-in{
    transform: translateX(100%);
}

.sign-up{
    left: 0;
    width: 50%;
    opacity: 0;
    z-index: 1;
}

.container.active .sign-up{
    transform: translateX(100%);
    opacity: 1;
    z-index: 5;
    animation: move 0.6s;
}

@keyframes move{
    0%, 49.99%{
        opacity: 0;
        z-index: 1;
    }
    50%, 100%{
        opacity: 1;
        z-index: 5;
    }
}

.social-icons{
    margin: 20px 0;
}

.social-icons a{
    border: 1px solid #ccc;
    border-radius: 20%;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    margin: 0 3px;
    width: 40px;
    height: 40px;
}

.toggle-container{
    position: absolute;
    top: 0;
    left: 50%;
    width: 50%;
    height: 100%;
    overflow: hidden;
    transition: all 0.6s ease-in-out;
    border-radius: 150px 0 0 100px;
    z-index: 1000;
}

.container.active .toggle-container{
    transform: translateX(-100%);
    border-radius: 0 150px 100px 0;
}

.toggle{
    background-color: #4A002A;
    height: 100%;
    background: linear-gradient(to right, #4A002A, #6a1b9a);
    color: #fff;
    position: relative;
    left: -100%;
    height: 100%;
    width: 200%;
    transform: translateX(0);
    transition: all 0.6s ease-in-out;
}

.container.active .toggle{
    transform: translateX(50%);
}

.toggle-panel{
    position: absolute;
    width: 50%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding: 0 30px;
    text-align: center;
    top: 0;
    transform: translateX(0);
    transition: all 0.6s ease-in-out;
}

.toggle-left{
    transform: translateX(-200%);
}

.container.active .toggle-left{
    transform: translateX(0);
}

.toggle-right{
    right: 0;
    transform: translateX(0);
}

.container.active .toggle-right{
    transform: translateX(200%);
}

/* Tambahan Styling */
.form-container input {
    transition: all 0.3s ease;
}

.form-container input:focus {
    border-color: #C2AE6D;
    box-shadow: 0 0 5px rgba(194, 174, 109, 0.3);
}

.form-container button:hover {
    transform: scale(1.02);
    background: #6a1b9a;
}

.input-error {
    border-color: #ff0000 !important;
}

.error-message {
    color: #ff0000;
    font-size: 12px;
    margin-top: -8px;
    margin-bottom: 10px;
    display: none;
    font-family: 'Poppins', sans-serif;
}

h1 {
    font-family: 'Cinzel Decorative', serif;
    color: #4A002A;
}
    </style>
</head>

<body>
    <!-- Corner Decorations -->
    <div class="corner-decoration top-left"></div>
    <div class="corner-decoration top-right"></div>
    <div class="corner-decoration bottom-left"></div>
    <div class="corner-decoration bottom-right"></div>
    
    <!-- Ornamental Elements -->
    <div class="ornament top-left"></div>
    <div class="ornament top-right"></div>
    <div class="ornament bottom-left"></div>
    <div class="ornament bottom-right"></div>
    
    <a href="index.html" class="back-button"><i class="fas fa-arrow-left"></i> Kembali</a>
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
                <button type="submit"><i class="fas fa-user-plus"></i> Sign Up</button>
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
                <button type="submit"><i class="fas fa-sign-in-alt"></i> Sign In</button>
            </form>
            <p id="statusSignIn" style="color: #C2AE6D; font-style: italic; margin: 5px 0 5px 0; font-family: 'Poppins', sans-serif;"></p>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>Enter your personal details to use all of site features</p>
                    <button class="hidden" id="login"><i class="fas fa-sign-in-alt"></i> Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Let's join together!</h1>
                    <p>Register with your personal details to use all of site features</p>
                    <button class="hidden" id="register"><i class="fas fa-user-plus"></i> Sign Up</button>
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