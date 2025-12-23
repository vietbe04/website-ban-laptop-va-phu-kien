<?php
/**
 * Controller quản lý bình luận bài viết (Admin)
 * - Danh sách, Ẩn/Hiện (status), Xóa
 */
class ArticleComment extends Controller {
    /**
     * Admin: danh sách bình luận bài viết có lọc + phân trang
     */
    public function adminIndex(){
        $this->requireRole(['admin'], 'admin-article-comments');
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'status' => trim($_GET['status'] ?? '')
        ];
        $itemsPerPage = 15;
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($currentPage - 1) * $itemsPerPage;

        $m = $this->model('ArticleCommentModel');
        $total = $m->countAdminSearch($filters);
        $list = $m->adminSearch($filters, $itemsPerPage, $offset);
        $totalPages = max(1, (int)ceil($total / $itemsPerPage));

        $this->view('adminPage', [
            'page' => 'ArticleCommentAdminView',
            'comments' => $list,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'total' => $total
        ]);
    }

    /**
     * Admin: Ẩn/Hiện một bình luận (POST)
     */
    public function approve(){
        $this->requireRole(['admin'], 'admin-article-comments');
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){ header('Location: '.APP_URL.'/ArticleComment/adminIndex'); exit(); }
        $id = (int)($_POST['id'] ?? 0);
        $status = (int)($_POST['status'] ?? 0);
        $m = $this->model('ArticleCommentModel');
        $m->setStatus($id, $status);
        header('Location: '.APP_URL.'/ArticleComment/adminIndex');
        exit();
    }

    /**
     * Admin: Xóa bình luận theo id (POST)
     */
    public function delete(){
        $this->requireRole(['admin'], 'admin-article-comments');
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){ header('Location: '.APP_URL.'/ArticleComment/adminIndex'); exit(); }
        $id = (int)($_POST['id'] ?? 0);
        $m = $this->model('ArticleCommentModel');
        $m->deleteById($id);
        header('Location: '.APP_URL.'/ArticleComment/adminIndex');
        exit();
    }
}
