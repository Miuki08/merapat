<?php 

require_once 'users.php';
$Users = new Users;

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'add':
            if (isset($_POST['name'], $_POST['email'], $_POST['password'])) {
                $Users->name = $_POST['name'];
                $Users->email = $_POST['email'];
                $Users->password = $_POST['password'];
                $Users->role = isset($_POST['role']) && !empty($_POST['role']) ? $_POST['role'] : 'customer';
                $Users->add();

                echo "<script>
                            alert('Data Berhasil Diunggah');
                            location.href = 'user_view.php';
                          </script>";
            } else {
                echo "<script>
                        alert('Data tidak lengkap');
                        location.href = 'user_view.php';
                      </script>";
            }
            break;

        case 'delete':
            if (isset($_GET['user_id'])) {
                    $Users->user_id = $_GET['user_id'];
                    $Users->delete();
    
                    echo "<script>
                        alert('Data Berhasil Dihapus');
                        location.href = 'user_view.php';
                    </script>";
            } else {
                echo "<script>
                        alert('Data Tidak dapat Dihapus');
                        location.href = 'user_view.php';
                    </script>";
            }
            break;
        
        case 'update':
            if (isset($_POST['user_id'], $_POST['name'], $_POST['email'], $_POST['password'], $_POST['role'])) {
                $Users->user_id = $_POST['user_id'];
                $Users->name = $_POST['name'];
                $Users->email = $_POST['email'];
                $Users->password = $_POST['password'];
                $Users->role = $_POST['role'];
                $Users->update();
            
                echo "<script>
                        alert('Data Berhasil Diupdate');
                        location.href = 'user_view.php';
                    </script>";
            } else {
                echo "<script>
                        alert('Data tidak lengkap');
                        location.href = 'user_view.php';
                    </script>";
            }
        break;

        case 'register':
            if (isset($_POST['name'], $_POST['email'], $_POST['password'])) {
                $Users->name = $_POST['name'];
                $Users->email = $_POST['email'];
                $Users->password = $_POST['password'];
                $Users->role = isset($_POST['role']) && !empty($_POST['role']) ? $_POST['role'] : 'customer';
                $Users->add();

                echo "<script>
                            alert('Kamu Berhasil Register, Silahkan Login');
                            location.href = 'User_login_register.php';
                          </script>";
            } else {
                echo "<script>
                            alert('Data tidak lengkap');
                            location.href = 'User_login_register.php';
                          </script>";
            }
            break;

        case 'login':
            if (isset($_POST['email'], $_POST['password'])) {
                $email = $_POST['email'];
                $password = $_POST['password'];
        
                // Panggil method login dari class Users
                $user = $Users->login($email, $password);
        
                if ($user) {
                    // Login berhasil, set session
                    session_start();
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['created_at'] = $user['created_at'];
                    $_SESSION['role'] = $user['role'];
        
                    // Redirect ke dashboard berdasarkan role
                    if ($user['role'] == 'manager' || $user['role'] == 'officer') {
                        // Manager dan Officer diarahkan ke staff_dashboard.php
                        header("Location: dasboard.php");
                    } elseif ($user['role'] == 'customer') {
                        // Customer diarahkan ke customer_dashboard.php
                        header("Location: UI_Home.php");
                    } else {
                        // Role tidak dikenali, redirect ke halaman login
                        echo "<script>
                                alert('Role tidak valid');
                                location.href = 'user_login_register.php';
                              </script>";
                    }
                    exit();
                } else {
                    // Login gagal, tampilkan pesan error
                    echo "<script>
                            alert('Email atau password salah');
                            location.href = 'user_login_register.php';
                          </script>";
                }
            } else {
                echo "<script>
                        alert('Data tidak lengkap');
                        location.href = 'user_login_register.php';
                      </script>";
            }
        break;
    }
} else {
    echo "<script>
            alert('Aksi tidak valid');
            location.href = 'user_view.php';
          </script>";
}
?>