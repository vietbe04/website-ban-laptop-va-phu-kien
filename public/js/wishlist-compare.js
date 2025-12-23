/**
 * Wishlist & Compare Functions
 */

// Lấy base URL từ window location
const getBaseUrl = () => {
    const path = window.location.pathname;
    const parts = path.split('/');
    // Giả sử cấu trúc là /DQV/... hoặc /...
    return parts[1] === 'DQV' ? '/DQV' : '';
};

// Thêm vào wishlist
function toggleWishlist(productId, btn) {
    const baseUrl = getBaseUrl();
    const isInWishlist = btn.classList.contains('active');
    const url = isInWishlist ? 
        `${baseUrl}/Wishlist/remove` : 
        `${baseUrl}/Wishlist/add`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            btn.classList.toggle('active');
            const icon = btn.querySelector('i');
            if (icon) {
                icon.className = btn.classList.contains('active') ? 'bi bi-heart-fill' : 'bi bi-heart';
            } else {
                btn.innerHTML = btn.classList.contains('active') ? 
                    '<i class="bi bi-heart-fill"></i> Yêu thích' : 
                    '<i class="bi bi-heart"></i> Yêu thích';
            }
            
            // Cập nhật số lượng trong header
            updateWishlistCount(data.count);
            
            // Hiển thị thông báo
            showToast(data.message, 'success');
        } else {
            if (data.message.includes('đăng nhập')) {
                if (confirm(data.message + '. Chuyển đến trang đăng nhập?')) {
                    window.location.href = `${baseUrl}/AuthController/ShowLogin`;
                }
            } else {
                showToast(data.message, 'warning');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Có lỗi xảy ra', 'error');
    });
}

// Thêm vào so sánh
function toggleCompare(productId, btn) {
    const baseUrl = getBaseUrl();
    const isInCompare = btn.classList.contains('active');
    const url = isInCompare ? 
        `${baseUrl}/Wishlist/removeFromCompare` : 
        `${baseUrl}/Wishlist/addToCompare`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            btn.classList.toggle('active');
            updateCompareCount(data.count);
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'warning');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Có lỗi xảy ra', 'error');
    });
}

// Cập nhật số lượng wishlist trong header
function updateWishlistCount(count) {
    const badge = document.querySelector('.wishlist-count');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    }
}

// Cập nhật số lượng compare trong header
function updateCompareCount(count) {
    const badge = document.querySelector('.compare-count');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    }
}

// Hiển thị toast notification
function showToast(message, type = 'info') {
    // Tạo toast element nếu chưa có
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container';
        toastContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
        document.body.appendChild(toastContainer);
    }
    
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'warning'} alert-dismissible fade show`;
    toast.style.cssText = 'min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); animation: slideInRight 0.3s ease;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    toastContainer.appendChild(toast);
    
    // Tự động xóa sau 3 giây
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Animation CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .wishlist-btn, .compare-btn {
        position: relative;
        transition: all 0.3s ease;
    }
    
    .wishlist-btn.active, 
    button.active .bi-heart,
    button.active .bi-heart-fill,
    .btn.active .bi-heart,
    .btn.active .bi-heart-fill {
        color: #dc3545 !important;
    }
    
    .bi-heart-fill {
        color: #dc3545 !important;
    }
    
    .compare-btn.active,
    button.active .bi-arrow-left-right,
    .btn.active .bi-arrow-left-right {
        color: #0d6efd !important;
    }
`;
document.head.appendChild(style);

// Kiểm tra và đánh dấu các sản phẩm đã có trong wishlist/compare khi trang load
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = getBaseUrl();
    
    // Lấy danh sách wishlist
    fetch(`${baseUrl}/Wishlist/getUserWishlist`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.wishlist) {
            // Đánh dấu các nút wishlist đã có trong danh sách
            data.wishlist.forEach(productId => {
                const buttons = document.querySelectorAll(`[onclick*="toggleWishlist('${productId}'"`);
                buttons.forEach(btn => {
                    btn.classList.add('active');
                    const icon = btn.querySelector('i');
                    if (icon) {
                        icon.className = 'bi bi-heart-fill';
                    }
                });
            });
        }
    })
    .catch(err => console.log('Could not load wishlist status:', err));
    
    // Lấy danh sách compare
    fetch(`${baseUrl}/Wishlist/getCompareList`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.compare) {
            // Đánh dấu các nút compare đã có trong danh sách
            data.compare.forEach(productId => {
                const buttons = document.querySelectorAll(`[onclick*="toggleCompare('${productId}'"`);
                buttons.forEach(btn => {
                    btn.classList.add('active');
                });
            });
        }
    })
    .catch(err => console.log('Could not load compare status:', err));
});
