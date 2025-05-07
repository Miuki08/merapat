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
            $penyimpan = "uploads/payments/";
            if (!file_exists($penyimpan)) {
                mkdir($penyimpan, 0777, true);
            }
            
            $nama_file = basename($_FILES["user_file"]["name"]);
            $target_file = $penyimpan . time() . '_' . $nama_file;
            
            if (move_uploaded_file($_FILES["user_file"]["tmp_name"], $target_file)) {
                // Set payment data
                $payment->booking_id = $_POST['booking_id'];
                $payment->payment_date = date('Y-m-d H:i:s');
                $payment->user_file = $target_file;
                $payment->payment_method = $_POST['payment_method'];
                $payment->status = 'pending'; // Default status
                $payment->amount = $_POST['amount'];
                
                if ($payment->add()) {
                    header("Location: UI_schadule.php?payment=success");
                } else {
                    header("Location: UI_formpayment.php?booking_id=".$_POST['booking_id']."&error=1");
                }
            } else {
                header("Location: UI_formpayment.php?booking_id=".$_POST['booking_id']."&error=file");
            }
        }
        break;
        
    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Ambil data pembayaran yang akan diedit
            $payment_id = $_POST['payment_id'];
            $existing_payment = $payment->getPaymentId($payment_id);
            
            // Handle file upload jika ada file baru
            $target_file = $existing_payment['user_file'];
            
            if (!empty($_FILES["user_file"]["name"])) {
                $penyimpan = "uploads/payments/";
                $nama_file = basename($_FILES["user_file"]["name"]);
                $new_target_file = $penyimpan . time() . '_' . $nama_file;
                
                if (move_uploaded_file($_FILES["user_file"]["tmp_name"], $new_target_file)) {
                    // Hapus file lama jika ada
                    if (file_exists($target_file)) {
                        unlink($target_file);
                    }
                    $target_file = $new_target_file;
                } else {
                    header("Location: UI_editpayment.php?payment_id=".$payment_id."&error=file");
                    exit();
                }
            }
            
            // Update data pembayaran
            $payment->payment_id = $payment_id;
            $payment->booking_id = $_POST['booking_id'];
            $payment->payment_date = $_POST['payment_date'];
            $payment->user_file = $target_file;
            $payment->payment_method = $_POST['payment_method'];
            $payment->amount = $_POST['amount'];
            
            if ($payment->update()) {
                header("Location: payment_view.php?payment=updated");
            } else {
                header("Location: UI_editpayment.php?payment_id=".$payment_id."&error=1");
            }
        }
        break;
        
    case 'delete':
        if (isset($_GET['payment_id'])) {
            $payment_id = $_GET['payment_id'];
            $payment_data = $payment->getPaymentId($payment_id);
            
            // Hapus file terkait jika ada
            if (!empty($payment_data['user_file']) && file_exists($payment_data['user_file'])) {
                unlink($payment_data['user_file']);
            }
            
            if ($payment->delete($payment_id)) {
                header("Location: payment_view.php?payment=deleted");
            } else {
                header("Location: payment_view.php?error=delete_failed");
            }
        }
        break;
        
    default:
        header("Location: UI_schadule.php");
        break;
}