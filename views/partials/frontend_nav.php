<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$cartCount = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) { $cartCount = count($_SESSION['cart']); }
// Nếu return VNPAY đã đặt cờ ép xóa nhưng vì lý do nào đó cart được tái nạp lại từ DB ở nơi khác, đảm bảo dọn lại.
if (!empty($_SESSION['cart_force_cleared'])) {
  $_SESSION['cart'] = [];
  $cartCount = 0;
  unset($_SESSION['cart_force_cleared']);
  @error_log('[FRONTEND_NAV] enforced cart clear via cart_force_cleared flag session_id=' . session_id());
}
$categories = $data['categories'] ?? null;
if (!$categories) {
    try {
        require_once __DIR__ . '/../../app/DB.php';
        $pdo = (new DB())->Connect();
        $stmt = $pdo->prepare('SELECT maLoaiSP, tenLoaiSP FROM tblloaisp ORDER BY tenLoaiSP ASC');
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { $categories = []; }
}
$currentRoute = $_GET['url'] ?? '';
?>
<nav class="navbar navbar-expand-lg sticky-top modern-navbar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;">
  <div class="container-fluid px-4">
    <a class="navbar-brand fw-bold d-flex align-items-center brand-gradient" href="<?= APP_URL ?>/Home/index">
      <i class="bi bi-cpu-fill me-2 brand-icon"></i>
      <span class="brand-text">PC Gear Shop</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 nav-center">
        <li class="nav-item">
          <a class="nav-link nav-link-modern <?= ($currentRoute === 'Home/index' ? 'active' : '') ?>" href="<?= APP_URL ?>/Home/index">
            <i class="bi bi-house-door me-1"></i> Trang chủ
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-link-modern <?= (strpos($currentRoute,'Home/show')===0 ? 'active' : '') ?>" href="<?= APP_URL ?>/Home/show">
            <i class="bi bi-grid me-1"></i> Sản phẩm
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-link-modern <?= (strpos($currentRoute,'Article/')===0 ? 'active' : '') ?>" href="<?= APP_URL ?>/Article/list">
            <i class="bi bi-newspaper me-1"></i> Tin tức
          </a>
        </li>
        <li class="nav-item dropdown dropdown-modern">
          <a class="nav-link dropdown-toggle nav-link-modern" href="#" id="navProducts" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-list-ul me-1"></i> Danh mục
          </a>
          <ul class="dropdown-menu dropdown-menu-modern" aria-labelledby="navProducts">
            <?php if(!empty($categories)): foreach($categories as $c): ?>
              <li><a class="dropdown-item dropdown-item-modern" href="<?= APP_URL ?>/Home/category/<?= urlencode($c['maLoaiSP']) ?>">
                <i class="bi bi-chevron-right me-1"></i><?= htmlspecialchars($c['tenLoaiSP']) ?>
              </a></li>
            <?php endforeach; else: ?>
              <li><span class="dropdown-item text-muted">Chưa có loại</span></li>
            <?php endif; ?>
          </ul>
        </li>
      </ul>
      <form class="d-flex search-form-modern" method="get" action="<?= APP_URL ?>/Home/search" role="search">
        <div class="input-group search-input-group">
          <span class="input-group-text search-icon"><i class="bi bi-search"></i></span>
          <input class="form-control search-input" type="search" placeholder="Tìm kiếm sản phẩm..." aria-label="Tìm kiếm" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
          <button class="btn search-btn" type="submit">
            <i class="bi bi-arrow-right"></i>
          </button>
        </div>
      </form>
      <div class="ms-3 d-flex align-items-center gap-3 user-actions">
        <!-- Wishlist Button -->
        <a href="<?= APP_URL ?>/Wishlist/index" class="btn wishlist-nav-btn position-relative" title="Yêu thích">
          <i class="bi bi-heart"></i>
          <span class="wishlist-count badge" style="display: none;">0</span>
        </a>
        
        <!-- Compare Button -->
        <a href="<?= APP_URL ?>/Wishlist/compare" class="btn compare-nav-btn position-relative" title="So sánh">
          <i class="bi bi-arrow-left-right"></i>
          <span class="compare-count badge" style="display: none;">0</span>
        </a>
        
        <!-- Cart Button -->
        <a href="<?= APP_URL ?>/Home/order" class="btn cart-btn position-relative">
          <i class="bi bi-cart3"></i>
          <?php if($cartCount > 0): ?>
            <span class="cart-badge"><?= $cartCount ?></span>
          <?php endif; ?>
        </a>
        <?php if (!empty($_SESSION['user'])): ?>
          <div class="dropdown user-dropdown">
            <button class="btn user-btn dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle me-1"></i>
              <span class="d-none d-lg-inline"><?= htmlspecialchars($_SESSION['user']['fullname'] ?? 'Tài khoản') ?></span>
            </button>
            <ul class="dropdown-menu-end user-dropdown-menu dropdown-menu" aria-labelledby="userMenu">
              <?php $role = $_SESSION['user']['role'] ?? 'user'; if (in_array($role, ['admin','staff'])): ?>
              <li><a class="dropdown-item" href="<?= APP_URL ?>/Admin/show">
                <i class="bi bi-speedometer2 me-2"></i>Trang quản trị
              </a></li>
              <li><hr class="dropdown-divider"></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="<?= APP_URL ?>/Home/orderHistory">
                <i class="bi bi-bag me-2"></i>Đơn hàng của tôi
              </a></li>
              <li><a class="dropdown-item" href="<?= APP_URL ?>/AuthController/showAccount">
                <i class="bi bi-person me-2"></i>Tài khoản của tôi
              </a></li>
              <li><a class="dropdown-item" href="<?= APP_URL ?>/Feedback/create">
                <i class="bi bi-chat-left-text me-2"></i>Góp ý hệ thống
              </a></li>
              <li><a class="dropdown-item" href="<?= APP_URL ?>/Feedback/my">
                <i class="bi bi-card-checklist me-2"></i>Góp ý của tôi
              </a></li>
              <li><a class="dropdown-item" href="<?= APP_URL ?>/AuthController/logout">
                <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
              </a></li>
            </ul>
          </div>
        <?php else: ?>
          <div class="auth-buttons">
            <a href="<?= APP_URL ?>/AuthController/ShowLogin" class="btn login-btn">
              <i class="bi bi-box-arrow-in-right me-1"></i>
              <span class="d-none d-lg-inline">Đăng nhập</span>
            </a>
            <a href="<?= APP_URL ?>/AuthController/Show" class="btn register-btn">
              <i class="bi bi-person-plus me-1"></i>
              <span class="d-none d-lg-inline">Đăng ký</span>
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<script>
// Hover dropdown for Danh mục (desktop only)
document.addEventListener('DOMContentLoaded', function(){
  const trigger = document.getElementById('navProducts');
  if(!trigger) return;
  const dropdown = trigger.closest('.dropdown');
  const menu = dropdown ? dropdown.querySelector('.dropdown-menu') : null;
  if(!dropdown || !menu) return;
  let hoverIntentTimer;
  function open(){
    if(window.innerWidth < 992) return; // only desktop (lg breakpoint)
    clearTimeout(hoverIntentTimer);
    dropdown.classList.add('show');
    menu.classList.add('show');
    trigger.setAttribute('aria-expanded','true');
  }
  function close(){
    if(window.innerWidth < 992) return;
    hoverIntentTimer = setTimeout(()=>{
      dropdown.classList.remove('show');
      menu.classList.remove('show');
      trigger.setAttribute('aria-expanded','false');
    }, 120); // small delay to allow pointer move into menu
  }
  dropdown.addEventListener('mouseenter', open);
  dropdown.addEventListener('mouseleave', close);
  // Keyboard focus support
  trigger.addEventListener('focus', open);
  trigger.addEventListener('blur', close);
  // Touch devices keep default click behavior -> no interference
});

// Keep header static and consistent across pages (no scroll/load animations)

// Load wishlist and compare counts on page load
document.addEventListener('DOMContentLoaded', function() {
  // Load wishlist count
  fetch('<?= APP_URL ?>/Wishlist/add', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: 'product_id=0&action=count'
  })
  .then(res => res.json())
  .then(data => {
    if (data.count > 0) {
      updateWishlistCount(data.count);
    }
  })
  .catch(err => console.error('Failed to load wishlist count:', err));

  // Load compare count  
  fetch('<?= APP_URL ?>/Wishlist/addToCompare', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: 'product_id=0&action=count'
  })
  .then(res => res.json())
  .then(data => {
    if (data.count > 0) {
      updateCompareCount(data.count);
    }
  })
  .catch(err => console.error('Failed to load compare count:', err));
});
</script>