    
<?php
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

//session_start();

/**
 * Controller xác thực tài khoản người dùng
 * - Đăng ký + gửi/kiểm tra OTP
 * - Đăng nhập/đăng xuất, đổi mật khẩu, quên mật khẩu (token)
 */
class AuthController extends Controller {
    // Bỏ cơ chế tự đăng nhập remember-me; chỉ dùng session chuẩn.
    /**
     * Hiển thị form đăng ký tài khoản
     * Route ví dụ: AuthController/Show
     */
    public function Show() {
        $this->view("homePage",["page"=>"RegisterView"]);
    }

    /**
     * Xử lý đăng ký: nhận form, lưu tạm thông tin và gửi OTP qua email
     * POST: fullname, email, password
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            if ($fullname === '' || $email === '' || $password === '') {
                echo '<div class="container mt-5"><div class="alert alert-danger">Vui lòng nhập đầy đủ thông tin!</div></div>';
                $this->view("homePage",["page"=>"RegisterView"]);
                return;
            }

            // Tạo mã OTP
            $otp = rand(100000, 999999);
            $_SESSION['register'] = [
                'fullname' => $fullname,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'otp' => $otp
            ];
            // Gửi OTP qua email
            $this->sendOtpEmail($email, $otp);

            // Hiển thị form nhập OTP
            $this->view("homePage",["page"=>"OtpView"]);
        }
    }

    /**
     * Gửi mã OTP đến email người dùng (SMTP Gmail)
     * @param string $email
     * @param int|string $otp
     * @return void
     */
    private function sendOtpEmail($email, $otp) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'nttv9604@gmail.com';
            $mail->Password = 'ryae yfan rkle pelu';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('nttv9604@gmail.com', 'Cửa hàng DQV');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Xác thực tài khoản - Mã OTP';
            
            $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5;">
                <div style="background-color: #ffffff; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 40px; color: white;">🔐</span>
                        </div>
                        <h2 style="color: #333; margin: 0; font-size: 28px; font-weight: 700;">Xác thực tài khoản</h2>
                    </div>
                    
                    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 25px 0;">
                        <p style="color: #666; font-size: 16px; margin: 0 0 15px 0; line-height: 1.6;">
                            Cảm ơn bạn đã đăng ký tài khoản tại <strong>Cửa hàng DQV</strong>!
                        </p>
                        <p style="color: #666; font-size: 16px; margin: 0; line-height: 1.6;">
                            Vui lòng sử dụng mã OTP bên dưới để hoàn tất quá trình đăng ký:
                        </p>
                    </div>
                    
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 10px; text-align: center; margin: 30px 0;">
                        <p style="color: rgba(255,255,255,0.9); margin: 0 0 10px 0; font-size: 14px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">Mã OTP của bạn</p>
                        <div style="background-color: rgba(255,255,255,0.15); padding: 15px 30px; border-radius: 8px; display: inline-block; backdrop-filter: blur(10px);">
                            <span style="font-size: 36px; font-weight: 700; color: #ffffff; letter-spacing: 8px; font-family: \'Courier New\', monospace;">' . $otp . '</span>
                        </div>
                    </div>
                    
                    <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 5px; margin: 25px 0;">
                        <p style="margin: 0; color: #856404; font-size: 14px; line-height: 1.6;">
                            <strong>⚠️ Lưu ý:</strong> Mã OTP này chỉ có hiệu lực trong phiên đăng ký hiện tại. Vui lòng không chia sẻ mã này với bất kỳ ai.
                        </p>
                    </div>
                    
                    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #f0f0f0;">
                        <p style="color: #999; font-size: 14px; margin: 0;">
                            Nếu bạn không yêu cầu đăng ký tài khoản, vui lòng bỏ qua email này.
                        </p>
                    </div>
                    
                    <div style="margin-top: 30px; text-align: center;">
                        <p style="color: #999; font-size: 14px; margin: 5px 0;">
                            Trân trọng,<br>
                            <strong style="color: #667eea;">Đội ngũ DQV</strong>
                        </p>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <p style="color: #999; font-size: 12px; margin: 5px 0;">
                        Email này được gửi tự động, vui lòng không trả lời.
                    </p>
                </div>
            </div>';

