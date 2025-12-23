<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!doctype html>
<html lang="vi">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PC Gear Shop</title>
    <link href="<?= APP_URL ?>/public/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= APP_URL ?>/public/css/shop-theme.css" rel="stylesheet" />
    <link href="<?= APP_URL ?>/public/css/public.css" rel="stylesheet" />
    <script defer src="<?= APP_URL ?>/public/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        if (typeof window !== 'undefined') {
            window.currentUser = {
                email: <?= json_encode($_SESSION['user']['email'] ?? '') ?>,
                fullname: <?= json_encode($_SESSION['user']['fullname'] ?? '') ?>,
                isLoggedIn: <?= !empty($_SESSION['user']) ? 'true' : 'false' ?>
            };
            console.log('[currentUser]', window.currentUser);
        }
    </script>
    <script defer src="<?= APP_URL ?>/public/js/chatbox.js"></script>
  </head>
  <body>
    <?php require __DIR__ . '/partials/frontend_nav.php'; ?>
    
        <?php if (!empty($_SESSION['flash_cart_message'])): ?>
            <?php $flashType = isset($_SESSION['flash_cart_type']) ? $_SESSION['flash_cart_type'] : 'success'; ?>
            <div class="container mt-3">
                <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['flash_cart_message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            <?php unset($_SESSION['flash_cart_message']); unset($_SESSION['flash_cart_type']); ?>
        <?php endif; ?>
        <main class="pb-5">
          <?php require_once __DIR__ . '/Font_end/'.$data['page'].'.php'; ?>
        </main>
        <!-- AJAX Add-to-cart handler & simple toast -->
        <div id="cart-toast-container" style="position:fixed;top:1rem;right:1rem;z-index:1200"></div>
        <script>
        (function(){
            function showToast(message, type){
                var container = document.getElementById('cart-toast-container');
                // remove existing toasts to avoid stacking
                while (container.firstChild) container.removeChild(container.firstChild);
                var alert = document.createElement('div');
                alert.className = 'alert alert-' + (type || 'info') + ' alert-dismissible';
                alert.style.minWidth = '220px';
                alert.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                container.appendChild(alert);
                setTimeout(function(){ try{ alert.remove(); } catch(e){} }, 4500);
            }

            document.addEventListener('click', function(ev){
                var el = ev.target.closest && ev.target.closest('a.add-to-cart, button.add-to-cart');
                if (!el) return;
                ev.preventDefault();
                // prefer data-url (for buttons) else href (for anchors)
                var url = el.getAttribute('data-url') || el.href;
                fetch(url, { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                .then(function(r){
                    // If server returned non-2xx (business failure), parse body and throw to catch handler
                    if (!r.ok) {
                        return r.json().then(function(body){ throw body; });
                    }
                    return r.json();
                })
                .then(function(data){
                    // Update any navbar badges that show cart count
                    var badges = document.querySelectorAll('.navbar .badge');
                    badges.forEach(function(b){ b.textContent = data.cartCount; });
                    // Also log prev/after on client console for quick debugging
                    if (typeof console !== 'undefined') {
                        console.log('add-to-cart response', data);
                    }

                    // Defensive client-side guard: if server returned no actual increase, treat as failure
                    var actuallyAdded = false;
                    if (typeof data.afterQty !== 'undefined' && typeof data.prevQty !== 'undefined') {
                        actuallyAdded = (parseInt(data.afterQty) > parseInt(data.prevQty));
                    } else {
                        actuallyAdded = !!data.success;
                    }

                    if (actuallyAdded) {
                        showToast(data.message || 'Đã thêm vào giỏ hàng', 'success');
                    } else {
                        var msg = data.message || 'Sản phẩm đã hết hoặc số lượng vượt quá tồn kho.';
                        // Chỉ thay thế thông điệp khi server KHÔNG gửi ra thông điệp rõ ràng
                        if (
                            typeof data.availableStock !== 'undefined' &&
                            (!data.message || data.message.indexOf('Số lượng hàng đã hết') === -1)
                        ) {
                            msg = 'Không thể thêm vào giỏ: hiện chỉ còn ' + data.availableStock + ' sản phẩm.';
                        }
                        showToast(msg, 'danger');
                    }
                }).catch(function(err){
                    // err may be an object returned by server (business error) or an Error
                    if (err && typeof err === 'object') {
                        var msg = err.message || (err.availableStock !== undefined ? ('Không thể thêm vào giỏ: hiện chỉ còn ' + err.availableStock + ' sản phẩm.') : 'Không thể thêm vào giỏ.');
                        // Nếu server đã gửi đúng thông điệp hết hàng thì giữ nguyên, không override bằng availableStock
                        if (err && err.message && err.message.indexOf('Số lượng hàng đã hết') !== -1) {
                            msg = err.message;
                        }
                        showToast(msg, 'danger');
                        console.log('add-to-cart error (server):', err);
                    } else {
                        showToast('Lỗi khi thêm vào giỏ (mạng).', 'danger');
                        console.error('add-to-cart error', err);
                    }
                });
            }, false);
        })();
        </script>
    <?php require __DIR__ . '/partials/frontend_footer.php'; ?>
  </body>
</html>