<footer class="bg-dark text-light mt-5 pt-4 pb-3 border-top">
  <div class="container">
    <div class="row g-4">
      <div class="col-12 col-md-4">
        <h5 class="fw-bold">PC Gear Shop</h5>
        <p class="small text-secondary mb-1">Chuyên laptop, PC, linh kiện & gaming gear.</p>
        <p class="small text-secondary">Hotline: 1900-0000<br>Email: support@pcgear.local</p>
      </div>
      <div class="col-6 col-md-2">
        <h6 class="text-uppercase small">Sản phẩm</h6>
        <ul class="list-unstyled small">
          <li><a class="link-light text-decoration-none" href="#">Laptop</a></li>
          <li><a class="link-light text-decoration-none" href="#">PC</a></li>
          <li><a class="link-light text-decoration-none" href="#">Card VGA</a></li>
          <li><a class="link-light text-decoration-none" href="#">RAM</a></li>
        </ul>
      </div>
      <div class="col-6 col-md-2">
        <h6 class="text-uppercase small">Hỗ trợ</h6>
        <ul class="list-unstyled small">
          <li><a class="link-light text-decoration-none" href="#">Hướng dẫn mua hàng</a></li>
        </ul>
      </div>
      <div class="col-12 col-md-4">
        <h6 class="text-uppercase small">Đăng ký nhận tin</h6>
        <form class="d-flex gap-2" onsubmit="event.preventDefault(); this.reset(); alert('Đã đăng ký!');">
          <input type="email" class="form-control form-control-sm" placeholder="Email của bạn" required>
          <button class="btn btn-sm btn-primary">Gửi</button>
        </form>
        <div class="mt-3 small text-secondary">&copy; <?= date('Y') ?> PC Gear Shop. All rights reserved.</div>
      </div>
    </div>
  </div>
</footer>