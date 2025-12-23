<?php
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Controller khu vực quản trị tổng hợp
 * - Trang chính, báo cáo, khách hàng, thêm tài khoản, và cấu hình ngưỡng giảm giá
 */
class Admin extends Controller{
    /**
     * Trang chính admin: hiển thị danh sách loại sản phẩm
     */
    function show(){
        $this->requireRole(['admin','staff'], 'admin');
        $obj=$this->model("AdProductTypeModel");
        $data=$obj->all("tblloaisp");
        $this->view("adminPage",["page"=>"ProductTypeView","productList"=>$data]);
    }

    /**
     * Báo cáo doanh thu (lọc theo ngày/tháng/năm) hiển thị trong layout admin
     */
    function report(){
        $this->requireRole(['admin'], 'admin-report');
        $report = $this->model('ReportModel');
        $type = $_GET['type'] ?? 'day';
        $date = $_GET['date'] ?? date('Y-m-d');
        $year = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? date('m');

        // Tự xác định khoảng tổng hợp để hiển thị danh sách
        if($type==='day'){
            $rangeStart = date('Y-m-01', strtotime($date));
            $rangeEnd   = date('Y-m-t', strtotime($date));
        } elseif ($type==='month') {
            $rangeStart = $year.'-01-01';
            $rangeEnd   = $year.'-12-31';
        } else { // year
            $rangeStart = $year.'-01-01';
            $rangeEnd   = $year.'-12-31';
        }

        $result = [];
        $single = null;
        try{
            switch ($type){
                case 'day':
                    $single = $report->getRevenueByDay($date);
                    $result = $report->getRevenueRangeGrouped('day', $rangeStart, $rangeEnd);
                    break;
                case 'month':
                    $single = $report->getRevenueByMonth($year, $month);
                    $result = $report->getRevenueRangeGrouped('month', $rangeStart, $rangeEnd);
                    break;
                case 'year':
                    $singleList = $report->getRevenueByYear($year);
                    $single = [
                        'period' => $year,
                        'revenue' => array_sum(array_column($singleList,'revenue')),
                        'profit' => array_sum(array_column($singleList,'profit')),
                    ];
                    $result = $report->getRevenueRangeGrouped('month', $rangeStart, $rangeEnd); // hiển thị theo tháng
                    break;
            }
        } catch (Exception $e){
            $error = $e->getMessage();
        }

        $totalAllTime = $report->getTotalRevenueAllTime();
        $viewData = [
            'type'=>$type,
            'date'=>$date,
            'year'=>$year,
            'month'=>$month,
            'single'=>$single,
            'list'=>$result,
            'totalAllTime'=>$totalAllTime,
            'error'=>$error ?? null,
        ];
        $this->view('adminPage', ['page'=>'RevenueReportView'] + $viewData);
    }

    /**
     * Quản lý khách hàng: danh sách có tìm kiếm và phân trang
     */
    function customers(){
        $this->requireRole(['admin'], 'admin-customers');
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 15;
        $offset = ($page - 1) * $perPage;
        
        $userModel = $this->model('UserModel');
        $total = $userModel->countWithStats($q);
        $list = $userModel->listWithStats($q, $perPage, $offset);
        
        $totalPages = ceil($total / $perPage);
        
        $this->view('adminPage', [
            'page'=>'AccountManagerView',
            'customerList'=>$list,
            'q'=>$q,
            'currentPage'=>$page,
            'totalPages'=>$totalPages,
            'total'=>$total
        ]);
    }
    /**
     * Chỉnh sửa thông tin khách hàng (form)
     * @param int|string $id
     */
    function customerEdit($id){
        $this->requireRole(['admin'], 'admin-customers');
        $userModel = $this->model('UserModel');
        $item = $userModel->getByIdWithStats($id);
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $list = $userModel->listWithStats($q);
        $this->view('adminPage', ['page'=>'AccountManagerView','customerList'=>$list,'editItem'=>$item,'q'=>$q]);
    }
    /**
     * Cập nhật thông tin khách hàng
     */
    function updateCustomer(){
        $this->requireRole(['admin'], 'admin-customers');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: '.APP_URL.'/Admin/customers'); exit(); }
        $id = $_POST['user_id'] ?? null;
        $role = isset($_POST['role']) ? trim($_POST['role']) : null;
        if (!$id) { header('Location: '.APP_URL.'/Admin/customers'); exit(); }
        // Do NOT trust or accept fullname/email changes from this admin UI.
        // Fetch current fullname/email from DB and only allow role update here.
        $userModel = $this->model('UserModel');
        $current = $userModel->getByIdWithStats($id);
        if (!$current) { header('Location: '.APP_URL.'/Admin/customers'); exit(); }
        $fullname = $current['fullname'] ?? '';
        $email = $current['email'] ?? '';
        // Validate role nếu truyền: chỉ chấp nhận user|staff|admin
        if ($role !== null && !in_array($role, ['user','staff','admin'])) { $role = null; }
        // Only update with the existing fullname/email and the new role (if provided)
        $userModel->update($id, $fullname, $email, $role);
        header('Location: '.APP_URL.'/Admin/customers');
        exit();
    }
    /**
     * Xóa khách hàng theo id
     * @param int|string $id
     */
    function deleteCustomer($id){
        $this->requireRole(['admin'], 'admin-customers');
        $userModel = $this->model('UserModel');
        $userModel->deleteById($id);
        header('Location: '.APP_URL.'/Admin/customers');
        exit();
    }

