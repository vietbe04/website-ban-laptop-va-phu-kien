<?php
/**
 * Form đăng nhập (LoginView).
 * Chức năng:
 *  - Hiển thị form đăng nhập: email, mật khẩu, ghi nhớ email.
 *  - Tự động gợi ý email đã lưu (localStorage) theo thứ tự dùng gần nhất.
 * Bảo mật:
 *  - Email hiển thị qua htmlspecialchars.
 *  - Mật khẩu không log ra ngoài, autocomplete current-password.
 */
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h4 mb-4 text-center">Đăng nhập tài khoản</h2>
                    <?php $errorMessage = $data['error'] ?? null; ?>
                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($errorMessage) ?>
                        </div>
                    <?php endif; ?>
                    <?php $rememberedEmail = $_COOKIE['remember_email'] ?? ''; ?>
                    <form id="loginForm" action="<?= APP_URL; ?>/AuthController/login" method="POST" autocomplete="on">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input list="saved-emails" type="email" class="form-control" id="email" name="email" required placeholder="name@example.com" value="<?= htmlspecialchars($rememberedEmail) ?>" autocomplete="email">
                            <datalist id="saved-emails"></datalist>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••" autocomplete="current-password">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember" <?= $rememberedEmail ? 'checked' : '' ?>>
                            <label class="form-check-label" for="remember">Ghi nhớ email</label>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Đăng nhập</button>
                            <a href="<?= APP_URL; ?>/AuthController/forgotPassword" class="btn btn-link">Quên mật khẩu?</a>
                        </div>
                        <hr class="my-4">
                        <div class="text-center small">Chưa có tài khoản? <a href="<?= APP_URL; ?>/AuthController/Show">Đăng ký ngay</a></div>
                    </form>
                    <script>
                    (function(){
                        const storageKey='savedEmails';
                        const emailInput=document.getElementById('email');
                        const dataList=document.getElementById('saved-emails');
                        const rememberCb=document.getElementById('remember');
                        // Không còn nút xóa toàn bộ

                        function normalize(raw){
                            // Chuyển định dạng cũ (array string) -> array object {email,lastUsed}
                            if(!Array.isArray(raw)) return [];
                            if(raw.length && typeof raw[0]==='string'){
                                return raw.map(e=>({email:e,lastUsed:Date.now()}));
                            }
                            return raw.filter(o=>o && typeof o.email==='string').map(o=>({email:o.email,lastUsed: o.lastUsed || Date.now()}));
                        }
                        function loadList(){
                            let raw=[];try{raw=JSON.parse(localStorage.getItem(storageKey)||'[]');}catch(e){raw=[];}
                            return normalize(raw);
                        }
                        function persist(list){
                            localStorage.setItem(storageKey,JSON.stringify(list));
                        }
                        function render(list){
                            // Sắp xếp giảm dần theo lastUsed
                            const sorted=[...list].sort((a,b)=>b.lastUsed - a.lastUsed);
                            dataList.innerHTML='';
                            sorted.forEach(obj=>{const opt=document.createElement('option');opt.value=obj.email;dataList.appendChild(opt);});
                        }
                        function touchEmail(email){
                            if(!email) return;
                            let list=loadList();
                            const now=Date.now();
                            const idx=list.findIndex(o=>o.email===email);
                            if(idx>-1){ list[idx].lastUsed=now; }
                            else { list.push({email,lastUsed:now}); }
                            persist(list);
                            render(list);
                        }

                        // Submit: ghi nhận lần dùng
                        document.getElementById('loginForm').addEventListener('submit',function(){
                            if(rememberCb.checked){ touchEmail(emailInput.value.trim()); }
                        });
                        // Bỏ chức năng xóa danh sách theo yêu cầu
                        // Initial render
                        render(loadList());
                        // Change triggers to help password managers
                        emailInput.addEventListener('change',function(){ emailInput.dispatchEvent(new Event('input',{bubbles:true})); });
                    })();
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
