<!doctype html>
<html lang="vi">
    <head>
        <title>Quản Trị Hệ Thống - <?= isset($data['title']) ? $data['title'] : 'Dashboard' ?></title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="Hệ thống quản trị website thương mại điện tử">
        
        <!-- Bootstrap CSS -->
        <link href="<?= APP_URL ?>/public/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
        <!-- Custom Admin Theme -->
        <link href="<?= APP_URL ?>/public/css/admin-theme.css" rel="stylesheet" />
        
        <!-- Bootstrap Bundle JS (có Popper) -->
        <script defer src="<?= APP_URL ?>/public/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script defer src="<?= APP_URL ?>/public/js/admin-chat.js"></script>
    </head>
    
    <body>
        <header class="admin-header">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <div class="container-fluid">
                    <a class="navbar-brand d-flex align-items-center" href="<?= APP_URL ?>/Admin/show" title="Về trang tổng quan quản trị">
                        <i class="bi bi-speedometer2 fs-4 me-2"></i>
                        <span class="fw-bold">Quản Trị Hệ Thống</span>
                    </a>
                    
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="collapse navbar-collapse" id="adminNavbar">
                        <?php 
                        if (session_status() === PHP_SESSION_NONE) { session_start(); }
                        $currentRole = $_SESSION['user']['role'] ?? 'user'; 
                        ?>
                        
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/ProductType/">
                                    <i class="bi bi-tags me-1"></i> Loại sản phẩm
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/Product/">
                                    <i class="bi bi-box-seam me-1"></i> Sản phẩm
                                </a>
                            </li>
                            
                            <?php if ($currentRole === 'admin'): ?>
                            <!-- Quản lý Banner -->
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/Banner/index">
                                    <i class="bi bi-image me-1"></i> Banner
                                </a>
                            </li>
                            
                            <!-- Báo cáo & Thống kê -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-graph-up me-1"></i> Báo cáo & Thống kê
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/Report/Show">
                                        <i class="bi bi-currency-dollar me-1"></i> Doanh thu
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/Inventory/">
                                        <i class="bi bi-boxes me-1"></i> Tồn kho
                                    </a></li>
                                </ul>
                            </li>
                            
                            <!-- Quản lý đơn hàng -->
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/AdminOrder/index">
                                    <i class="bi bi-receipt me-1"></i> Đơn hàng
                                </a>
                            </li>
                            
                            <!-- Quản lý khuyến mãi (admin full access) -->
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/Discount/show">
                                    <i class="bi bi-percent me-1"></i> Khuyến mãi
                                </a>
                            </li>
                            
                            <!-- Quản lý nội dung -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-journal-text me-1"></i> Nội dung
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/Article/admin">
                                        <i class="bi bi-file-text me-1"></i> Bài viết
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/Review/adminIndex">
                                        <i class="bi bi-star me-1"></i> Đánh giá
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/ArticleComment/adminIndex">
                                        <i class="bi bi-chat-dots me-1"></i> Bình luận bài viết
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/Feedback/adminIndex">
                                        <i class="bi bi-chat-left-text me-1"></i> Góp ý người dùng
                                    </a></li>
                                </ul>
                            </li>
                            
                            <!-- Quản lý người dùng -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-people me-1"></i> Người dùng
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/Admin/customers">
                                        <i class="bi bi-person-lines-fill me-1"></i> Danh sách khách hàng
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/Admin/addAccount">
                                        <i class="bi bi-person-plus me-1"></i> Thêm tài khoản mới
                                    </a></li>
                                </ul>
                            </li>
                            
                            <!-- Quản lý nhà cung cấp -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-building me-1"></i> Nhà cung cấp
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/Supplier/index">
                                        <i class="bi bi-building me-1"></i> Danh sách NCC
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/Supplier/contracts">
                                        <i class="bi bi-file-earmark-text me-1"></i> Hợp đồng
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/Supplier/products">
                                        <i class="bi bi-box-seam me-1"></i> Hàng hóa cung cấp
                                    </a></li>
                                </ul>
                            </li>
                            <?php endif; ?>
                            <?php if ($currentRole === 'staff'): ?>
                            <!-- Quản lý đơn hàng (staff) -->
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/AdminOrder/index">
                                    <i class="bi bi-receipt me-1"></i> Đơn hàng
                                </a>
                            </li>
                            
                            <!-- Khuyến mãi (staff - chỉ tab khuyến mãi sản phẩm) -->
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/Discount/show">
                                    <i class="bi bi-percent me-1"></i> Khuyến mãi
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                        
                        <div class="d-flex align-items-center">
                            
                            <?php if (!empty($_SESSION['user'])): ?>
                            <div class="dropdown">
                                <button class="btn btn-link text-light dropdown-toggle d-flex align-items-center" 
                                        type="button" data-bs-toggle="dropdown">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-2">
                                            <?= mb_substr($_SESSION['user']['fullname'] ?? '', 0, 1, 'UTF-8') ?>
                                        </div>
                                        <div class="d-none d-lg-block">
                                            <div class="small fw-semibold"><?= htmlspecialchars($_SESSION['user']['fullname'] ?? '') ?></div>
                                            <div class="small text-light-50"><?= htmlspecialchars($currentRole) ?></div>
                                        </div>
                                    </div>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>">
                                        <i class="bi bi-house me-1"></i> Xem website
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="<?= APP_URL ?>/AuthController/logout">
                                        <i class="bi bi-box-arrow-right me-1"></i> Đăng xuất
                                    </a></li>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
        
        <main class="admin-main">
            <div class="container-fluid">
                <?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
                <?php if (!empty($_SESSION['flash_message'])): ?>
                    <?php 
                    $flashType = isset($_SESSION['flash_type']) ? $_SESSION['flash_type'] : 'info';
                    $flashIcons = [
                        'success' => 'bi-check-circle-fill',
                        'danger' => 'bi-exclamation-triangle-fill', 
                        'warning' => 'bi-exclamation-triangle-fill',
                        'info' => 'bi-info-circle-fill'
                    ];
                    $flashIcon = $flashIcons[$flashType] ?? 'bi-info-circle-fill';
                    ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show fade-in-up" role="alert">
                                <i class="bi <?= $flashIcon ?> me-2"></i>
                                <?= htmlspecialchars($_SESSION['flash_message']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                    <?php unset($_SESSION['flash_message']); unset($_SESSION['flash_type']); ?>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-12">
                        <div class="fade-in-up">
                            <?php
                              require_once "./views/Back_end/".$data["page"].".php";
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <footer class="admin-footer mt-auto">
            <div class="container-fluid text-center py-3">
                <div class="text-muted">
                    <small>
                        <i class="bi bi-c-circle me-1"></i>
                        <?= date('Y') ?> Hệ thống quản trị thương mại điện tử. 
                        <span class="d-none d-md-inline">Phát triển bởi đội ngũ IT.</span>
                    </small>
                </div>
            </div>
        </footer>
        
        <!-- SheetJS library for Excel export -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    </body>
</html>