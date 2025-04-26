<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login_register.php");
    exit();
}

require_once 'payment.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$payment = new Payment();

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Handle file upload
            $target_dir = "uploads/payments/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_name = basename($_FILES["user_file"]["name"]);
            $target_file = $target_dir . time() . '_' . $file_name;
            
            if (move_uploaded_file($_FILES["user_file"]["tmp_name"], $target_file)) {
                // Set payment data
                $payment->booking_id = $_POST['booking_id'];
                $payment->payment_date = date('Y-m-d H:i:s');
                $payment->user_file = $target_file;
                $payment->payment_method = $_POST['payment_method'];
                $payment->amount = $_POST['amount'];
                
                if ($payment->add()) {
                    header("Location: UI_scadule.php?payment=success");
                } else {
                    header("Location: UI_formpayment.php?booking_id=".$_POST['booking_id']."&error=1");
                }
            } else {
                header("Location: UI_formpayment.php?booking_id=".$_POST['booking_id']."&error=file");
            }
        }
        break;
        
    default:
        header("Location: UI_scadule.php");
        break;
}