            $mail->send();
        } catch (Exception $e) {
            error_log('Gửi OTP email thất bại: ' . $mail->ErrorInfo);
        }
    }

    /**
     * Xác thực OTP và tạo tài khoản chính thức
     * - Nếu thành công: tạo user, merge giỏ hàng tạm (nếu có), điều hướng phù hợp
     */
    public function verifyOtp() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputOtp = $_POST['otp'];
            if (isset($_SESSION['register']) && $_SESSION['register']['otp'] == $inputOtp) {
                // Lưu user vào DB
                $user = $this->model('UserModel');
                $email = $_SESSION['register']['email'];
                if ($user->emailExists($email)) {
                    echo '<div class="container mt-5"><div class="alert alert-danger">Email đã được đăng ký. Vui lòng sử dụng email khác!</div></div>';
                    unset($_SESSION['register']);
                    $this->view("homePage",["page"=>"RegisterView"]);
                    return;
                }
                $user->email = $email;
                $user->password = $_SESSION['register']['password'];
                $user->fullname = $_SESSION['register']['fullname'];
                $user->token = bin2hex(random_bytes(16));
                $user->create();
                unset($_SESSION['register']);
                echo '<div class="container mt-5"><div class="alert alert-success">Đăng ký thành viên thành công! Bạn có thể <a href="' . APP_URL . '/AuthController/ShowLogin" class="btn btn-success ms-2">Đăng nhập để đặt hàng</a></div></div>';
                // Hoặc tự động đăng nhập và chuyển sang trang đặt hàng nếu muốn
                // Lấy user vừa tạo để lấy id
                $userModel = $this->model('UserModel');
                $stmt = $userModel->findByEmail($user->email);
                $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
                if ($row) {
                    $_SESSION['user'] = [
                        'id' => $row['user_id'] ?? $row['id'] ?? 0,
                        'email' => $row['email'],
                        'fullname' => $row['fullname'],
                        'role' => $row['role'] ?? 'user'
                    ];
                } else {
                    $_SESSION['user'] = [
                        'email' => $user->email,
                        'fullname' => $user->fullname,
                        'role' => 'user'
                    ];
                }
                // Ghi cart về bảng cart (CartModel) thay vì OrderModel staging (loại bỏ các hàm chưa tồn tại)
                if (!empty($_SESSION['cart']) && isset($_SESSION['user']['id'])) {
                    $cartModel = $this->model('CartModel');
                    $userEmail = $_SESSION['user']['email'] ?? '';
                    foreach ($_SESSION['cart'] as $sessionKey => $it) {
                        $masp = $it['masp'] ?? null;
                        if (!$masp) { continue; }
                        $qty = (int)($it['qty'] ?? 1);
                        $phantram = isset($it['phantram']) ? (float)$it['phantram'] : (float)($it['khuyenmai'] ?? 0);
                        $giaGoc = (float)($it['giaxuat'] ?? 0);
                        $giaSauKM = $giaGoc * (1 - $phantram/100);
                        $capacityVariantId = $it['capacity_variant_id'] ?? null;
                        $colorVariantId = $it['color_variant_id'] ?? null;
                        $cartModel->addOrUpdateCart($userEmail, $masp, $capacityVariantId, $colorVariantId, $qty, $giaSauKM);
                    }
                }
                // Nếu có redirect sau khi đăng ký (ví dụ checkout), chuyển tới đó
                if (!empty($_SESSION['next']) && $_SESSION['next'] === 'checkout') {
                    unset($_SESSION['next']);
                    header('Location: ' . APP_URL . '/Home/checkoutInfo');
                    exit();
                }
                // Sau đăng ký chuyển về trang chủ (index) thay vì danh sách sản phẩm (Show)
                header('Location: ' . APP_URL . '/Home/index');
                exit();
            } else {
                echo '<div class="container mt-5"><div class="alert alert-danger">Mã OTP không đúng!</div></div>';
                $this->view("homePage",["page"=>"OtpView"]);
            }
        }
    }
    /**
     * Hiển thị form đăng nhập; ghi nhớ tham số next để điều hướng sau đăng nhập
     */
    public function ShowLogin() {
      //  $this->view("Font_end/LoginView");
      // Nếu có tham số next trong query string, lưu vào session để redirect sau khi login
      $next = isset($_GET['next']) ? $_GET['next'] : null;
      if ($next) {
          $_SESSION['next'] = $next;
      }
      $this->view("homePage",["page"=>"LoginView"]);
    }

    /**
     * Xử lý đăng nhập: xác thực email/mật khẩu, lưu session, merge giỏ hàng, điều hướng theo role/next
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $userModel = $this->model('UserModel');
            $stmt = $userModel->findByEmail($email);
            $user = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
            
            // Kiểm tra tài khoản có bị khóa không
            if ($user && isset($user['is_locked']) && (int)$user['is_locked'] === 1) {
                $this->view('homePage', [
                    'page' => 'LoginView',
                    'error' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên để được hỗ trợ.'
                ]);
                return;
            }
            
            if ($user && password_verify($password, $user['password'])) {
                // Lưu thêm id + role vào session để phân quyền
                $_SESSION['user'] = [
                    'id' => $user['user_id'] ?? $user['id'] ?? 0,
                    'email' => $user['email'],
                    'fullname' => $user['fullname'],
                    'role' => $user['role'] ?? 'user'
                ];
                // Nếu người dùng chọn nhớ mật khẩu/email -> chỉ lưu email (không lưu mật khẩu, để trình duyệt tự quản lý)
                if (!empty($_POST['remember'])) {
                    setcookie('remember_email', $user['email'], time() + 60*60*24*30, '/', '', false, true);
                } else {
                    // Xóa nếu trước đó đã lưu
                    if (!empty($_COOKIE['remember_email'])) {
                        setcookie('remember_email', '', time() - 3600, '/', '', false, true);
                    }
                }
                // Đồng bộ session cart sang DB bằng CartModel (loại bỏ các hàm staging không tồn tại)
                if (!empty($_SESSION['cart']) && isset($_SESSION['user']['id'])) {
                    $cartModel = $this->model('CartModel');
                    $userEmail = $_SESSION['user']['email'] ?? '';
                    foreach ($_SESSION['cart'] as $sessionKey => $it) {
                        $masp = $it['masp'] ?? null;
                        if (!$masp) { continue; }
                        $qty = (int)($it['qty'] ?? 1);
                        $phantram = isset($it['phantram']) ? (float)$it['phantram'] : (float)($it['khuyenmai'] ?? 0);
                        $giaGoc = (float)($it['giaxuat'] ?? 0);
                        $giaSauKM = $giaGoc * (1 - $phantram/100);
                        $capacityVariantId = $it['capacity_variant_id'] ?? null;
                        $colorVariantId = $it['color_variant_id'] ?? null;
                        $cartModel->addOrUpdateCart($userEmail, $masp, $capacityVariantId, $colorVariantId, $qty, $giaSauKM);
                    }
                }
                // Điều hướng theo quyền sau khi đăng nhập
                // 1) Nếu có yêu cầu tiếp tục quy trình checkout thì ưu tiên checkout
                if (!empty($_SESSION['next']) && $_SESSION['next'] === 'checkout') {
                    unset($_SESSION['next']);
                    header('Location: ' . APP_URL . '/Home/checkoutInfo');
                    exit();
                }
                // 2) Nếu là admin hoặc staff thì chuyển sang trang quản trị
                $role = $_SESSION['user']['role'] ?? 'user';
                if ($role === 'admin' || $role === 'staff') {
                    header('Location: ' . APP_URL . '/Admin');
                    exit();
                }
                // 3) Mặc định trở về trang chủ cho tài khoản thường
                header('Location: ' . APP_URL . '/Home/index');
                exit();
            } else {
                $this->view('homePage', [
                    'page' => 'LoginView',
                    'error' => 'Email hoặc mật khẩu không đúng!'
                ]);
            }
        }
    }

    /**
     * Đăng xuất và điều hướng về trang chủ
     */
    public function logout() {
        // Xóa email ghi nhớ nếu muốn (tùy chọn: giữ lại để user đăng nhập nhanh)
        session_destroy();
        // Đăng xuất chuyển về trang chủ (index) rõ ràng
        header('Location: ' . APP_URL . '/Home/index');
        exit();
    }

    /**
     * Hiển thị form đổi mật khẩu (yêu cầu nhập email, mật khẩu hiện tại, mật khẩu mới)
     */
    public function showChangePassword() {
        $this->view('homePage', ['page' => 'ChangePasswordView']);
    }

    /**
     * Hiển thị trang thông tin tài khoản cho người đã đăng nhập
     */
    public function showAccount() {
        if (empty($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            header('Location: ' . APP_URL . '/AuthController/ShowLogin');
            exit();
        }
        $userModel = $this->model('UserModel');
        $user = $userModel->getByIdWithStats($_SESSION['user']['id']);
        $this->view('homePage', ['page' => 'AccountInfoView', 'user' => $user]);
    }

    /**
     * Xử lý cập nhật thông tin tài khoản (fullname, email)
     */
    public function updateAccount() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/AuthController/showAccount');
            exit();
        }
        if (empty($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            header('Location: ' . APP_URL . '/AuthController/ShowLogin');
            exit();
        }
        $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
        // Email cannot be changed here; use current session email (or fallback to DB)
        $currentEmail = $_SESSION['user']['email'] ?? '';
        if ($fullname === '') {
            $this->view('homePage', ['page' => 'AccountInfoView', 'user' => ['fullname'=>$fullname,'email'=>$currentEmail], 'error' => 'Vui lòng điền đầy đủ thông tin.']);
            return;
        }
        $userModel = $this->model('UserModel');
        // Do not allow updating email via this endpoint — always keep current email
        $email = $currentEmail;
        $ok = $userModel->update($_SESSION['user']['id'], $fullname, $email);
        if ($ok) {
            // cập nhật session
            $_SESSION['user']['fullname'] = $fullname;
            // do not change session email here
            $user = $userModel->getByIdWithStats($_SESSION['user']['id']);
            $this->view('homePage', ['page' => 'AccountInfoView', 'user' => $user, 'success' => 'Cập nhật thông tin thành công.']);
            return;
        } else {
            $this->view('homePage', ['page' => 'AccountInfoView', 'user' => ['fullname'=>$fullname,'email'=>$email], 'error' => 'Không thể cập nhật thông tin, thử lại sau.']);
            return;
        }
    }

    /**
     * Thay mật khẩu cho người dùng đang đăng nhập (xác thực mật khẩu hiện tại)
     */
    public function changePasswordAuthenticated() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/AuthController/showAccount');
            exit();
        }
        if (empty($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            header('Location: ' . APP_URL . '/AuthController/ShowLogin');
            exit();
        }
        $current = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $new = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirm = isset($_POST['new_password_confirm']) ? $_POST['new_password_confirm'] : '';
        if ($current === '' || $new === '' || $confirm === '') {
            $this->view('homePage', ['page' => 'AccountInfoView', 'user' => $this->model('UserModel')->getByIdWithStats($_SESSION['user']['id']), 'error' => 'Vui lòng điền đầy đủ thông tin đổi mật khẩu.']);
            return;
        }
        if ($new !== $confirm) {
            $this->view('homePage', ['page' => 'AccountInfoView', 'user' => $this->model('UserModel')->getByIdWithStats($_SESSION['user']['id']), 'error' => 'Mật khẩu mới không khớp.']);
            return;
        }
        $userModel = $this->model('UserModel');
        $stmt = $userModel->findByIdInternal($_SESSION['user']['id']);
        $user = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        if (!$user) {
            header('Location: ' . APP_URL . '/AuthController/ShowLogin');
            exit();
        }
        if (!password_verify($current, $user['password'])) {
            $this->view('homePage', ['page' => 'AccountInfoView', 'user' => $user, 'error' => 'Mật khẩu hiện tại không đúng.']);
            return;
        }
        $ok = $userModel->updatePasswordById($_SESSION['user']['id'], password_hash($new, PASSWORD_DEFAULT));
        if ($ok) {
            $this->view('homePage', ['page' => 'AccountInfoView', 'user' => $user, 'success' => 'Đổi mật khẩu thành công.']);
            return;
        } else {
            $this->view('homePage', ['page' => 'AccountInfoView', 'user' => $user, 'error' => 'Không thể đổi mật khẩu, thử lại sau.']);
            return;
        }
    }

    /**
     * Xử lý đổi mật khẩu: xác thực email + mật khẩu hiện tại, cập nhật mật khẩu mới
     */
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/AuthController/ShowLogin');
            exit();
        }
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $current = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $new = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirm = isset($_POST['new_password_confirm']) ? $_POST['new_password_confirm'] : '';
        if ($email === '' || $current === '' || $new === '' || $confirm === '') {
            $this->view('homePage', ['page' => 'ChangePasswordView', 'error' => 'Vui lòng điền đầy đủ thông tin.']);
            return;
        }
        if ($new !== $confirm) {
            $this->view('homePage', ['page' => 'ChangePasswordView', 'error' => 'Mật khẩu mới không khớp.']);
            return;
        }
        $userModel = $this->model('UserModel');
        $stmt = $userModel->findByEmail($email);
        $user = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        if (!$user) {
            $this->view('homePage', ['page' => 'ChangePasswordView', 'error' => 'Email không tồn tại.']);
            return;
        }
        if (!password_verify($current, $user['password'])) {
            $this->view('homePage', ['page' => 'ChangePasswordView', 'error' => 'Mật khẩu hiện tại không đúng.']);
            return;
        }
        // Cập nhật mật khẩu
        $userModel->updatePassword($email, password_hash($new, PASSWORD_DEFAULT));
        $this->view('homePage', ['page' => 'ChangePasswordView', 'success' => 'Đổi mật khẩu thành công.']);
    }

    /**
     * Hiển thị form quên mật khẩu (nhập email để nhận link đặt lại)
     */
    public function forgotPassword() {
        //$this->view("Font_end/ForgotPasswordView");
        $this->view("homePage",["page"=>"ForgotPasswordView"]);
    }
    /**
     * Gửi link đặt lại mật khẩu qua email (có token)
     */
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $userModel = $this->model('UserModel');
            $stmt = $userModel->findByEmail($email);
            $user = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
            if ($user) {
                $token = bin2hex(random_bytes(16));
                $userModel->setResetToken($email, $token);
                $resetLink = APP_URL . '/AuthController/showResetForm/' . $token;
                $this->sendResetLinkEmail($email, $resetLink);
                echo '<div class="container mt-5"><div class="alert alert-success">Link đặt lại mật khẩu đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư.</div></div>';
            } else {
                echo '<div class="container mt-5"><div class="alert alert-danger">Email không tồn tại!</div></div>';
            }
            $this->view("homePage",["page"=>"ForgotPasswordView"]);
        }
    }

    /**
     * Hiển thị form đặt lại mật khẩu mới theo token
     * @param string|null $token
     */
    public function showResetForm($token = null) {
        if (!$token) {
            header('Location: ' . APP_URL . '/AuthController/ShowLogin');
            exit();
        }
        $userModel = $this->model('UserModel');
        $stmt = $userModel->findByResetToken($token);
        $user = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        if (!$user) {
            echo '<div class="container mt-5"><div class="alert alert-danger">Token không hợp lệ hoặc đã hết hạn.</div></div>';
            header('Location: ' . APP_URL . '/AuthController/ShowLogin');
            exit();
        }
        $this->view('homePage', ['page' => 'ResetPasswordView', 'token' => $token]);
    }

    /**
     * Cập nhật mật khẩu mới dựa trên token hợp lệ
     */
    public function doResetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = isset($_POST['token']) ? trim($_POST['token']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $passwordConfirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
            if ($password === '' || $passwordConfirm === '' || $password !== $passwordConfirm) {
                $this->view('homePage', ['page' => 'ResetPasswordView', 'error' => 'Mật khẩu không hợp lệ hoặc không khớp.', 'token' => $token]);
                return;
            }
            $userModel = $this->model('UserModel');
            $stmt = $userModel->findByResetToken($token);
            $user = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
            if (!$user) {
                echo '<div class="container mt-5"><div class="alert alert-danger">Token không hợp lệ hoặc đã hết hạn.</div></div>';
                header('Location: ' . APP_URL . '/AuthController/ShowLogin');
                exit();
            }
            // Diagnostic logging: record which user/email is being updated
            @error_log('[RESET] token=' . $token . ' user=' . json_encode($user));
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            // Prefer updating by user_id when available to avoid email formatting mismatches
            $userId = $user['user_id'] ?? ($user['id'] ?? null);
            if ($userId) {
                $ok = $userModel->updatePasswordById($userId, $newHash);
                if ($ok) {
                    $cleared = $userModel->clearResetTokenById($userId);
                } else {
                    @error_log('[RESET] updatePasswordById failed for id=' . $userId);
                }
                @error_log('[RESET] updatePasswordById result for id=' . $userId . ' ok=' . ($ok?1:0) . ' clear=' . ($cleared?1:0));
            } else {
                $ok = $userModel->updatePassword($user['email'], $newHash);
                $cleared = $userModel->clearResetToken($user['email']);
                @error_log('[RESET] updatePassword fallback for email=' . ($user['email'] ?? 'NULL') . ' ok=' . ($ok?1:0) . ' clear=' . ($cleared?1:0));
            }
            if (!$ok) {
                echo '<div class="container mt-5"><div class="alert alert-danger">Không thể cập nhật mật khẩu, vui lòng thử lại hoặc liên hệ admin.</div></div>';
                $this->view('homePage', ['page' => 'ResetPasswordView', 'token' => $token]);
                return;
            }
            echo '<div class="container mt-5"><div class="alert alert-success">Mật khẩu đã được cập nhật. Bạn có thể đăng nhập bằng mật khẩu mới.</div></div>';
            header('Location: ' . APP_URL . '/AuthController/ShowLogin');
            exit();
        }
    }

    /**
     * Gửi mật khẩu mới qua email (không dùng trong flow token chuẩn)
     * @param string $email
     * @param string $newPass
     * @return void
     */
    private function sendNewPasswordEmail($email, $newPass) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'nttv9604@gmail.com';
            $mail->Password = 'ryae yfan rkle pelu';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom('nttv9604@gmail.com', 'Your App');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Mật khẩu mới cho tài khoản của bạn";
            $mail->Body = "Mật khẩu mới của bạn là: <b>$newPass</b>";
            $mail->send();
        } catch (Exception $e) {
            // Không echo lỗi ra ngoài
        }
    }

    /**
     * Gửi email chứa link đặt lại mật khẩu
     * @param string $email
     * @param string $link
     * @return void
     */
    private function sendResetLinkEmail($email, $link) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'nttv9604@gmail.com';
            $mail->Password = 'ryae yfan rkle pelu';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';
            
            $mail->setFrom('nttv9604@gmail.com', 'Cửa hàng DQV');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Yêu cầu đặt lại mật khẩu';
            
            $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5;">
                <div style="background-color: #ffffff; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 40px; color: white;">🔑</span>
                        </div>
                        <h2 style="color: #333; margin: 0; font-size: 28px; font-weight: 700;">Đặt lại mật khẩu</h2>
                    </div>
                    
                    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 25px 0;">
                        <p style="color: #666; font-size: 16px; margin: 0 0 15px 0; line-height: 1.6;">
                            Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản <strong>' . htmlspecialchars($email) . '</strong> tại <strong>Cửa hàng DQV</strong>.
                        </p>
                        <p style="color: #666; font-size: 16px; margin: 0; line-height: 1.6;">
                            Vui lòng nhấp vào nút bên dưới để tạo mật khẩu mới:
                        </p>
                    </div>
                    
                    <div style="text-align: center; margin: 35px 0;">
                        <a href="' . $link . '" style="display: inline-block; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 50px; font-size: 16px; font-weight: 600; letter-spacing: 0.5px; box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4); transition: all 0.3s ease;">
                            🔐 Đặt lại mật khẩu
                        </a>
                    </div>
                    
                    <div style="background-color: #e7f3ff; border-left: 4px solid #2196F3; padding: 15px; border-radius: 5px; margin: 25px 0;">
                        <p style="margin: 0 0 10px 0; color: #1565c0; font-size: 14px; font-weight: 600;">
                            ℹ️ Thông tin quan trọng:
                        </p>
                        <ul style="margin: 0; padding-left: 20px; color: #1976d2; font-size: 14px; line-height: 1.8;">
                            <li>Link này chỉ có hiệu lực trong thời gian ngắn</li>
                            <li>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này</li>
                            <li>Không chia sẻ link này với bất kỳ ai</li>
                        </ul>
                    </div>
                    
                    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin: 25px 0;">
                        <p style="color: #666; font-size: 13px; margin: 0 0 10px 0; line-height: 1.6;">
                            <strong>Không thể nhấp vào nút?</strong> Sao chép và dán URL sau vào trình duyệt:
                        </p>
                        <div style="background-color: #ffffff; padding: 12px; border-radius: 5px; border: 1px solid #e0e0e0; word-break: break-all;">
                            <a href="' . $link . '" style="color: #667eea; text-decoration: none; font-size: 13px;">' . $link . '</a>
                        </div>
                    </div>
                    
                    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #f0f0f0;">
                        <p style="color: #999; font-size: 14px; margin: 0 0 10px 0;">
                            Nếu bạn cần hỗ trợ, vui lòng liên hệ với chúng tôi.
                        </p>
                    </div>
                    
                    <div style="margin-top: 30px; text-align: center;">
                        <p style="color: #999; font-size: 14px; margin: 5px 0;">
                            Trân trọng,<br>
                            <strong style="color: #f5576c;">Đội ngũ DQV</strong>
                        </p>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <p style="color: #999; font-size: 12px; margin: 5px 0;">
                        Email này được gửi tự động, vui lòng không trả lời.
                    </p>
                    <p style="color: #999; font-size: 12px; margin: 5px 0;">
                        © 2025 Cửa hàng DQV. All rights reserved.
                    </p>
                </div>
            </div>';
            
            $mail->send();
        } catch (Exception $e) {
            error_log('Gửi reset password email thất bại: ' . $mail->ErrorInfo);
        }
    }

}