<?php
/**
 * Controller quản trị và hiển thị bài viết
 * - Admin: danh sách/tạo/sửa/xóa
 * - Front: danh sách và chi tiết bài viết đã xuất bản
 */
class Article extends Controller {
    /**
     * Admin: danh sách bài viết (có phân trang)
     */
    public function admin(){
        $this->requireRole(['admin'], 'admin-articles');
        $m = $this->model('ArticleModel');
        
        // Phân trang
        $itemsPerPage = 15;
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($currentPage - 1) * $itemsPerPage;
        
        $total = $m->countAll(false);
        $articles = $m->all(false, $itemsPerPage, $offset);
        $totalPages = max(1, ceil($total / $itemsPerPage));
        
        $this->view('adminPage', [
            'page'=>'ArticleListView',
            'articles'=>$articles,
            'currentPage'=>$currentPage,
            'totalPages'=>$totalPages,
            'total'=>$total
        ]);
    }

    /**
     * Admin: form tạo bài viết mới
     */
    public function create(){
        $this->requireRole(['admin'], 'admin-articles');
        $this->view('adminPage', ['page'=>'ArticleFormView','article'=>null]);
    }

    /**
     * Admin: lưu bài viết mới (xử lý upload ảnh, validate cơ bản)
     */
    public function store(){
        $this->requireRole(['admin'], 'admin-articles');
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){ header('Location: '.APP_URL.'/Article/admin'); exit(); }
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $image = '';
        $status = (int)($_POST['status'] ?? 0);
        if($title===''){ $_SESSION['flash_message']='Tiêu đề không được trống.'; header('Location: '.APP_URL.'/Article/create'); exit(); }
        // Handle image upload if provided
        if (!empty($_FILES['image_file']) && isset($_FILES['image_file']['tmp_name']) && is_uploaded_file($_FILES['image_file']['tmp_name'])) {
            $tmp = $_FILES['image_file']['tmp_name'];
            $name = $_FILES['image_file']['name'] ?? '';
            $size = (int)($_FILES['image_file']['size'] ?? 0);
            $err  = (int)($_FILES['image_file']['error'] ?? 0);
            if ($err === UPLOAD_ERR_OK) {
                // Basic validations
                if ($size > 2*1024*1024) { // ~2MB
                    $_SESSION['flash_message'] = 'Ảnh quá lớn (tối đa 2MB).';
                    header('Location: '.APP_URL.'/Article/create'); exit();
                }
                $finfo = @getimagesize($tmp);
                if ($finfo === false) {
                    $_SESSION['flash_message'] = 'File tải lên không phải là ảnh hợp lệ.';
                    header('Location: '.APP_URL.'/Article/create'); exit();
                }
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $ext = strtolower($ext);
                if (!in_array($ext, ['jpg','jpeg','png','gif'])) {
                    $_SESSION['flash_message'] = 'Định dạng ảnh không hỗ trợ. Chỉ JPG, PNG, GIF.';
                    header('Location: '.APP_URL.'/Article/create'); exit();
                }
                $safeBase = preg_replace('/[^a-zA-Z0-9_-]/','', pathinfo($name, PATHINFO_FILENAME));
                $newName = $safeBase.'-'.date('YmdHis').'-'.bin2hex(random_bytes(3)).'.'.$ext;
                $uploadDir = dirname(__DIR__).DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR;
                if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }
                if (!@move_uploaded_file($tmp, $uploadDir.$newName)) {
                    $_SESSION['flash_message'] = 'Không thể lưu ảnh lên máy chủ.';
                    header('Location: '.APP_URL.'/Article/create'); exit();
                }
                $image = $newName;
            } else {
                $_SESSION['flash_message'] = 'Lỗi tải ảnh (mã '.$err.').';
                header('Location: '.APP_URL.'/Article/create'); exit();
            }
        }
        $m = $this->model('ArticleModel');
        $ok = $m->create($title,$content,$image,$status);
        if ($ok) {
            $_SESSION['flash_message'] = 'Đã lưu bài viết thành công.';
            $_SESSION['flash_type'] = 'success';
            header('Location: '.APP_URL.'/Article/admin'); exit();
        } else {
            $_SESSION['flash_message'] = 'Không thể lưu bài viết. Vui lòng kiểm tra cơ sở dữ liệu (đã tạo bảng articles chưa?).';
            $_SESSION['flash_type'] = 'danger';
            header('Location: '.APP_URL.'/Article/create'); exit();
        }
    }

    /**
     * Admin: form sửa bài viết theo id
     * @param int|string $id
     */
    public function edit($id){
        $this->requireRole(['admin'], 'admin-articles');
        $m = $this->model('ArticleModel');
        $article = $m->findById($id);
        $this->view('adminPage', ['page'=>'ArticleFormView','article'=>$article]);
    }

    /**
     * Admin: cập nhật bài viết theo id (có thể thay ảnh)
     */
    public function update(){
        $this->requireRole(['admin'], 'admin-articles');
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){ header('Location: '.APP_URL.'/Article/admin'); exit(); }
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $image = trim($_POST['old_image'] ?? '');
        $status = (int)($_POST['status'] ?? 0);
        // Handle new image upload if provided
        if (!empty($_FILES['image_file']) && isset($_FILES['image_file']['tmp_name']) && is_uploaded_file($_FILES['image_file']['tmp_name'])) {
            $tmp = $_FILES['image_file']['tmp_name'];
            $name = $_FILES['image_file']['name'] ?? '';
            $size = (int)($_FILES['image_file']['size'] ?? 0);
            $err  = (int)($_FILES['image_file']['error'] ?? 0);
            if ($err === UPLOAD_ERR_OK) {
                if ($size > 2*1024*1024) { // ~2MB
                    $_SESSION['flash_message'] = 'Ảnh quá lớn (tối đa 2MB).';
                    header('Location: '.APP_URL.'/Article/edit/'.$id); exit();
                }
                $finfo = @getimagesize($tmp);
                if ($finfo === false) {
                    $_SESSION['flash_message'] = 'File tải lên không phải là ảnh hợp lệ.';
                    header('Location: '.APP_URL.'/Article/edit/'.$id); exit();
                }
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $ext = strtolower($ext);
                if (!in_array($ext, ['jpg','jpeg','png','gif'])) {
                    $_SESSION['flash_message'] = 'Định dạng ảnh không hỗ trợ. Chỉ JPG, PNG, GIF.';
                    header('Location: '.APP_URL.'/Article/edit/'.$id); exit();
                }
                $safeBase = preg_replace('/[^a-zA-Z0-9_-]/','', pathinfo($name, PATHINFO_FILENAME));
                $newName = $safeBase.'-'.date('YmdHis').'-'.bin2hex(random_bytes(3)).'.'.$ext;
                $uploadDir = dirname(__DIR__).DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR;
                if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }
                if (!@move_uploaded_file($tmp, $uploadDir.$newName)) {
                    $_SESSION['flash_message'] = 'Không thể lưu ảnh lên máy chủ.';
                    header('Location: '.APP_URL.'/Article/edit/'.$id); exit();
                }
                $image = $newName; // replace old image reference
            } else {
                $_SESSION['flash_message'] = 'Lỗi tải ảnh (mã '.$err.').';
                header('Location: '.APP_URL.'/Article/edit/'.$id); exit();
            }
        }
        $m = $this->model('ArticleModel');
        $ok = $m->updateById($id,$title,$content,$image,$status);
        if ($ok) {
            $_SESSION['flash_message'] = 'Cập nhật bài viết thành công.';
            $_SESSION['flash_type'] = 'success';
            header('Location: '.APP_URL.'/Article/admin'); exit();
        } else {
            $_SESSION['flash_message'] = 'Không thể cập nhật bài viết.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: '.APP_URL.'/Article/edit/'.$id); exit();
        }
    }

    /**
     * Admin: xóa bài viết theo id (POST)
     */
    public function delete(){
        $this->requireRole(['admin'], 'admin-articles');
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){ header('Location: '.APP_URL.'/Article/admin'); exit(); }
        $id = (int)($_POST['id'] ?? 0);
        $m = $this->model('ArticleModel');
        $ok = $m->deleteById($id);
        $_SESSION['flash_message'] = $ok ? 'Đã xóa bài viết.' : 'Không thể xóa bài viết.';
        $_SESSION['flash_type'] = $ok ? 'success' : 'danger';
        header('Location: '.APP_URL.'/Article/admin'); exit();
    }

    /**
     * Front: danh sách bài viết đã xuất bản
     */
    public function list(){
        $m = $this->model('ArticleModel');
        $articles = $m->all(true);
        $this->view('homePage', ['page'=>'ArticleListView','articles'=>$articles]);
    }

    /**
     * Front: chi tiết bài viết theo id (chỉ hiển thị nếu đã xuất bản)
     * @param int|string $id
     */
    public function detail($id){
        $m = $this->model('ArticleModel');
        $article = $m->findById($id);
        if(!$article || (int)$article['status']!==1){ $article = null; }
        // Prepare likes and comments data
        $likesCount = 0; $userLiked = false; $comments = [];
        if ($article) {
            $likeModel = $this->model('ArticleLikeModel');
            $commentModel = $this->model('ArticleCommentModel');
            $likesCount = $likeModel->countLikes((int)$article['id']);
            $comments = $commentModel->listByArticle((int)$article['id']);
            $userId = $_SESSION['user']['id'] ?? null;
            if ($userId) { $userLiked = $likeModel->hasLiked((int)$article['id'], (int)$userId); }
            // Related articles (basic: other published articles)
            $related = $m->related((int)$article['id'], 3);
            // Prev/Next
            $prev = $m->prevOf((int)$article['id']);
            $next = $m->nextOf((int)$article['id']);
        }
        $this->view('homePage', [
            'page'=>'ArticleDetailView',
            'article'=>$article,
            'likesCount'=>$likesCount,
            'userLiked'=>$userLiked,
            'comments'=>$comments,
            'related'=>$related ?? [],
            'prev'=>$prev ?? null,
            'next'=>$next ?? null
        ]);
    }

    /**
     * Toggle like for an article (AJAX JSON)
     */
    public function like($id){
        header('Content-Type: application/json');
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) { echo json_encode(['ok'=>false,'error'=>'not_logged_in']); return; }
        $likeModel = $this->model('ArticleLikeModel');
        $toggled = $likeModel->toggleLike((int)$id, (int)$userId);
        $count = $likeModel->countLikes((int)$id);
        echo json_encode(['ok'=>true,'liked'=>$toggled['liked'], 'count'=>$count]);
    }

    /**
     * Add a comment to an article (AJAX JSON)
     */
    public function comment($id){
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['ok'=>false,'error'=>'invalid_method']); return; }
        $user = $_SESSION['user'] ?? null;
        if (!$user) { echo json_encode(['ok'=>false,'error'=>'not_logged_in']); return; }
        $content = trim($_POST['content'] ?? '');
        if ($content === '') { echo json_encode(['ok'=>false,'error'=>'empty']); return; }
        $commentModel = $this->model('ArticleCommentModel');
        $ok = $commentModel->addComment((int)$id, (int)($user['id'] ?? 0), $user['fullname'] ?? '', $content);
        if (!$ok) { echo json_encode(['ok'=>false]); return; }
        $comments = $commentModel->listByArticle((int)$id);
        echo json_encode(['ok'=>true,'comments'=>$comments]);
    }
}