    /**
     * Khóa tài khoản khách hàng theo id
     * @param int|string $id
     */
    function lockCustomer($id){
        $this->requireRole(['admin'], 'admin-customers');
        $userModel = $this->model('UserModel');
        $userModel->lockAccount($id);
        $_SESSION['flash_message'] = 'Đã khóa tài khoản thành công';
        $_SESSION['flash_type'] = 'success';
        header('Location: '.APP_URL.'/Admin/customers');
        exit();
    }

    /**
     * Mở khóa tài khoản khách hàng theo id
     * @param int|string $id
     */
    function unlockCustomer($id){
        $this->requireRole(['admin'], 'admin-customers');
        $userModel = $this->model('UserModel');
        $userModel->unlockAccount($id);
        $_SESSION['flash_message'] = 'Đã mở khóa tài khoản thành công';
        $_SESSION['flash_type'] = 'success';
        header('Location: '.APP_URL.'/Admin/customers');
        exit();
    }

    /**
     * Thêm tài khoản mới (gửi OTP xác thực qua email trước khi tạo)
     */
    function addAccount(){
        $this->requireRole(['admin'], 'admin-add-account');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';
            
            // Validation
            $errors = [];
            
            if (empty($fullname)) {
                $errors[] = 'Họ tên không được để trống';
            }
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ';
            }
            
            if (empty($password) || strlen($password) < 6) {
                $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
            }
            
            // Validate role
            $validRoles = ['user', 'staff', 'admin'];
            if (!in_array($role, $validRoles)) {
                $role = 'user'; // Default to user if invalid role
            }
            
            // Kiểm tra email đã tồn tại
            $userModel = $this->model('UserModel');
            if ($userModel->getByEmail($email)) {
                $errors[] = 'Email đã được sử dụng';
            }
            
            if (empty($errors)) {
                // Tạo mã OTP và gửi email
                $otp = rand(100000, 999999);
                
                // Lưu thông tin vào session để xác thực sau
                $_SESSION['add_account'] = [
                    'fullname' => $fullname,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => $role,
                    'otp' => $otp
                ];
                
                // Gửi OTP qua email
                $this->sendOtpEmail($email, $otp);
                
                // Hiển thị form nhập OTP
                $this->view('adminPage', [
                    'page' => 'AddAccountView',
                    'show_otp' => true,
                    'email' => $email
                ]);
                return;
            }
            
