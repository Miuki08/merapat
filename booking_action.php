<?php

require_once 'booking.php';
$Booking = new booking();

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'add':
            if (isset($_POST['user_id'], $_POST['room_id'], $_POST['start_time'], $_POST['end_time'], $_POST['total_price'])) {
                $Booking->user_id = $_POST['user_id'];
                $Booking->room_id = $_POST['room_id'];
                $Booking->start_time = $_POST['start_time'];
                $Booking->end_time = $_POST['end_time'];
                $Booking->status = 'pending'; // Default status
                $Booking->total_price = $_POST['total_price'];
                $Booking->add();
        
                echo "<script>
                            alert('Booking berhasil dibuat!');
                            location.href = 'booking_view.php';
                        </script>";
                        
            } else {
                echo "<script>
                        alert('Data tidak lengkap');
                        location.href = 'UI_listpage.php';
                    </script>";
            }
            break;

        case 'delete':
            if (isset($_GET['booking_id'])) {
                $Booking->booking_id = $_GET['booking_id'];
                $Booking->delete();

                echo "<script>
                        alert('Booking berhasil dihapus!');
                        location.href = 'booking_view.php';
                    </script>";

            } else {
                echo "<script>
                        alert('Data tidak dapat dihapus');
                        location.href = 'booking_view.php';
                    </script>";
            }
            break;
        
        case 'update':
            if (isset($_POST['booking_id'], $_POST['room_id'], $_POST['user_id'], $_POST['start_time'], 
                $_POST['end_time'], $_POST['status'], $_POST['total_price'])) {
                
                $Booking->booking_id = $_POST['booking_id'];
                $Booking->room_id = $_POST['room_id'];
                $Booking->user_id = $_POST['user_id'];
                $Booking->start_time = $_POST['start_time'];
                $Booking->end_time = $_POST['end_time'];
                $Booking->status = $_POST['status'];
                $Booking->total_price = $_POST['total_price'];
                
                if ($Booking->update()) {
                    echo "<script>
                        alert('Booking berhasil diupdate!');
                        location.href = 'booking_view.php';
                    </script>";
                } else {
                    echo "<script>
                        alert('Periksa Data Kembali!');
                        location.href = 'booking_view.php';
                    </script>";
                }
                          
            } else {
                echo "<script>
                        alert('Data tidak lengkap');
                        location.href = 'booking_view.php';
                    </script>";
            }
            break;
        
            case 'booking':
                if (isset($_POST['user_id'], $_POST['room_id'], $_POST['start_time'], $_POST['end_time'])) {
                    $Booking->user_id = $_POST['user_id'];
                    $Booking->room_id = $_POST['room_id'];
                    $Booking->start_time = $_POST['start_time'];
                    $Booking->end_time = $_POST['end_time'];
                    $Booking->status = 'pending'; // Default status
                    $Booking->add();
            
                    echo "<script>
                                alert('Booking berhasil dibuat!');
                                location.href = 'UI_schadule.php';
                            </script>";
                            
                } else {
                    echo "<script>
                            alert('Data tidak lengkap');
                            location.href = 'UI_listpage.php';
                        </script>";
                } 
                break;
            
                case 'batal':
                    if (isset($_GET['booking_id'])) {
                        $Booking->booking_id = $_GET['booking_id'];
                        $Booking->delete();
        
                        echo "<script>
                                alert('Booking berhasil dihapus!');
                                location.href = 'UI_schadule.php';
                            </script>";
        
                    } else {
                        echo "<script>
                                alert('Data tidak dapat dihapus');
                                location.href = 'UI_schadule.php';
                            </script>";
                    }
                    break;
    }
} else {
    echo "<script>
            alert('Aksi tidak valid');
            location.href = 'booking_view.php';
        </script>";
    
}
?>