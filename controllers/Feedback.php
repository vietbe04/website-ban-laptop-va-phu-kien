<?php
class Feedback extends Controller {
    /** User: show submit form */
    public function create(){
        // Require login to submit feedback
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['user'])){ header('Location: '.APP_URL.'/AuthController/ShowLogin?next=feedback'); exit(); }
        $this->view('homePage', ['page'=>'FeedbackFormView']);
    }

    /** User: handle submit */
    public function store(){
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['user'])){ header('Location: '.APP_URL.'/AuthController/ShowLogin?next=feedback'); exit(); }
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){ header('Location: '.APP_URL.'/Feedback/create'); exit(); }
        $email = $_SESSION['user']['email'];
        $fullname = $_SESSION['user']['fullname'] ?? $email;
        $subject = trim($_POST['subject'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if($content === ''){
            $_SESSION['flash_message'] = 'Vui lòng nhập nội dung góp ý.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: '.APP_URL.'/Feedback/create'); exit();
        }
        $m = $this->model('FeedbackModel');
        $ok = $m->create(['user_email'=>$email,'fullname'=>$fullname,'subject'=>$subject,'content'=>$content]);
        $_SESSION['flash_message'] = $ok ? 'Đã gửi góp ý. Cảm ơn bạn!' : 'Không thể gửi góp ý.';
        $_SESSION['flash_type'] = $ok ? 'success' : 'danger';
        header('Location: '.APP_URL.'/Feedback/my'); exit();
    }

    /** User: list my feedbacks */
    public function my(){
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['user'])){ header('Location: '.APP_URL.'/AuthController/ShowLogin?next=feedback'); exit(); }
        $email = $_SESSION['user']['email'];
        $itemsPerPage = 15;
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($currentPage - 1) * $itemsPerPage;
        $m = $this->model('FeedbackModel');
        $total = $m->countByEmail($email);
        $list = $m->listByEmail($email, $itemsPerPage, $offset);
        $totalPages = max(1, ceil($total / $itemsPerPage));
        $this->view('homePage', ['page'=>'MyFeedbackListView','feedbacks'=>$list,'currentPage'=>$currentPage,'totalPages'=>$totalPages,'total'=>$total]);
    }

    /** Admin: feedback management list */
    public function adminIndex(){
        $this->requireRole(['admin','staff'], 'admin-feedback');
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'status' => trim($_GET['status'] ?? '')
        ];
        $itemsPerPage = 15;
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($currentPage - 1) * $itemsPerPage;
        $m = $this->model('FeedbackModel');
        $total = $m->countAdminSearch($filters);
        $list = $m->adminSearch($filters, $itemsPerPage, $offset);
        $totalPages = max(1, ceil($total / $itemsPerPage));
        $this->view('adminPage', ['page'=>'FeedbackAdminView','feedbacks'=>$list,'filters'=>$filters,'currentPage'=>$currentPage,'totalPages'=>$totalPages,'total'=>$total]);
    }

    /** Admin: reply to feedback */
    public function reply(){
        $this->requireRole(['admin','staff'], 'admin-feedback');
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){ header('Location: '.APP_URL.'/Feedback/adminIndex'); exit(); }
        $id = (int)($_POST['id'] ?? 0);
        $reply = trim($_POST['reply'] ?? '');
        $status = (int)($_POST['status'] ?? 1);
        $m = $this->model('FeedbackModel');
        $m->reply($id, $reply, $status);

        // If admin requested notification, send email to the user; otherwise skip sending
        $notify = isset($_POST['notify']) && $_POST['notify'] == '1';
        if ($notify) {
            $row = $m->findById($id);
            if ($row && !empty($row['user_email'])) {
                require_once 'vendor/autoload.php';
                $to = $row['user_email'];
                $subject = 'Phản hồi góp ý từ hệ thống';
                $title = htmlspecialchars($row['subject'] ?? 'Góp ý của bạn', ENT_QUOTES, 'UTF-8');
                $body = "<p>Xin chào,</p>"
                    . "<p>Góp ý của bạn: <strong>" . $title . "</strong></p>"
                    . "<p>Nội dung góp ý:</p><blockquote style='border-left:4px solid #ddd;padding-left:8px;'>" . nl2br(htmlspecialchars($row['content'] ?? '', ENT_QUOTES, 'UTF-8')) . "</blockquote>"
                    . "<p>Phản hồi từ quản trị viên:</p><div style='background:#f8f9fa;padding:10px;border-radius:6px;'>" . nl2br(htmlspecialchars($reply, ENT_QUOTES, 'UTF-8')) . "</div>"
                    . "<p>Trân trọng,</p><p>Hệ thống hỗ trợ khách hàng</p>";
                try {
                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'nttv9604@gmail.com';
                    $mail->Password = 'ryae yfan rkle pelu';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';

                    $mail->setFrom('nttv9604@gmail.com', 'Hỗ trợ hệ thống');
                    $mail->addAddress($to);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body = $body;
                    $mail->AltBody = strip_tags("Phản hồi cho góp ý: " . ($row['subject'] ?? '') . "\n" . $reply);
                    $mail->send();
                } catch (Exception $e) {
                    error_log('Gửi email phản hồi góp ý thất bại: ' . $e->getMessage());
                }
            }
        }
        header('Location: '.APP_URL.'/Feedback/adminIndex'); exit();
    }

    /** Admin: delete feedback */
    public function delete(){
        $this->requireRole(['admin'], 'admin-feedback');
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){ header('Location: '.APP_URL.'/Feedback/adminIndex'); exit(); }
        $id = (int)($_POST['id'] ?? 0);
        $m = $this->model('FeedbackModel');
        $m->remove($id);
        header('Location: '.APP_URL.'/Feedback/adminIndex'); exit();
    }
}
?>