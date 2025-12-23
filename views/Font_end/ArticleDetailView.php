<?php
/**
 * Chi tiết bài viết (ArticleDetailView).
 * Nhiệm vụ:
 *  - Nhận $data['article'] từ Controller.
 *  - Hiển thị trạng thái "không tồn tại" nếu rỗng.
 *  - Render tiêu đề, meta (ngày, tác giả), ảnh (ẩn nếu lỗi), nội dung.
 * Bảo mật: dùng htmlspecialchars + nl2br tránh XSS.
 */
$article = $data['article'] ?? null;
?>

<!-- Modern Article Detail View -->
<div class="article-detail-section">
  <div class="container">
    <?php if(!$article): ?>
      <!-- Not Found State -->
      <div class="not-found-wrapper">
        <div class="not-found-content">
          <div class="not-found-icon">
            <i class="bi bi-journal-x"></i>
          </div>
          <h2 class="not-found-title">Bài viết không tồn tại</h2>
          <p class="not-found-description">
            Bài viết bạn tìm kiếm không tồn tại hoặc đã bị ẩn.
            Vui lòng kiểm tra lại đường dẫn hoặc quay lại danh sách bài viết.
          </p>
          <a href="<?= APP_URL ?>/Article/list" class="back-btn">
            <i class="bi bi-arrow-left"></i>
            Quay lại danh sách bài viết
          </a>
        </div>
        <div class="not-found-decoration">
          <div class="decoration-shape shape-1"></div>
          <div class="decoration-shape shape-2"></div>
          <div class="decoration-shape shape-3"></div>
        </div>
      </div>
    <?php else: ?>
      <!-- Article Content -->
      <article class="article-detail-content">
        <!-- Article Header -->
        <header class="article-detail-header">
          <div class="header-background">
            <div class="header-overlay"></div>
            <div class="header-content">
              <div class="article-breadcrumb">
                <a href="<?= APP_URL ?>/Article/list" class="breadcrumb-link">
                  <i class="bi bi-newspaper"></i>
                  Tin tức
                </a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Chi tiết</span>
              </div>
              
              <h1 class="article-title"><?= htmlspecialchars($article['title']) ?></h1>
              
              <div class="article-meta-info">
                <div class="meta-group">
                  <div class="meta-item">
                    <i class="bi bi-calendar3"></i>
                    <span><?= date('d/m/Y', strtotime($article['created_at'] ?? 'now')) ?></span>
                  </div>
                  <div class="meta-item">
                    <i class="bi bi-person"></i>
                    <span>Admin</span>
                  </div>
                  <div class="meta-item">
                    <i class="bi bi-eye"></i>
                    <span><?= rand(100, 1000) ?> lượt xem</span>
                  </div>
                  <div class="meta-item">
                    <i class="bi bi-clock"></i>
                    <span>5 phút đọc</span>
                  </div>
                </div>
                
                <div class="article-actions">
                  <button class="action-btn share-btn" onclick="shareArticle()">
                    <i class="bi bi-share"></i>
                    Chia sẻ
                  </button>
                  <button class="action-btn print-btn" onclick="printArticle()">
                    <i class="bi bi-printer"></i>
                    In
                  </button>
                </div>
              </div>
            </div>
          </div>
        </header>

        <!-- Article Image -->
        <?php $img = $article['image'] ?? ''; $url = APP_URL . '/public/images/' . rawurlencode($img); ?>
        <?php if ($img): ?>
          <div class="article-image-section">
            <div class="article-image-container">
              <img src="<?= $url ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="article-detail-image" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDgwMCA0MDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI4MDAiIGhlaWdodD0iNDAwIiBmaWxsPSIjRjFGM0Y0Ii8+CjxyZWN0IHg9IjIwMCIgeT0iMTUwIiB3aWR0aD0iNDAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iIzlDQTNBRiIgcng9IjEwIi8+CjxjaXJjbGUgY3g9IjQwMCIgY3k9IjMwMCIgcj0iMjAiIGZpbGw9IiNEMEQ2REUiLz4KPC9zdmc+'">
              <div class="image-overlay">
                <div class="overlay-content">
                  <i class="bi bi-image"></i>
                  <span>Ảnh bài viết</span>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <!-- Article Body -->
        <div class="article-body">
          <div class="article-content-wrapper">
            <div class="article-content">
              <?= nl2br(htmlspecialchars($article['content'])) ?>
            </div>
            
            <div class="article-tags">
              <div class="tags-label">
                <i class="bi bi-tags"></i>
                <span>Tags:</span>
              </div>
              <div class="tags-list">
                <span class="tag-item">Tin tức</span>
                <span class="tag-item">Công nghệ</span>
                <span class="tag-item">Thông tin</span>
              </div>
            </div>
          </div>

          <!-- Article Sidebar -->
          <aside class="article-sidebar">
            <div class="sidebar-section">
              <h3 class="sidebar-title">Chia sẻ bài viết</h3>
              <div class="share-buttons">
                <button class="share-btn facebook" onclick="shareToSocial('facebook')">
                  <i class="bi bi-facebook"></i>
                  Facebook
                </button>
                <button class="share-btn twitter" onclick="shareToSocial('twitter')">
                  <i class="bi bi-twitter"></i>
                  Twitter
                </button>
                <button class="share-btn linkedin" onclick="shareToSocial('linkedin')">
                  <i class="bi bi-linkedin"></i>
                  LinkedIn
                </button>
              </div>
            </div>

            <div class="sidebar-section">
              <h3 class="sidebar-title">Bài viết liên quan</h3>
              <div class="related-articles">
                <?php $related = $data['related'] ?? []; ?>
                <?php if (empty($related)): ?>
                  <div class="alert alert-light">Chưa có bài viết liên quan.</div>
                <?php else: ?>
                  <?php foreach ($related as $ra): ?>
                    <a class="related-item" href="<?= APP_URL ?>/Article/detail/<?= (int)$ra['id'] ?>">
                      <div class="related-image">
                        <i class="bi bi-newspaper"></i>
                      </div>
                      <div class="related-content">
                        <h4 class="related-title"><?= htmlspecialchars($ra['title']) ?></h4>
                        <span class="related-date"><?= date('d/m/Y', strtotime($ra['created_at'] ?? 'now')) ?></span>
                      </div>
                    </a>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </aside>
        </div>

        <!-- Article Footer -->
        <footer class="article-footer">
          <div class="footer-content">
            <div class="footer-actions">
              <a href="<?= APP_URL ?>/Article/list" class="back-btn">
                <i class="bi bi-arrow-left"></i>
                Quay lại danh sách bài viết
              </a>
              <div class="footer-actions-right">
                <?php
                $likesCount = $data['likesCount'] ?? 0;
                $userLiked = $data['userLiked'] ?? false;
                ?>
                <?php $likeClass = ($userLiked ? ' liked' : ''); ?>
                <button class="action-btn like-btn<?= $likeClass ?>" id="likeBtn" onclick="likeArticle(event)" aria-pressed="<?= $userLiked ? 'true' : 'false' ?>">
                  <i class="bi <?= $userLiked ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                  <span>Thích</span>
                  <span class="badge bg-light text-dark ms-2" id="likeCount"><?= (int)$likesCount ?></span>
                </button>
                <button class="action-btn comment-btn" onclick="scrollToComments()">
                  <i class="bi bi-chat-dots"></i>
                  Bình luận
                </button>
              </div>
            </div>
            
            <div class="article-navigation">
              <?php $prev = $data['prev'] ?? null; $next = $data['next'] ?? null; ?>
              <?php if ($prev): ?>
                <a class="nav-item nav-prev" href="<?= APP_URL ?>/Article/detail/<?= (int)$prev['id'] ?>" title="<?= htmlspecialchars($prev['title']) ?>">
                  <i class="bi bi-chevron-left"></i>
                  <span>Bài viết trước</span>
                </a>
              <?php else: ?>
                <div class="nav-item nav-prev disabled" title="Không có bài trước">
                  <i class="bi bi-chevron-left"></i>
                  <span>Bài viết trước</span>
                </div>
              <?php endif; ?>
              <?php if ($next): ?>
                <a class="nav-item nav-next" href="<?= APP_URL ?>/Article/detail/<?= (int)$next['id'] ?>" title="<?= htmlspecialchars($next['title']) ?>">
                  <span>Bài viết sau</span>
                  <i class="bi bi-chevron-right"></i>
                </a>
              <?php else: ?>
                <div class="nav-item nav-next disabled" title="Không có bài sau">
                  <span>Bài viết sau</span>
                  <i class="bi bi-chevron-right"></i>
                </div>
              <?php endif; ?>
            </div>
        
            <!-- Comments Section -->
            <section class="comments-section" id="comments">
              <h3 class="comments-title"><i class="bi bi-chat-dots"></i> Bình luận</h3>
              <?php $comments = $data['comments'] ?? []; ?>
              <div id="commentList">
                <?php if (empty($comments)): ?>
                  <div class="alert alert-light">Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</div>
                <?php else: ?>
                  <?php foreach ($comments as $c): ?>
                    <div class="comment-item">
                      <div class="comment-avatar"><i class="bi bi-person-circle"></i></div>
                      <div class="comment-body">
                        <div class="comment-header">
                          <span class="comment-name"><?= htmlspecialchars($c['fullname'] ?? 'Người dùng') ?></span>
                          <span class="comment-time"><?= date('d/m/Y H:i', strtotime($c['created_at'] ?? 'now')) ?></span>
                        </div>
                        <div class="comment-text"><?= nl2br(htmlspecialchars($c['content'] ?? '')) ?></div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
              <?php $isLoggedIn = !empty($_SESSION['user']); ?>
              <?php if ($isLoggedIn): ?>
                <form id="commentForm" class="comment-form" onsubmit="return submitComment(event)">
                  <div class="mb-3">
                    <label for="commentContent" class="form-label">Nội dung bình luận</label>
                    <textarea id="commentContent" name="content" class="form-control" rows="3" placeholder="Nhập bình luận..."></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send"></i> Gửi bình luận
                  </button>
                </form>
              <?php else: ?>
                <div class="alert alert-warning">
                  Vui lòng <a class="alert-link" href="<?= APP_URL ?>/AuthController/ShowLogin?next=<?= rawurlencode(APP_URL . '/Article/detail/' . (int)($article['id'] ?? 0)) ?>">đăng nhập</a> để bình luận.
                </div>
              <?php endif; ?>
            </section>
          </div>
        </footer>
      </article>
    <?php endif; ?>
  </div>
