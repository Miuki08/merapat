<?php 

require_once 'meeting_admin.php';
$meeting = new Meet;

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'add':
            if (isset($_POST['room_name'], $_POST['location'], $_POST['capacity'], $_POST['facilities'], $_POST['status'], $_POST['total_price'])) {
                $meeting->room_name = $_POST['room_name'];
                $meeting->location = $_POST['location'];
                $meeting->capacity = $_POST['capacity'];
                $meeting->facilities = $_POST['facilities'];
                $meeting->status = $_POST['status'];
                $meeting->total_price = $_POST['total_price'];
                $meeting->add();

                echo "<script>
                        alert('Data Berhasil Diunggah');
                        location.href = 'meet_min_view.php';
                    </script>";
            } else {
                echo "<script>
                        alert('Data tidak lengkap');
                        location.href = 'meet_min_view.php';
                    </script>";
            }
            break;
        
        case 'delete':
            if (isset($_GET['room_id'])) {
                $meeting->room_id = $_GET['room_id'];
                $meeting->delete();

                echo "<script>
                        alert('Data Berhasil Dihapus');
                        location.href = 'meet_min_view.php';
                    </script>";
            } else {
                echo "<script>
                        alert('Data Tidak dapat Dihapus');
                        location.href = 'meet_min_view.php';
                    </script>";
            }
            break;
        
        case 'update':
            if (isset($_POST['room_id'], $_POST['room_name'], $_POST['location'], $_POST['capacity'], $_POST['facilities'], $_POST['status'], $_POST['total_price'])) {
                $meeting->room_id = $_POST['room_id'];
                $meeting->room_name = $_POST['room_name'];
                $meeting->location = $_POST['location'];
                $meeting->capacity = $_POST['capacity'];
                $meeting->facilities = $_POST['facilities'];
                $meeting->status = $_POST['status'];
                $meeting->total_price = $_POST['total_price'];
                $meeting->update();

                echo "<script>
                        alert('Data Berhasil Diupdate');
                        location.href = 'meet_min_view.php';
                      </script>";
            } else {
                echo "<script>
                        alert('Data tidak lengkap');
                        location.href = 'meet_min_view.php';
                      </script>";
            }
            break;
    }
} else {
    echo "<script>
            alert('Aksi tidak valid');
            location.href = 'meet_min_view.php';
          </script>";
}
?>