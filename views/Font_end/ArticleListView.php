<?php
/**
 * View danh sách bài viết.
 * Nhiệm vụ:
 *  - Nhận mảng $data['articles'] từ Controller.
 *  - Hiển thị trạng thái rỗng nếu không có bài viết.
 *  - Render mỗi bài viết thành thẻ (card) với ảnh, tiêu đề, trích đoạn, ngày tạo.
 * Ghi chú bảo mật:
 *  - Dùng htmlspecialchars để tránh XSS với dữ liệu tiêu đề & nội dung.
 */
$articles = $data['articles'] ?? [];
?>

<!-- Modern Articles List View -->
<div class="articles-section">
  <div class="container">
    <!-- Header Section -->
    <div class="articles-header">
      <div class="header-content">
        <h2 class="articles-main-title">
          <i class="bi bi-newspaper"></i>
          Tin tức & bài viết
        </h2>
        <p class="articles-description">
          Khám phá những thông tin, kiến thức và xu hướng công nghệ mới nhất
        </p>
      </div>
      <div class="header-decoration">
        <div class="decoration-line"></div>
        <div class="decoration-dot"></div>
      </div>
    </div>

    <!-- Articles Grid -->
    <div class="articles-grid">
      <?php if (empty($articles)): ?>
        <!-- Empty State -->
        <div class="empty-state-container">
          <div class="empty-state-icon">
            <i class="bi bi-journal-x"></i>
          </div>
          <h3 class="empty-state-title">Chưa có bài viết nào</h3>
          <p class="empty-state-description">
            Hiện tại chưa có bài viết nào được đăng. Vui lòng quay lại sau!
          </p>
          <div class="empty-state-decoration">
            <div class="decoration-circle"></div>
            <div class="decoration-square"></div>
          </div>
        </div>
      <?php else: ?>
        <div class="row g-4">
          <?php foreach ($articles as $index => $a): 
            $delay = $index * 0.1;
            $img = $a['image'] ?? '';
            $url = $img ? APP_URL . '/public/images/' . rawurlencode($img) : '';
            $excerpt = mb_substr(strip_tags($a['content'] ?? ''), 0, 120) . '...';
            $date = date('d/m/Y', strtotime($a['created_at'] ?? 'now'));
          ?>
            <div class="col-lg-4 col-md-6">
              <article class="article-card" style="animation-delay: <?= $delay ?>s">
                <!-- Article Image -->
                <div class="article-image-wrapper">
                  <?php if ($img): ?>
                    <img src="<?= $url ?>" 
                         alt="<?= htmlspecialchars($a['title']) ?>" 
                         class="article-image"
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjI1MCIgdmlld0JveD0iMCAwIDQwMCAyNTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iMjUwIiBmaWxsPSIjRjFGM0Y0Ii8+CjxwYXRoIGQ9Ik0xNzUgMTAwSDE2MFY4NUgxNzVWMTAwWiIgZmlsbD0iIzlDQTNBRiIvPgo8cGF0aCBkPSJNMTc1IDEyMEgxNjBWMTA1SDE3NVYxMjBaIiBmaWxsPSIjOUNBM0FGIi8+CjxwYXRoIGQ9Ik0xNzUgMTQwSDE2MFYxMjVIMTc1VjE0MFoiIGZpbGw9IiM5Q0EzQUYiLz4KPHBhdGggZD0iTTI0MCAxMjBIMjI1VjEwNUgyNDBWMTIwWiIgZmlsbD0iIzlDQTNBRiIvPgo8cGF0aCBkPSJNMjQwIDE0MEgyMjVWMTI1SDI0MFYxNDBaIiBmaWxsPSIjOUNBM0FGIi8+CjxjaXJjbGUgY3g9IjIwMCIgY3k9IjE4MCIgcj0iMTUiIGZpbGw9IiNEMEQ2REUiLz4KPC9zdmc+'">
                  <?php else: ?>
                    <div class="article-image-placeholder">
                      <i class="bi bi-image"></i>
                    </div>
                  <?php endif; ?>
                  <a href="<?= APP_URL ?>/Article/detail/<?= (int) $a['id'] ?>" class="article-image-overlay" aria-label="Đọc bài viết">
                    <div class="overlay-content">
                      <span class="overlay-text">Đọc bài viết</span>
                      <i class="bi bi-arrow-right"></i>
                    </div>
                  </a>
                </div>

                <!-- Article Content -->
                <div class="article-content">
                  <div class="article-category">
                    <i class="bi bi-tag"></i>
                    <span>Tin tức</span>
                  </div>
                  
                  <h3 class="article-title">
                    <?= htmlspecialchars($a['title']) ?>
                  </h3>
                  
                  <p class="article-excerpt">
                    <?= htmlspecialchars($excerpt) ?>
                  </p>
                  
                  <div class="article-meta">
                    <div class="meta-item">
                      <i class="bi bi-calendar3"></i>
                      <span><?= $date ?></span>
                    </div>
                    <div class="meta-item">
                      <i class="bi bi-person"></i>
                      <span>Admin</span>
                    </div>
                    <div class="meta-item">
                      <i class="bi bi-clock"></i>
                      <span>3 phút đọc</span>
                    </div>
                  </div>
                  
                  <a href="<?= APP_URL ?>/Article/detail/<?= (int) $a['id'] ?>" class="read-more-btn">
                    <span>Đọc tiếp</span>
                    <i class="bi bi-arrow-right"></i>
                  </a>
                </div>
              </article>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- View-specific styles moved to external CSS -->
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/views/ArticleListView.css" />