</div>

<!-- View-specific styles moved to external CSS -->
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/views/ArticleDetailView.css" />

<!-- JavaScript Functions -->
<script>
const LIKE_URL = '<?= rtrim(APP_URL,'/') ?>/index.php?url=Article/like/<?= (int)($article['id'] ?? 0) ?>';
const COMMENT_URL = '<?= rtrim(APP_URL,'/') ?>/index.php?url=Article/comment/<?= (int)($article['id'] ?? 0) ?>';

function shareArticle() {
  if (navigator.share) {
    navigator.share({
      title: '<?= htmlspecialchars($article['title'] ?? '') ?>',
      url: window.location.href
    });
  } else {
    // Fallback - copy to clipboard
    navigator.clipboard.writeText(window.location.href);
    alert('Đã sao chép đường dẫn bài viết!');
  }
}

function printArticle() {
  window.print();
}

function shareToSocial(platform) {
  const url = encodeURIComponent(window.location.href);
  const title = encodeURIComponent('<?= htmlspecialchars($article['title'] ?? '') ?>');
  
  let shareUrl = '';
  switch(platform) {
    case 'facebook':
      shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
      break;
    case 'twitter':
      shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
      break;
    case 'linkedin':
      shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
      break;
  }
  
  if (shareUrl) {
    window.open(shareUrl, '_blank', 'width=600,height=400');
  }
}

