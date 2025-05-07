<?php 
require_once 'review.php';
$review = new Review();

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'add':
            if (isset($_POST['user_id'], $_POST['room_id'], $_POST['rating'], $_POST['review_text'])) {
                $review->user_id = $_POST['user_id'];
                $review->room_id = $_POST['room_id'];
                $review->rating = $_POST['rating'];
                $review->review_text = $_POST['review_text'];
                
                // Response is optional, so check if it's set
                $review->response = isset($_POST['response']) ? $_POST['response'] : null;
                
                if ($review->add()) {
                    echo "<script>
                            alert('Review Berhasil Ditambahkan');
                            location.href = 'UI_formbooking.php';
                        </script>";
                } else {
                    echo "<script>
                            alert('Gagal Menambahkan Review');
                            location.href = 'UI_formbooking.php';
                        </script>";
                }
            } else {
                echo "<script>
                        alert('Data tidak lengkap');
                        location.href = 'UI_formbooking.php';
                    </script>";
            }
            break;
        
        case 'delete':
            if (isset($_GET['review_id'])) {
                $review->review_id = $_GET['review_id'];
                if ($review->delete()) {
                    echo "<script>
                            alert('Review Berhasil Dihapus');
                            location.href = 'UI_formbooking.php';
                        </script>";
                } else {
                    echo "<script>
                            alert('Gagal Menghapus Review');
                            location.href = 'UI_formbooking.php';
                        </script>";
                }
            } else {
                echo "<script>
                        alert('ID Review tidak valid');
                        location.href = 'UI_formbooking.php';
                    </script>";
            }
            break;
        
        case 'update':
            if (isset($_POST['review_id'], $_POST['user_id'], $_POST['room_id'], $_POST['rating'], $_POST['review_text'])) {
                $review->review_id = $_POST['review_id'];
                $review->user_id = $_POST['user_id'];
                $review->room_id = $_POST['room_id'];
                $review->rating = $_POST['rating'];
                $review->review_text = $_POST['review_text'];
                $review->response = isset($_POST['response']) ? $_POST['response'] : null;
                
                if ($review->update()) {
                    echo "<script>
                            alert('Review Berhasil Diperbarui');
                            location.href = 'UI_formbooking.php';
                          </script>";
                } else {
                    echo "<script>
                            alert('Gagal Memperbarui Review');
                            location.href = 'UI_formbooking.php';
                          </script>";
                }
            } else {
                echo "<script>
                        alert('Data tidak lengkap');
                        location.href = 'UI_formbooking.php';
                      </script>";
            }
            break;

        case 'add_response':
            if (isset($_POST['review_id'], $_POST['response'])) {
                $review->review_id = $_POST['review_id'];
                $review->response = $_POST['response'];
                
                if ($review->addResponse()) {
                    echo "<script>
                            alert('Respon Berhasil Ditambahkan');
                            location.href = 'review_view.php';
                          </script>";
                } else {
                    echo "<script>
                            alert('Gagal Menambahkan Respon');
                            location.href = 'review_view.php';
                          </script>";
                }
            } else {
                echo "<script>
                        alert('Data tidak lengkap');
                        location.href = 'review_view.php';
                      </script>";
            }
            break;
    }
} else {
    echo "<script>
            alert('Aksi tidak valid');
            location.href = 'review_view.php';
          </script>";
}
?>