            // Nếu có lỗi, hiển thị lại form với thông báo lỗi
            $this->view('adminPage', [
                'page' => 'AddAccountView',
                'error' => implode('<br>', $errors),
                'form_data' => $_POST
            ]);
        } else {
            // Hiển thị form thêm tài khoản
            $this->view('adminPage', ['page' => 'AddAccountView']);
        }
    }
    
    /**
     * Xác thực OTP và tạo tài khoản theo thông tin đã nhập
     */
    function verifyOtp(){
        $this->requireRole(['admin'], 'admin-add-account');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputOtp = $_POST['otp'] ?? '';
            
            if (!isset($_SESSION['add_account'])) {
                header('Location: '.APP_URL.'/Admin/addAccount');
                exit();
            }
            
            $accountData = $_SESSION['add_account'];
            
            if ($accountData['otp'] == $inputOtp) {
                // OTP đúng, tạo tài khoản
                $userModel = $this->model('UserModel');
                $token = bin2hex(random_bytes(32));
                
                // Tạo user mới theo cách model hoạt động
                $userModel->fullname = $accountData['fullname'];
                $userModel->email = $accountData['email'];
                $userModel->password = $accountData['password'];
                $userModel->token = $token;
                $userModel->role = $accountData['role'];
                
                if ($userModel->create()) {
                    unset($_SESSION['add_account']);
                    $_SESSION['flash_message'] = 'Tạo tài khoản thành công!';
                    $_SESSION['flash_type'] = 'success';
                    header('Location: '.APP_URL.'/Admin/customers');
                    exit();
                } else {
                    $error = 'Có lỗi xảy ra khi tạo tài khoản';
                }
            } else {
                $error = 'Mã OTP không đúng';
            }
            
            // Nếu có lỗi, hiển thị lại form OTP
            $this->view('adminPage', [
                'page' => 'AddAccountView',
                'show_otp' => true,
                'email' => $accountData['email'] ?? '',
                'error' => $error ?? ''
            ]);
        } else {
            header('Location: '.APP_URL.'/Admin/addAccount');
            exit();
        }
    }
    
    /**
     * Gửi OTP qua email (SMTP Gmail)
     * @param string $email
     * @param int|string $otp
     */
    private function sendOtpEmail($email, $otp) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'nttv9604@gmail.com'; // Thay bằng Gmail của bạn
            $mail->Password = 'ryae yfan rkle pelu'; // Thay bằng App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('nttv9604@gmail.com', 'Admin hệ thống');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Mã OTP xác thực tạo tài khoản";
            $mail->Body = "Mã OTP để tạo tài khoản mới là: <b>$otp</b><br>Vui lòng nhập mã này để hoàn tất quá trình tạo tài khoản.";

            $mail->send();
        } catch (Exception $e) {
            error_log("Gửi email thất bại: {$mail->ErrorInfo}");
        }
    }
    /**
     * Quản lý ngưỡng giảm giá: điều hướng đến trang thống nhất
     */
    function thresholds(){
        // Điều hướng sang trang quản lý giảm giá hợp nhất
        $this->requireRole(['admin','staff'], 'admin-thresholds');
        header('Location: ' . APP_URL . '/Discount/show#threshold');
        exit();
    }

    /**
     * Tạo ngưỡng giảm giá mới (POST)
     */
    function thresholdCreate(){
        $this->requireRole(['admin','staff'], 'admin-thresholds');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: '.APP_URL.'/Admin/thresholds'); exit(); }
        $min = (int)($_POST['min_total'] ?? 0);
        $percent = (int)($_POST['percent'] ?? 0);
        $status = isset($_POST['status']) ? 1 : 0;
        if ($min <= 0 || $percent <=0 || $percent>100) { header('Location: '.APP_URL.'/Admin/thresholds'); exit(); }
        $model = $this->model('ThresholdDiscountModel');
        $model->create($min, $percent, $status);
        header('Location: '.APP_URL.'/Discount/show#threshold');
        exit();
    }

    /**
     * Xóa ngưỡng giảm giá theo id
     * @param int|string $id
     */
    function thresholdDelete($id){
        $this->requireRole(['admin','staff'], 'admin-thresholds');
        $model = $this->model('ThresholdDiscountModel');
        $model->remove($id);
        header('Location: '.APP_URL.'/Discount/show#threshold');
        exit();
    }

    /**
     * Bật/tắt trạng thái ngưỡng giảm giá theo id
     * @param int|string $id
     */
    function thresholdToggle($id){
        $this->requireRole(['admin','staff'], 'admin-thresholds');
        $model = $this->model('ThresholdDiscountModel');
        $tiers = $model->getAll();
        foreach($tiers as $t){ if ($t['id']==$id){ $new = $t['status']?0:1; $model->updateStatus($id,$new); break; } }
        header('Location: '.APP_URL.'/Discount/show#threshold');
        exit();
    }
}