async function likeArticle(e) {
  const btn = document.getElementById('likeBtn');
  const icon = btn.querySelector('i');
  const countEl = document.getElementById('likeCount');
  try {
    const res = await fetch(LIKE_URL, { method: 'POST', credentials: 'same-origin' });
    const data = await res.json();
    if (!data.ok && data.error === 'not_logged_in') {
      window.location.href = '<?= APP_URL ?>/AuthController/ShowLogin?next=' + encodeURIComponent(window.location.href);
      return;
    }
    const liked = !!data.liked;
    const count = parseInt(data.count || 0, 10);
    countEl.textContent = count;
    btn.setAttribute('aria-pressed', liked ? 'true' : 'false');
    icon.classList.toggle('bi-heart', !liked);
    icon.classList.toggle('bi-heart-fill', liked);
    btn.classList.toggle('liked', liked);
  } catch (err) {
    console.error('Like error', err);
  }
}

function scrollToComments() {
  const el = document.getElementById('comments');
  if (el) { el.scrollIntoView({ behavior: 'smooth' }); }
}

async function submitComment(ev) {
  ev.preventDefault();
  const textarea = document.getElementById('commentContent');
  const content = (textarea.value || '').trim();
  if (content === '') { textarea.focus(); return false; }
  try {
    const formData = new FormData();
    formData.append('content', content);
    const res = await fetch(COMMENT_URL, { method: 'POST', body: formData, credentials: 'same-origin' });
    const data = await res.json();
    if (!data.ok && data.error === 'not_logged_in') {
      window.location.href = '<?= APP_URL ?>/AuthController/ShowLogin?next=' + encodeURIComponent(window.location.href);
      return false;
    }
    if (data.ok && Array.isArray(data.comments)) {
      const listEl = document.getElementById('commentList');
      listEl.innerHTML = data.comments.map(c => `
        <div class="comment-item">
          <div class="comment-avatar"><i class="bi bi-person-circle"></i></div>
          <div class="comment-body">
            <div class="comment-header">
              <span class="comment-name">${escapeHtml(c.fullname || 'Người dùng')}</span>
              <span class="comment-time">${formatDateTime(c.created_at)}</span>
            </div>
            <div class="comment-text">${escapeHtml(c.content || '').replace(/\n/g,'<br>')}</div>
          </div>
        </div>
      `).join('');
      textarea.value = '';
    }
  } catch (err) {
    console.error('Comment error', err);
  }
  return false;
}

function escapeHtml(str){
  return String(str).replace(/[&<>\"]/g, function (s) {
    const entityMap = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' };
    return entityMap[s];
  });
}
function formatDateTime(dt){
  try { const d = new Date(dt); return d.toLocaleString('vi-VN'); } catch { return dt; }
}

// Smooth scrolling for navigation
document.addEventListener('DOMContentLoaded', function() {
  // Add smooth scrolling behavior
  document.documentElement.style.scrollBehavior = 'smooth';
  
  // Add reading progress indicator
  window.addEventListener('scroll', function() {
    const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = (winScroll / height) * 100;
    
    // You can add a progress bar element if needed
    console.log('Reading progress: ' + scrolled + '%');
  });
});
</script>