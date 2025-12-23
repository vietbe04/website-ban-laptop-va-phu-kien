<?php
/**
 * Controller trang chủ và luồng đặt hàng phía người dùng
 * - Trang chủ, danh mục, tìm kiếm, chi tiết
 * - Quản lý giỏ hàng, thanh toán (COD/VNPAY)
 */
class Home extends Controller
{
    /**
     * Trang chủ: banner, khuyến mãi, bài viết, sản phẩm nổi bật
     */
    public function index()
    {
        $productModel = $this->model('AdProducModel');
        $articleModel = $this->model('ArticleModel');
        $promoModel = $this->model('AdKhuyenMai');
        $reviewModel = $this->model('ReviewModel');

        $allProducts = $productModel->search(['q'=>'']); // lấy toàn bộ (search với q rỗng trả về all)
        // Bổ sung phần trăm khuyến mãi cho tất cả sản phẩm
        foreach ($allProducts as &$p) {
            $promo = $promoModel->findWithDiscount($p['masp']);
            $p['phantram'] = $promo['phantram'] ?? 0;
        }
        // Sắp xếp theo createDate DESC đã ở trong search; chọn 8 sản phẩm đầu
        $featuredProducts = array_slice($allProducts, 0, 8);
        // Bổ sung số lượng đã bán và đánh giá cho featuredProducts
        $fpIds = array_map(function($p){ return $p['masp']; }, $featuredProducts);
        $soldMap = $productModel->getSoldCounts($fpIds);
        foreach ($featuredProducts as &$fp) {
            $fp['daban'] = $soldMap[$fp['masp']] ?? 0;
            $fp['sold_count'] = $soldMap[$fp['masp']] ?? 0;
            
            // Thêm rating
            $rating = $reviewModel->getAverageRating($fp['masp']);
            $fp['avg_rating'] = $rating['avg'] ?? 0;
            $fp['rating_count'] = $rating['count'] ?? 0;
        }

        // Bỏ phần "bán chạy" theo yêu cầu – không còn lấy danh sách riêng

        $articles = $articleModel->all(true); // chỉ bài viết publish
        $latestArticles = array_slice($articles, 0, 3);

        $promotionsRaw = $promoModel->getAllWithProduct();
        $now = date('Y-m-d');
        $activePromotions = array_filter($promotionsRaw, function($p) use ($now){
            if(empty($p['ngaybatdau']) || empty($p['ngayketthuc'])) return false;
            return $now >= $p['ngaybatdau'] && $now <= $p['ngayketthuc'];
        });
        $activePromotions = array_slice($activePromotions,0,6);

        $this->view('homePage', [
            'page' => 'HomeView',
            'featuredProducts' => $featuredProducts,
            // 'bestSellingProducts' removed per request
            'latestArticles' => $latestArticles,
            'activePromotions' => $activePromotions
        ]);
    }
    /**
     * Lịch sử đơn hàng cho người dùng đã đăng nhập
     */
    public function orderHistory()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . APP_URL . '/AuthController/ShowLogin');
            exit();
        }
        
        $orderModel = $this->model('OrderModel');
        $email = $_SESSION['user']['email'];
        
        // Phân trang
        $itemsPerPage = 15;
        $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($currentPage - 1) * $itemsPerPage;
        
        // Lấy tổng số đơn hàng
        $totalOrders = count($orderModel->getOrdersByEmail($email));
        $totalPages = ceil($totalOrders / $itemsPerPage);
        
        // Lấy danh sách đơn hàng có phân trang
        $orders = $orderModel->getOrdersByEmailWithPagination($email, $itemsPerPage, $offset);
        
        $this->view('homePage', [
            'page' => 'OrderHistoryView',
            'orders' => $orders,
            'pagination' => [
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'totalOrders' => $totalOrders,
                'itemsPerPage' => $itemsPerPage
            ]
        ]);
    }
    
    /**
     * Cập nhật trạng thái đơn hàng thành "Đã nhận hàng"
     */
    public function updateOrderStatus()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . APP_URL . '/AuthController/ShowLogin');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
            $requestedStatus = isset($_POST['new_status']) ? trim($_POST['new_status']) : 'dathanhtoan';
            $normalizedStatus = strtolower(preg_replace('/[\s_-]+/', '', $requestedStatus));
            $allowedStatuses = ['dathanhtoan'];
            if ($normalizedStatus === '' || !in_array($normalizedStatus, $allowedStatuses, true)) {
                $normalizedStatus = 'dathanhtoan';
            }
            
            if ($orderId > 0) {
                $orderModel = $this->model('OrderModel');
                
                // Kiểm tra đơn hàng có thuộc về user hiện tại không
                $order = $orderModel->getOrderById($orderId);
                
                if ($order && $order['user_email'] === $_SESSION['user']['email']) {
                    // Cập nhật trạng thái thành "Đã thanh toán" bằng order_code
                    $orderCode = $order['order_code'];
                    $currentInfo = $order['transaction_info'] ?? '';
                    
                    // Tách phần trạng thái và các thông tin khác
                    $parts = explode('|', $currentInfo);
                    $additionalInfo = isset($parts[1]) ? '|' . trim($parts[1]) : '';
                    
                    // Cập nhật trạng thái mới
                    $newInfo = $normalizedStatus . $additionalInfo;
                    
                    $orderModel->updateOrderStatus($orderCode, $newInfo);
                    
                    $_SESSION['success_message'] = 'Đã xác nhận nhận hàng và cập nhật trạng thái đơn thành công!';
                } else {
                    $_SESSION['error_message'] = 'Không tìm thấy đơn hàng hoặc bạn không có quyền cập nhật đơn hàng này.';
                }
            } else {
                $_SESSION['error_message'] = 'Mã đơn hàng không hợp lệ.';
            }
        }
        
        // Chuyển về trang lịch sử đơn hàng
        header('Location: ' . APP_URL . '/Home/orderHistory');
        exit();
    }
    // Lưu thông tin giao hàng, hóa đơn và chi tiết hóa đơn


    /**
     * Danh sách sản phẩm (phân trang) dùng chung ProductListView
     */
    public  function show()
    {
        $obj = $this->model("AdProducModel");
        $promoModel = $this->model("AdKhuyenMai");
        $typeModel = $this->model("AdProductTypeModel");
        
        // Lấy tham số trang hiện tại
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20; // Số sản phẩm mỗi trang
        $offset = ($page - 1) * $limit;
        
        // Lấy danh sách loại sản phẩm, màu sắc, dung lượng cho bộ lọc
        $categories = $typeModel->all("tblloaisp");
        $allColors = $obj->getAllColors();
        $allCapacities = $obj->getAllCapacities();
        
        // Lấy tổng số sản phẩm và dữ liệu phân trang
        $totalProducts = $obj->countAllProducts();
        $data = $obj->getProductsWithPagination($limit, $offset);
        
        // Bổ sung phần trăm khuyến mãi cho tất cả sản phẩm
        $reviewModel = $this->model('ReviewModel');
        $productIds = array_column($data, 'masp');
        $soldCounts = $obj->getSoldCounts($productIds);
        
        foreach ($data as &$p) {
            $promo = $promoModel->findWithDiscount($p['masp']);
            $p['phantram'] = $promo['phantram'] ?? 0;
            
            // Thêm rating và số lượng bán
            $rating = $reviewModel->getAverageRating($p['masp']);
            $p['avg_rating'] = $rating['avg'] ?? 0;
            $p['rating_count'] = $rating['count'] ?? 0;
            $p['sold_count'] = $soldCounts[$p['masp']] ?? 0;
        }
        
        // Tính toán thông tin phân trang
        $totalPages = ceil($totalProducts / $limit);
        
        // Tách view sản phẩm riêng: ProductListView để có thể sửa lại HomeView cho trang chủ
        $this->view("homePage", [
            "page" => "ProductListView", 
            "productList" => $data,
            'searchFilters' => ['q'=>'','price_min'=>'','price_max'=>'','color'=>'','capacity'=>'','in_stock'=>'','maLoaiSP'=>''],
            'sortBy' => 'newest',
            'categories' => $categories,
            'allColors' => $allColors,
            'allCapacities' => $allCapacities,
            "pagination" => [
                "currentPage" => $page,
                "totalPages" => $totalPages,
                "totalProducts" => $totalProducts,
                "limit" => $limit
            ]
        ]);
    }
    
    /**
     * Xử lý tìm kiếm sản phẩm
     * URL ví dụ: /Home/search?masp=SP001&tensp=áo&price_min=100000&price_max=500000
     */
    /**
     * Tìm kiếm sản phẩm nâng cao theo nhiều tiêu chí, lọc và sắp xếp
     */
    public function search()
    {
        // Thu thập tất cả các tham số lọc
        $filters = [];
        $filters['q'] = isset($_GET['q']) ? trim($_GET['q']) : '';
        $filters['maLoaiSP'] = isset($_GET['category']) ? trim($_GET['category']) : '';
        $filters['price_min'] = isset($_GET['price_min']) ? trim($_GET['price_min']) : '';
        $filters['price_max'] = isset($_GET['price_max']) ? trim($_GET['price_max']) : '';
        $filters['color'] = isset($_GET['color']) ? trim($_GET['color']) : '';
        $filters['capacity'] = isset($_GET['capacity']) ? trim($_GET['capacity']) : '';
        $filters['in_stock'] = isset($_GET['in_stock']) ? trim($_GET['in_stock']) : '';
        
        // Tham số sắp xếp: price_asc, price_desc, popularity, rating, newest
        $sortBy = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest';
        
        // Phân trang
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $obj = $this->model("AdProducModel");
        $promoModel = $this->model("AdKhuyenMai");
        $typeModel = $this->model("AdProductTypeModel");
        
        // Lấy danh sách loại sản phẩm, màu sắc, dung lượng cho bộ lọc
        $categories = $typeModel->all("tblloaisp");
        $allColors = $obj->getAllColors();
        $allCapacities = $obj->getAllCapacities();
        
        // Tìm kiếm nâng cao với lọc và sắp xếp
        $totalProducts = $obj->countProductsAdvanced($filters);
        $results = $obj->searchWithPaginationAdvanced($filters, $sortBy, $limit, $offset);
        
        // Bổ sung phần trăm khuyến mãi
        $reviewModel = $this->model('ReviewModel');
        $productIds = array_column($results, 'masp');
        $soldCounts = $obj->getSoldCounts($productIds);
        
        foreach ($results as &$p) {
            $promo = $promoModel->findWithDiscount($p['masp']);
            $p['phantram'] = $promo['phantram'] ?? 0;
            
            // Thêm rating và số lượng bán
            $rating = $reviewModel->getAverageRating($p['masp']);
            $p['avg_rating'] = $rating['avg'] ?? 0;
            $p['rating_count'] = $rating['count'] ?? 0;
            $p['sold_count'] = $soldCounts[$p['masp']] ?? 0;
        }
        
        // Lấy tên danh mục hiện tại nếu có
        $currentCategoryName = '';
        if (!empty($filters['maLoaiSP'])) {
            foreach ($categories as $cat) {
                if ($cat['maLoaiSP'] === $filters['maLoaiSP']) {
                    $currentCategoryName = $cat['tenLoaiSP'];
                    break;
                }
            }
        }
        
        $totalPages = ceil($totalProducts / $limit);

        $this->view("homePage", [
            "page" => "ProductListView", 
            "productList" => $results, 
            'searchFilters' => $filters,
            'sortBy' => $sortBy,
            'categories' => $categories,
            'allColors' => $allColors,
            'allCapacities' => $allCapacities,
            'currentCategoryName' => $currentCategoryName,
            "pagination" => [
                "currentPage" => $page,
                "totalPages" => $totalPages,
                "totalProducts" => $totalProducts,
                "limit" => $limit
            ]
        ]);
    }

    /**
     * Danh sách sản phẩm theo mã loại (có phân trang)
     * @param string $maLoaiSP
     */
    public function category($maLoaiSP)
    {
        $obj = $this->model('AdProducModel');
        $promoModel = $this->model('AdKhuyenMai');
        $typeModel = $this->model('AdProductTypeModel');
        
        // Lấy tên danh mục để hiển thị tiêu đề động
        try {
            $catRow = $typeModel->find('tblloaisp', $maLoaiSP);
            $currentCategoryName = $catRow['tenLoaiSP'] ?? $maLoaiSP;
        } catch (Exception $e) {
            $currentCategoryName = $maLoaiSP; // fallback nếu lỗi
        }
        
        // Lấy danh sách loại sản phẩm, màu sắc, dung lượng cho bộ lọc
        $categories = $typeModel->all("tblloaisp");
        $allColors = $obj->getAllColors();
        $allCapacities = $obj->getAllCapacities();
        
        // Lấy tham số trang hiện tại
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20; // Số sản phẩm mỗi trang
        $offset = ($page - 1) * $limit;
        
        // Lấy tổng số sản phẩm theo loại và dữ liệu phân trang
        $totalProducts = $obj->countProductsByCategory($maLoaiSP);
        $results = $obj->getByCategoryWithPagination($maLoaiSP, $limit, $offset);
        
        // Bổ sung phần trăm khuyến mãi cho tất cả sản phẩm
        $reviewModel = $this->model('ReviewModel');
        $productIds = array_column($results, 'masp');
        $soldCounts = $obj->getSoldCounts($productIds);
        
        foreach ($results as &$p) {
            $promo = $promoModel->findWithDiscount($p['masp']);
            $p['phantram'] = $promo['phantram'] ?? 0;
            
            // Thêm rating và số lượng bán
            $rating = $reviewModel->getAverageRating($p['masp']);
            $p['avg_rating'] = $rating['avg'] ?? 0;
            $p['rating_count'] = $rating['count'] ?? 0;
            $p['sold_count'] = $soldCounts[$p['masp']] ?? 0;
        }
        
        // Tính toán thông tin phân trang
        $totalPages = ceil($totalProducts / $limit);
        
        $this->view('homePage', [
            'page' => 'ProductListView',
            'productList' => $results,
            'currentCategory' => $maLoaiSP,
            'currentCategoryName' => $currentCategoryName,
            'searchFilters' => ['q'=>'','price_min'=>'','price_max'=>'','color'=>'','capacity'=>'','in_stock'=>'','maLoaiSP'=>$maLoaiSP],
            'sortBy' => 'newest',
            'categories' => $categories,
            'allColors' => $allColors,
            'allCapacities' => $allCapacities,
            "pagination" => [
                "currentPage" => $page,
                "totalPages" => $totalPages,
                "totalProducts" => $totalProducts,
                "limit" => $limit
            ]
        ]);
    }
    /**
     * Chi tiết sản phẩm + đánh giá + biến thể
     * @param string $masp
     */
    public function detail($masp)
    {
        $discountModel = $this->model("AdKhuyenMai");
        $data = $discountModel->findWithDiscount($masp);

        if (!empty($data["phantram"])) {
            $data["gia_khuyenmai"] = $data["giaXuat"] * (1 - $data["phantram"] / 100);
        } else {
            $data["gia_khuyenmai"] = $data["giaXuat"];
        }

        // Reviews
        $reviewModel = $this->model('ReviewModel');
        $avg = $reviewModel->getAverageRating($masp);
        $reviews = $reviewModel->getApprovedByProduct($masp);
        $canReview = false; $already = false;
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!empty($_SESSION['user'])){
            $email = $_SESSION['user']['email'];
            $orderModel = $this->model('OrderModel');
            $canReview = $orderModel->userPurchasedProductPaid($email, $masp);
            $already = $this->model('ReviewModel')->hasUserReviewed($email,$masp);
        }

        // Biến thể sản phẩm
        try {
            $variantModel = $this->model('ProductVariantModel');
            $allVariants = $variantModel->getByProduct($masp);
        } catch (Exception $e) {
            $allVariants = [];
        }
        $colorVariants = array_values(array_filter($allVariants, function($v){ return ($v['variant_type'] ?? '') === 'color' && (int)$v['active']===1; }));
        $capacityVariants = array_values(array_filter($allVariants, function($v){ return ($v['variant_type'] ?? '') === 'capacity' && (int)$v['active']===1; }));

        $this->view("homePage", [
            "page" => "DetailView",
            "product" => $data,
            "reviews" => $reviews,
            "avgRating" => $avg,
            "canReview" => $canReview && !$already,
            "alreadyReviewed" => $already,
            "colorVariants" => $colorVariants,
            "capacityVariants" => $capacityVariants
        ]);
    }

    /**
     * Thêm vào giỏ hàng (hỗ trợ biến thể dung lượng/màu sắc, AJAX hoặc điều hướng)
     * @param string $masp
     */
    public function addtocard($masp)
{
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($isAjax && function_exists('ob_start')) { @ob_start(); @ini_set('display_errors',0); }
    if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

    $obj = $this->model('AdProducModel');
    $objKM = $this->model('AdKhuyenMai');
    $objCart = $this->model('CartModel');
    $email = $_SESSION['user']['email'] ?? null;

    $addAmount = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
    if ($addAmount < 1) { $addAmount = 1; }

    // Lấy biến thể màu & dung lượng (có thể đồng thời)
    $colorVariantId = isset($_POST['color_variant_id']) && $_POST['color_variant_id'] !== '' ? (int)$_POST['color_variant_id'] : null;
    $colorVariantName = isset($_POST['color_variant_name']) ? trim($_POST['color_variant_name']) : null;
    $capacityVariantId = isset($_POST['capacity_variant_id']) && $_POST['capacity_variant_id'] !== '' ? (int)$_POST['capacity_variant_id'] : null;
    $capacityVariantName = isset($_POST['capacity_variant_name']) ? trim($_POST['capacity_variant_name']) : null;
    $capacityVariantPrice = isset($_POST['capacity_variant_price']) && $_POST['capacity_variant_price'] !== '' ? (float)$_POST['capacity_variant_price'] : null;
    // Legacy single variant fields (fallback nếu chỉ chọn 1 loại)
    $variantIdLegacy = isset($_POST['variant_id']) && $_POST['variant_id'] !== '' ? (int)$_POST['variant_id'] : null;
    $variantTypeLegacy = isset($_POST['variant_type']) ? trim($_POST['variant_type']) : null;

    $availableStock = (int)$obj->getStock($masp);
    $currentQtyInSession = isset($_SESSION['cart'][$masp]) ? (int)$_SESSION['cart'][$masp]['qty'] : 0;

    // Chỉ dùng capacityVariantId để kiểm tra DB vì hiện DB chưa có cột màu riêng -> hạn chế: khác màu + cùng dung lượng sẽ gộp
    if ($email) {
        $cartDB = $objCart->getCartItem($email, $masp, ($capacityVariantId ?: $variantIdLegacy), $colorVariantId);
        $prevQty = $cartDB ? (int)$cartDB['soluong'] : 0;
    } else {
        $prevQty = $currentQtyInSession;
    }

    $remaining = $availableStock - $prevQty;
    if ($remaining <= 0 || $addAmount > $remaining) {
        $limitMsg = $remaining <= 0 ? 'Số lượng hàng đã hết' : 'Số lượng yêu cầu vượt tồn kho (còn ' . $remaining . ')';
        if ($isAjax) {
            if (function_exists('ob_get_level') && @ob_get_level()) { @ob_end_clean(); }
            $cartCountDistinct = !empty($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
            $cartCountQty = 0; foreach($_SESSION['cart'] as $ci){ $cartCountQty += (int)($ci['qty'] ?? 0); }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => $limitMsg,
                'cartCount' => $cartCountDistinct,
                'cartCountQty' => $cartCountQty,
                'availableStock' => $availableStock,
                'prevQty' => $prevQty,
                'afterQty' => $prevQty,
                'requested' => $addAmount
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
        $_SESSION['flash_cart_message'] = $limitMsg;
        $_SESSION['flash_cart_type'] = 'danger';
        $back = $_SERVER['HTTP_REFERER'] ?? (APP_URL . '/Home');
        header('Location: ' . $back); exit();
    }

    // Lấy dữ liệu sản phẩm & khuyến mãi
    $data = $obj->find('tblsanpham', $masp);
    $km = $objKM->findWithDiscount($masp);
    // Use promotion table only; do not rely on product-level khuyenmai column
    $phantram = !empty($km['phantram']) ? (float)$km['phantram'] : 0;
    // Áp dụng override giá nếu là biến thể dung lượng
    $basePrice = $data['giaXuat'];
    if ($capacityVariantId && $capacityVariantPrice !== null) {
        $basePrice = $capacityVariantPrice; // giá gốc cho biến thể dung lượng
    }
    $giaSauKM = $basePrice * (1 - $phantram / 100);

    // Cập nhật session
    // Tạo key session: nếu có variantId giữ riêng từng biến thể, nếu chưa triển khai toàn bộ update/delete cho composite key thì vẫn hoạt động cho trường hợp 1 biến thể.
    // Tạo key phiên: masp#capacity#color (0 nếu null)
    $sessionKey = $masp . '#' . ($capacityVariantId ?: 0) . '#' . ($colorVariantId ?: 0);
    if (isset($_SESSION['cart'][$sessionKey])) {
        $newSessionQty = $_SESSION['cart'][$sessionKey]['qty'] + $addAmount;
        if ($newSessionQty > $availableStock) { $newSessionQty = $availableStock; }
        $_SESSION['cart'][$sessionKey]['qty'] = $newSessionQty;
    } else {
        $initialQty = min($addAmount, $availableStock);
        $_SESSION['cart'][$sessionKey] = [
            'qty' => $initialQty,
            'masp' => $data['masp'],
            'tensp' => $data['tensp'],
            'hinhanh' => $data['hinhanh'],
            'giaxuat' => $basePrice, // lưu giá gốc đã override nếu dung lượng
            'phantram' => $phantram,
            'capacity_variant_id' => $capacityVariantId,
            'capacity_variant_name' => $capacityVariantName,
            'color_variant_id' => $colorVariantId,
            'color_variant_name' => $colorVariantName,
        ];
    }

    // Đồng bộ DB nếu đã đăng nhập
    $addedSuccessfully = false;
    if ($email) {
        // Lưu cả dung lượng & màu vào DB
        $newQty = $objCart->addOrUpdateCart($email, $masp, ($capacityVariantId ?: $variantIdLegacy), $colorVariantId, $addAmount, $giaSauKM);
        $afterQty = (int)$newQty;
        if ($afterQty > $prevQty) { $addedSuccessfully = true; }
        $_SESSION['cart'][$sessionKey]['qty'] = $afterQty;
    } else {
        $afterQty = $_SESSION['cart'][$sessionKey]['qty'];
        if ($afterQty > $prevQty) { $addedSuccessfully = true; }
    }

    @error_log(json_encode([
        'action' => 'addtocard','masp'=>$masp,'email'=>$email,'requested'=>$addAmount,
        'available'=>$availableStock,'prev'=>$prevQty,'after'=>$afterQty,'success'=>$addedSuccessfully
    ], JSON_UNESCAPED_UNICODE));

    if ($isAjax) {
        if (function_exists('ob_get_level') && @ob_get_level()) { @ob_end_clean(); }
        $cartCountDistinct = count($_SESSION['cart']);
        $cartCountQty = 0; foreach($_SESSION['cart'] as $ci){ $cartCountQty += (int)($ci['qty'] ?? 0); }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $addedSuccessfully,
            'message' => $addedSuccessfully ? ('Đã thêm ' . $addAmount . ' vào giỏ hàng') : 'Không thể thêm số lượng yêu cầu',
            'cartCount' => $cartCountDistinct,
            'cartCountQty' => $cartCountQty,
            'prevQty' => $prevQty,
            'afterQty' => $afterQty,
            'availableStock' => $availableStock,
            'requested' => $addAmount
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    if ($addedSuccessfully) {
        $_SESSION['flash_cart_message'] = 'Đã thêm ' . $addAmount . ' vào giỏ hàng';
        $_SESSION['flash_cart_type'] = 'success';
    } else {
        $_SESSION['flash_cart_message'] = 'Không thể thêm số lượng yêu cầu';
        $_SESSION['flash_cart_type'] = 'danger';
    }
    $back = $_SERVER['HTTP_REFERER'] ?? (APP_URL . '/Home');
    header('Location: ' . $back); exit();
}



    /**
     * Xóa một mục khỏi giỏ hàng (theo khóa tổng hợp masp#capacity#color)
     * @param string $masp Mã sản phẩm hoặc khóa tổng hợp đã mã hóa
     */
    public function delete($masp)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $objCart = $this->model("CartModel");
        $email = $_SESSION['user']['email'] ?? null;
        // Parse composite key (masp#capacity#color) hoặc fallback nếu chỉ mã sp
        $maspParam = rawurldecode($masp);
        $parts = explode('#', $maspParam);
        $maspReal = $parts[0];
        $capacityVariantId = isset($parts[1]) ? (int)$parts[1] : 0;
        $colorVariantId = isset($parts[2]) ? (int)$parts[2] : 0;
        $compositeKey = $maspReal . '#' . $capacityVariantId . '#' . $colorVariantId;

        // Debug tập hợp key hiện có
        $existingKeys = array_keys($_SESSION['cart'] ?? []);
        @error_log('[DELETE] requested=' . $compositeKey . ' existing=' . json_encode($existingKeys, JSON_UNESCAPED_UNICODE));

        $removed = false; $removedItem = null;
        // Case 1: key trùng hoàn toàn -> xóa trực tiếp
        if (isset($_SESSION['cart'][$compositeKey])) {
            $removedItem = $_SESSION['cart'][$compositeKey];
            unset($_SESSION['cart'][$compositeKey]);
            $removed = true;
        }

        // Case 2: nếu không thấy, duyệt tìm item matching từng phần biến thể
        if (!$removed) {
            foreach ($_SESSION['cart'] as $k => $item) {
                $matchMasP = ($item['masp'] ?? '') === $maspReal;
                $matchCapacity = (int)($item['capacity_variant_id'] ?? 0) === $capacityVariantId;
                $matchColor = (int)($item['color_variant_id'] ?? 0) === $colorVariantId;
                if ($matchMasP && $matchCapacity && $matchColor) { $removedItem = $item; unset($_SESSION['cart'][$k]); $removed = true; break; }
            }
        }

        // Case 3: nếu tham số chỉ có mã sp (không biến thể) => xóa tất cả biến thể của mã đó
        if (!$removed && ($capacityVariantId === 0 && $colorVariantId === 0)) {
            foreach ($_SESSION['cart'] as $k => $item) {
                if (($item['masp'] ?? '') === $maspReal) { $removedItem = $item; unset($_SESSION['cart'][$k]); $removed = true; }
            }
        }

        @error_log('[DELETE] removed=' . ($removed ? 'yes':'no') . ' final_keys=' . json_encode(array_keys($_SESSION['cart'] ?? []), JSON_UNESCAPED_UNICODE));

        // Đồng bộ DB nếu đăng nhập
        if ($email) {
            // Nếu lấy được item đã xóa dùng chính biến thể từ item cho chắc chắn, fallback dùng tham số
            if ($removedItem) {
                $objCart->deleteCartItem(
                    $email,
                    $maspReal,
                    ($removedItem['capacity_variant_id'] ?? null) ?: ($capacityVariantId ?: null),
                    ($removedItem['color_variant_id'] ?? null) ?: ($colorVariantId ?: null)
                );
            } else {
                $objCart->deleteCartItem($email, $maspReal, $capacityVariantId ?: null, $colorVariantId ?: null);
            }
            $dbCart = $objCart->getCartByEmail($email);
            // Chuẩn hóa lại key composite cho session
            $normalized = [];
            foreach ($dbCart as $itemVal) {
                $ckey = ($itemVal['masp'] ?? '') . '#' . ($itemVal['capacity_variant_id'] ?? 0) . '#' . ($itemVal['color_variant_id'] ?? 0);
                $normalized[$ckey] = $itemVal;
            }
            if (!empty($_SESSION['cart_force_cleared'])) {
                @error_log('[CART_REPOP DELETE] skip repop due to cart_force_cleared flag email=' . $email);
            } else {
                $_SESSION['cart'] = $normalized;
                @error_log('[CART_REPOP DELETE] repop after delete email=' . $email . ' count=' . count($normalized));
            }
            $updatedCart = $normalized;
        } else {
            $updatedCart = $_SESSION['cart'] ?? [];
        }

        // Redirect về trang order sau khi xóa thành công
        $_SESSION['cart_success'] = $removed ? "Đã xoá sản phẩm khỏi giỏ hàng" : "Không tìm thấy sản phẩm cần xoá";
        header("Location: " . APP_URL . "/Home/order");
        exit();
    }

    /**
     * Cập nhật số lượng/ghi chú giỏ hàng; kiểm tra tồn kho; tính lại tổng
     */
    public function update()
  {
      $objCart = $this->model("CartModel");
      $objKM = $this->model("AdKhuyenMai");
      $objProduct = $this->model("AdProducModel");

      $email = $_SESSION['user']['email'] ?? null;
      $quantities = $_POST['qty'] ?? [];
      $noteCart = $_POST['note_cart'] ?? [];

      // Logged-in user: update DB variant-aware
      if ($email) {
          $qtyWarnings = [];
          foreach ($quantities as $key => $qty) {
              // Key format: masp#capacityVariantId#colorVariantId
              $parts = explode('#', $key);
              $maspReal = $parts[0];
              // parts may contain '0' to mean null in session keys; convert 0 -> null so DB NULL matches correctly
              $capacityVariantId = isset($parts[1]) ? ((int)$parts[1] ?: null) : null;
              $colorVariantId = isset($parts[2]) ? ((int)$parts[2] ?: null) : null;
              $qty = max(1, (int)$qty);
              $available = (int)$objProduct->getStock($maspReal);
              if ($qty > $available) {
                  $qtyWarnings[] = "Sản phẩm $maspReal chỉ còn $available trong kho. Số lượng đã được điều chỉnh.";
                  $qty = $available > 0 ? $available : 1;
              }
              $objCart->updateCartQty($email, $maspReal, $capacityVariantId, $colorVariantId, $qty);
          }
          foreach ($noteCart as $key => $note) {
              $parts = explode('#', $key);
              $maspReal = $parts[0];
              $capacityVariantId = isset($parts[1]) ? ((int)$parts[1] ?: null) : null;
              $colorVariantId = isset($parts[2]) ? ((int)$parts[2] ?: null) : null;
              $noteVal = $note == 1 ? 1 : 0;
              $objCart->updateNoteCart($email, $maspReal, $capacityVariantId, $colorVariantId, $noteVal);
          }
          $cart = $objCart->getCartByEmail($email);
          // Recompute discounts & total (only counting note_cart=1 items)
          $tongtien = 0;
          foreach ($cart as $ck => &$item) {
              $km = $objKM->findWithDiscount($item['masp']);
              $phantram = !empty($km['phantram']) ? (float)$km['phantram'] : ($item['phantram'] ?? 0);
              $giaSauKM = $item['giaxuat'] * (1 - $phantram / 100);
              if (!empty($item['note_cart']) && (int)$item['note_cart'] === 1) {
                  $tongtien += $giaSauKM * $item['qty'];
              }
              $item['phantram'] = $phantram;
          }
          // Chuẩn hoá key composite sau cập nhật
          $normalized = [];
          foreach ($cart as $itemVal) {
              $ckey = ($itemVal['masp'] ?? '') . '#' . ($itemVal['capacity_variant_id'] ?? 0) . '#' . ($itemVal['color_variant_id'] ?? 0);
              $normalized[$ckey] = $itemVal;
          }
          if (!empty($_SESSION['cart_force_cleared'])) {
              @error_log('[CART_UPDATE] skip DB repop due to cart_force_cleared flag email=' . $email);
          } else {
              $_SESSION['cart'] = $normalized;
              @error_log('[CART_UPDATE] repop DB cart email=' . $email . ' count=' . count($normalized));
          }
          $msg = "Giỏ hàng đã được cập nhật thành công!";
          if (!empty($qtyWarnings)) { $msg .= ' ' . implode(' ', $qtyWarnings); }
          $this->view("homePage", [
              "page" => "OrderView",
              "listProductOrder" => $normalized,
              "amount" => $tongtien,
              "success" => $msg
          ]);
          return;
      }

      // Guest user: session-only variant-aware updates
      $qtyWarnings = [];
      foreach ($quantities as $key => $qty) {
          $parts = explode('#', $key);
          $maspReal = $parts[0];
          if (!isset($_SESSION['cart'][$key])) { continue; }
          $qty = max(1, (int)$qty);
          $available = (int)$objProduct->getStock($maspReal);
          if ($available <= 0) {
              unset($_SESSION['cart'][$key]);
              $qtyWarnings[] = "Sản phẩm $maspReal đã hết hàng và đã bị loại khỏi giỏ.";
              continue;
          }
          if ($qty > $available) {
              $qtyWarnings[] = "Sản phẩm $maspReal chỉ còn $available trong kho. Số lượng đã được điều chỉnh.";
              $qty = $available;
          }
          $_SESSION['cart'][$key]['qty'] = $qty;
      }
      foreach ($noteCart as $key => $note) {
          if (isset($_SESSION['cart'][$key])) {
              $_SESSION['cart'][$key]['note_cart'] = ($note == 1 ? 1 : 0);
          }
      }
      $cart = $_SESSION['cart'] ?? [];
      $tongtien = 0;
      foreach ($cart as $ck => &$item) {
          $km = $objKM->findWithDiscount($item['masp']);
          $phantram = !empty($km['phantram']) ? (float)$km['phantram'] : ($item['phantram'] ?? 0);
          $giaSauKM = $item['giaxuat'] * (1 - $phantram / 100);
          if (!empty($item['note_cart']) && (int)$item['note_cart'] === 1) {
              $tongtien += $giaSauKM * $item['qty'];
          }
          $item['phantram'] = $phantram;
      }
      $_SESSION['cart'] = $cart;
      $msg = "Giỏ hàng đã được cập nhật thành công!";
      if (!empty($qtyWarnings)) { $msg .= ' ' . implode(' ', $qtyWarnings); }
      $this->view("homePage", [
          "page" => "OrderView",
          "listProductOrder" => $cart,
          "amount" => $tongtien,
          "success" => $msg
      ]);
  }




    /**
     * Hiển thị trang giỏ hàng/đặt hàng, đồng bộ DB nếu đã đăng nhập
     */
    public function order()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        $objKM = $this->model("AdKhuyenMai");
        $objCart = $this->model("CartModel");
        $objProduct = $this->model("AdProducModel");
        $reviewModel = $this->model('ReviewModel');
        $email = $_SESSION['user']['email'] ?? null;
        
        // Lấy thông báo success từ session (sau khi xóa sản phẩm)
        $successMessage = $_SESSION['cart_success'] ?? null;
        if ($successMessage) {
            unset($_SESSION['cart_success']);
        }

        if ($email) {
            $cartDB = $objCart->getCartByEmail($email);
            $cartSession = $_SESSION['cart'] ?? [];
            $needSync = false;
            foreach ($cartSession as $cartKey => $item) {
                $dbKey = $item['masp'] . '#' . ($item['capacity_variant_id'] ?? 0) . '#' . ($item['color_variant_id'] ?? 0);
                if (!isset($cartDB[$dbKey])) { $needSync = true; break; }
            }
            if ($needSync) {
                foreach ($cartSession as $cartKey => $item) {
                    $maspReal = $item['masp'];
                    $dbKey = $maspReal . '#' . ($item['capacity_variant_id'] ?? 0) . '#' . ($item['color_variant_id'] ?? 0);
                    if (!isset($cartDB[$dbKey])) {
                        $giaSauKM = $item['giaxuat'] * (1 - $item['phantram'] / 100);
                        $objCart->addOrUpdateCart(
                            $email,
                            $maspReal,
                            ($item['capacity_variant_id'] ?? null),
                            ($item['color_variant_id'] ?? null),
                            $item['qty'],
                            $giaSauKM
                        );
                    }
                }
                $cartDB = $objCart->getCartByEmail($email);
            }
            $tongtien = 0;
            $cartProductIds = [];
            foreach ($cartDB as $ck => &$item) {
                if (!isset($item['note_cart'])) { $item['note_cart'] = 0; }
                $km = $objKM->findWithDiscount($item['masp']);
                $phantram = !empty($km['phantram']) ? (float)$km['phantram'] : ($item['phantram'] ?? 0);
                $giaSauKM = $item['giaxuat'] * (1 - $phantram / 100);
                $item['phantram'] = $phantram;
                if ((int)$item['note_cart'] === 1) { $tongtien += $giaSauKM * $item['qty']; }
                $cartProductIds[] = $item['masp'];
            }
            // Chuẩn hoá key composite masp#capacity#color cho giỏ khi đăng nhập
            $normalized = [];
            foreach ($cartDB as $itemVal) {
                $ckey = ($itemVal['masp'] ?? '') . '#' . ($itemVal['capacity_variant_id'] ?? 0) . '#' . ($itemVal['color_variant_id'] ?? 0);
                if (isset($normalized[$ckey])) {
                    $normalized[$ckey]['qty'] += (int)($itemVal['qty'] ?? 0);
                } else {
                    $normalized[$ckey] = $itemVal;
                }
            }
            if (!empty($_SESSION['cart_force_cleared'])) {
                @error_log('[ORDER_VIEW] skip repop due to cart_force_cleared flag email=' . $email . ' db_count=' . count($normalized));
            } else {
                $_SESSION['cart'] = $normalized;
                @error_log('[ORDER_VIEW] repop cart email=' . $email . ' db_count=' . count($normalized));
            }
            
            // Lấy sản phẩm đề xuất dựa trên danh mục của sản phẩm trong giỏ
            $recommendedProducts = $this->getRecommendedProducts($cartProductIds, 8);
            
            $this->view("homePage", [
                "page" => "OrderView",
                "listProductOrder" => $normalized,
                "amount" => $tongtien,
                "recommendedProducts" => $recommendedProducts,
                "success" => $successMessage
            ]);
            return;
        }

        // Guest session cart
        $cartSession = $_SESSION['cart'] ?? [];
        $tongtien = 0; $qtyWarnings = [];
        $cartProductIds = [];
        foreach ($cartSession as $cartKey => &$item) {
            if (!isset($item['note_cart'])) { $item['note_cart'] = 0; }
            $available = (int)$objProduct->getStock($item['masp']);
            if ($available <= 0) { unset($cartSession[$cartKey]); $qtyWarnings[] = "Sản phẩm {$item['masp']} đã hết hàng và đã bị loại khỏi giỏ."; continue; }
            if ($item['qty'] > $available) { $qtyWarnings[] = "Sản phẩm {$item['masp']} chỉ còn $available trong kho. Số lượng đã được điều chỉnh."; $item['qty'] = $available; }
            $km = $objKM->findWithDiscount($item['masp']);
            $phantram = !empty($km['phantram']) ? (float)$km['phantram'] : ($item['phantram'] ?? 0);
            $giaSauKM = $item['giaxuat'] * (1 - $phantram / 100);
            $item['phantram'] = $phantram;
            if ((int)$item['note_cart'] === 1) { $tongtien += $giaSauKM * $item['qty']; }
            $cartProductIds[] = $item['masp'];
        }
        if (!empty($_SESSION['cart_force_cleared'])) {
            // Guest flow: if force cleared set, empty cart entirely
            @error_log('[ORDER_VIEW GUEST] force cleared flag present, emptying guest cart (prev count=' . count($cartSession) . ')');
            $_SESSION['cart'] = [];
            unset($cartSession); $cartSession = [];
        } else {
            $_SESSION['cart'] = $cartSession;
        }
        
        // Lấy sản phẩm đề xuất cho khách
        $recommendedProducts = $this->getRecommendedProducts($cartProductIds, 8);
        
        $msg = !empty($qtyWarnings) ? implode(' ', $qtyWarnings) : null;
        $finalSuccessMsg = $successMessage ?: $msg;
        $this->view("homePage", [
            "page" => "OrderView",
            "listProductOrder" => $cartSession,
            "amount" => $tongtien,
            "success" => $finalSuccessMsg,
            "recommendedProducts" => $recommendedProducts
        ]);
    }
    
    /**
     * Lấy sản phẩm đề xuất dựa trên sản phẩm trong giỏ hàng
     * @param array $cartProductIds Danh sách mã sản phẩm trong giỏ
     * @param int $limit Số lượng sản phẩm đề xuất
     * @return array
     */
    private function getRecommendedProducts($cartProductIds, $limit = 8)
    {
        if (empty($cartProductIds)) {
            return [];
        }
        
        $objProduct = $this->model("AdProducModel");
        $promoModel = $this->model("AdKhuyenMai");
        $reviewModel = $this->model('ReviewModel');
        
        // Lấy danh mục của các sản phẩm trong giỏ
        $categories = [];
        foreach ($cartProductIds as $masp) {
            $product = $objProduct->find('tblsanpham', $masp);
            if ($product && !empty($product['maLoaiSP'])) {
                $categories[] = $product['maLoaiSP'];
            }
        }
        $categories = array_unique($categories);
        
        if (empty($categories)) {
            return [];
        }
        
        // Lấy sản phẩm cùng danh mục, loại trừ sản phẩm đã có trong giỏ
        $recommended = [];
        foreach ($categories as $category) {
            $products = $objProduct->getByCategory($category);
            foreach ($products as $p) {
                if (!in_array($p['masp'], $cartProductIds) && !isset($recommended[$p['masp']])) {
                    $recommended[$p['masp']] = $p;
                }
            }
        }
        
        // Giới hạn số lượng và thêm thông tin khuyến mãi, đánh giá
        $recommended = array_slice($recommended, 0, $limit);
        $productIds = array_column($recommended, 'masp');
        $soldCounts = $objProduct->getSoldCounts($productIds);
        
        foreach ($recommended as &$p) {
            $promo = $promoModel->findWithDiscount($p['masp']);
            $p['phantram'] = $promo['phantram'] ?? 0;
            
            $rating = $reviewModel->getAverageRating($p['masp']);
            $p['avg_rating'] = $rating['avg'] ?? 0;
            $p['rating_count'] = $rating['count'] ?? 0;
            $p['sold_count'] = $soldCounts[$p['masp']] ?? 0;
        }
        
        return array_values($recommended);
    }

    /**
     * Chi tiết đơn hàng của người dùng
     * @param int|string $id
     */
    public function orderDetail($id)
    {
        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($id);
        $orderItems = $orderModel->getOrderItems($id);
        $this->view('homePage', [
            'page' => 'OrderDetailView',
            'order' => $order,
            'orderItems' => $orderItems
        ]);
    }





    // Xử lý đặt hàng: chỉ cho phép khi đã đăng nhập
    // public function checkout() {
    //     $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    //     // If user not logged in
    //     if (!isset($_SESSION['user'])) {
    //         if (!empty($cart)) {
    //             // Has cart but not logged in -> go to login page
    //             header('Location: ' . APP_URL . '/AuthController/ShowLogin');
    //             exit();
    //         }
    //         // No cart and not logged in -> go to homepage
    //         header('Location: ' . APP_URL . '/Home');
    //         exit();
    //     }

    //     // User is logged in
    //     if (empty($cart)) {
    //         // Logged in but cart empty -> go to homepage or show order page with empty list
    //         $this->view("homePage", [
    //             "page" => "OrderView",
    //             "listProductOrder" => [],
    //             "success" => "Giỏ hàng trống!"
    //         ]);
    //         return;
    //     }
    //     header('Location: ' . APP_URL . '/Home');
    //     exit();
    // }
    /**
     * Luồng chọn phương thức thanh toán: chuyển tới CheckoutInfoView hoặc yêu cầu đăng nhập
     */
    public function checkout()
    {
        // Lấy thông tin giỏ hàng và đăng nhập
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $isLoggedIn = isset($_SESSION['user']);

        // 1. Đã đăng nhập và có sản phẩm trong giỏ hàng
        if ($isLoggedIn && !empty($cart)) {
            $this->view("homePage", [
                "page" => "CheckoutInfoView"
            ]);
            return;
        }

        // 2. Chưa đăng nhập nhưng có sản phẩm trong giỏ hàng
        if (!$isLoggedIn && !empty($cart)) {
            // Redirect to login and request the checkout flow after login
            header('Location: ' . APP_URL . '/AuthController/ShowLogin?next=checkout');
            exit();
        }

        // 3. Đã đăng nhập nhưng giỏ hàng trống
        if ($isLoggedIn && empty($cart)) {
            $this->view("homePage", [
                "page" => "OrderView",
                "listProductOrder" => [],
                "success" => "Giỏ hàng trống!"
            ]);
            return;
        }

        // 4. Chưa đăng nhập và giỏ hàng trống
        if (!$isLoggedIn && empty($cart)) {
            header('Location: ' . APP_URL . '/Home');
            exit();
        }

        // Trường hợp không xác định
        header('Location: ' . APP_URL . '/Home');
        exit();
    }




    /**
     * Lưu đơn hàng: tính giảm giá (ngưỡng/coupon), tạo đơn + chi tiết, trừ tồn, điều hướng VNPAY/COD
     */
    public function checkoutSave()
    {
        if (!isset($_SESSION['user'])) {
            // If a guest posts to checkoutSave, force login first and then continue to checkout
            header('Location: ' . APP_URL . '/AuthController/ShowLogin?next=checkout');
            exit();
        }

        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart)) {
            $this->view("homePage", [
                "page" => "OrderView",
                "listProductOrder" => [],
                "success" => "Giỏ hàng trống!"
            ]);
            return;
        }

        $user = $_SESSION['user'];
        
        // Lấy thông tin giao hàng từ POST hoặc user session
        $receiver = isset($_POST['receiver']) ? trim($_POST['receiver']) : (isset($user['fullname']) ? trim($user['fullname']) : '');
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : (isset($user['phone']) ? trim($user['phone']) : '');
        $address = isset($_POST['address']) ? trim($_POST['address']) : (isset($user['address']) ? trim($user['address']) : '');
        $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'cod';
        
        // Nếu chọn nhận tại cửa hàng, không cần phí vận chuyển
        if ($payment_method === 'store') {
            $shippingSpeed = 'store_pickup';
            $shippingFee = 0;
            $shippingSpeedText = 'Nhận tại cửa hàng';
        } else {
            $shippingSpeed = isset($_POST['shipping_speed']) ? $_POST['shipping_speed'] : 'slow';
            $shippingFee = ($shippingSpeed === 'fast') ? 50000 : 30000;
            $shippingSpeedText = ($shippingSpeed === 'fast') ? 'Giao hàng nhanh' : 'Giao hàng tiêu chuẩn';
        }

        if ($receiver === '' || $phone === '' || $address === '') {
            echo '<div class="alert alert-danger">Vui lòng nhập đầy đủ thông tin giao hàng!</div>';
            $this->view("homePage", ["page" => "CheckoutInfoView"]);
            return;
        }

        $orderModel = $this->model("OrderModel");
        $orderDetailModel = $this->model("OrderDetailModel");
        $couponModel = $this->model("CouponModel");
        $orderCode = 'HD' . time();
        // Thiết lập trạng thái ban đầu dựa trên phương thức thanh toán
        if ($payment_method == 'store') {
            $transaction_info = "chonhantaicuahang";
        } else {
            $transaction_info = "chothanhtoan";
        }
        $created_at = date('Y-m-d H:i:s');
        error_log("DEBUG checkoutSave: payment_method=$payment_method, transaction_info=$transaction_info");

        // ✅ Tính tổng tiền ban đầu theo khuyến mãi sản phẩm (chưa áp mã coupon + chưa áp giảm theo ngưỡng)
        $cartSubtotal = 0;
        foreach ($cart as $item) {
            $phantram = isset($item['phantram']) ? (float)$item['phantram'] : 0;
            $giaSauKM = $item['giaxuat'] * (1 - $phantram / 100);
            $thanhtien = $giaSauKM * $item['qty'];
            $cartSubtotal += $thanhtien;
        }

        // Áp dụng giảm giá theo ngưỡng (threshold discount) trước khi xét coupon
        $thresholdInfo = $this->computeThresholdDiscount($cartSubtotal);
        $totalAfterThreshold = $thresholdInfo['total_after'];

        // Tổng để dùng cho coupon (min_total của coupon thường xét theo subtotal trước hay sau? => dùng subtotal gốc để tránh mất điều kiện sau giảm ngưỡng)
        $couponValidationBase = $cartSubtotal;
        $totalAmount = $totalAfterThreshold; // khởi tạo tổng chạy tiếp xử lý coupon

        // Handle coupon application (apply only when user clicks Apply button)
        if (isset($_POST['apply_coupon'])) {
            $code = isset($_POST['coupon_code']) ? trim($_POST['coupon_code']) : '';
            $coupon = $couponModel->findByCode($code);
            // Validate dựa trên tổng trước threshold để không làm người dùng mất điều kiện tối thiểu vì đã được giảm tự động
            list($valid, $msg) = $couponModel->validateCoupon($coupon, $couponValidationBase);
            $couponResult = ['valid' => $valid, 'message' => $msg];
            if ($valid) {
                $applied = $couponModel->applyDiscount($coupon, $totalAmount);
                // store in session so it's applied when placing order
                $_SESSION['coupon'] = [
                    'id' => $coupon['id'],
                    'code' => $coupon['code'],
                    'discount' => $applied['discount'],
                    'total_after' => $applied['total_after']
                ];
                $couponResult['discount'] = $applied['discount'];
                // Update totalAmount sau coupon
                $totalAmount = $applied['total_after'];
            }

            // re-render checkout page with coupon feedback
            $this->view("homePage", [
                "page" => "CheckoutInfoView",
                "couponResult" => $couponResult,
                "couponCode" => $code,
                "cartTotal" => $cartSubtotal,
                "thresholdInfo" => $thresholdInfo,
                "discountedTotal" => $totalAmount
            ]);
            return;
        }

        // Nếu có coupon trong session, áp dụng giảm giá và tăng usage
        $appliedCoupon = $_SESSION['coupon'] ?? null;
        if ($appliedCoupon) {
            // Ensure coupon still valid at finalization and recalc discount
            $couponRow = $couponModel->findByCode($appliedCoupon['code']);
            list($validFinal, $msgFinal) = $couponModel->validateCoupon($couponRow, $couponValidationBase);
            if ($validFinal) {
                $discInfo = $couponModel->applyDiscount($couponRow, $totalAmount);
                $discountAmount = $discInfo['discount'];
                $totalAmount = $discInfo['total_after'];
                // increment usage
                $couponModel->incrementUsage($couponRow['id']);
                // annotate transaction_info (thêm trước sau threshold)
                $transaction_info .= " | coupon:" . $couponRow['code'] . " (-" . $discountAmount . ")";
            } else {
                // coupon became invalid — remove from session
                unset($_SESSION['coupon']);
            }
        }

        // Ghi chú giảm theo ngưỡng vào transaction_info nếu có áp dụng
        if ($thresholdInfo['discount'] > 0) {
            $transaction_info .= " | threshold:" . $thresholdInfo['percent'] . "%(-" . $thresholdInfo['discount'] . ")";
        }
        
        // Thêm phí vận chuyển vào tổng tiền (trừ khi nhận tại cửa hàng)
        if ($shippingFee > 0) {
            $totalAmount += $shippingFee;
            $transaction_info .= " | shipping:" . $shippingSpeedText . "(+" . $shippingFee . ")";
        } else {
            $transaction_info .= " | pickup:Nhận tại cửa hàng(+0)";
        }
        error_log("DEBUG checkoutSave: final transaction_info=$transaction_info");

        // Với VNPAY: CHƯA tạo đơn trước, chỉ tạo sau khi thanh toán thành công.
        // Với COD: tạo đơn ngay và hiển thị kết quả.

        // ✅ Lưu thông tin vào session để VNPAY sử dụng/hoàn tất
        $_SESSION['orderCode'] = $orderCode;
        $_SESSION['totalAmount'] = $totalAmount;
        $_SESSION['shipping_snapshot'] = [
            'receiver' => $receiver,
            'phone' => $phone,
            'address' => $address,
            'shipping_speed' => $shippingSpeed,
            'shipping_fee' => $shippingFee,
            'transaction_info' => $transaction_info,
            'created_at' => $created_at,
        ];
        $_SESSION['cart_snapshot'] = $cart;
        // Clear applied coupon after placing or preparing payment
        if (isset($_SESSION['coupon'])) unset($_SESSION['coupon']);

        if ($payment_method == 'vnpay') {
            // VNPAY: Không tạo đơn trước. Chuyển sang trang thanh toán, đơn sẽ được tạo ở vnpay_return khi thành công.
            header('Location: ' . APP_URL . '/vnpay_php/vnpay_pay.php');
            exit();
        } elseif ($payment_method == 'cod' || $payment_method == 'store') {
            // COD hoặc thanh toán tại cửa hàng: Tạo đơn ngay
            $userId = isset($user['id']) ? (int)$user['id'] : 0;
            $orderId = $orderModel->createOrderWithShipping(
                $orderCode,
                $totalAmount,
                $userId,
                $user['email'],
                $receiver,
                $phone,
                $address,
                $created_at,
                $transaction_info
            );
            foreach ($cart as $item) {
                $phantram = isset($item['phantram']) ? (float)$item['phantram'] : 0;
                $giaSauKM = $item['giaxuat'] * (1 - $phantram / 100);
                $thanhtien = $giaSauKM * $item['qty'];
                $orderDetailModel->addOrderDetail(
                    $orderId,
                    $item['masp'],
                    ($item['capacity_variant_id'] ?? null),
                    ($item['capacity_variant_name'] ?? null),
                    ($item['color_variant_id'] ?? null),
                    ($item['color_variant_name'] ?? null),
                    $item['qty'],
                    $item['giaxuat'],
                    $giaSauKM,
                    $thanhtien,
                    ($item['hinhanh'] ?? ''),
                    ($item['tensp'] ?? '')
                );
            }
            // finalize (stock & clear cart), but do not mark paid
            $this->finalizeOrderAndClearCart($orderCode, $cart, $_SESSION['user']['email'], false);
            
            error_log("DEBUG checkoutSave: Order created with code=$orderCode, transaction_info=$transaction_info");
            
            // Gửi email xác nhận đơn hàng
            $this->sendOrderConfirmationEmail($user['email'], $orderCode, $totalAmount, $cart, $receiver, $phone, $address, $shippingSpeedText, $shippingFee, $payment_method, $transaction_info);
            
            $paymentMethodText = ($payment_method == 'store') ? 'Thanh toán tại cửa hàng' : 'COD';
            $this->view("homePage", [
                "page" => "OrderView",
                "listProductOrder" => [],
                "success" => "Đặt hàng thành công! Mã hóa đơn: $orderCode. Phương thức: $paymentMethodText. Email xác nhận đã được gửi." . ($thresholdInfo['discount']>0 ? " (Giảm thêm " . number_format($thresholdInfo['discount'],0,',','.') . "₫ theo ngưỡng)" : '')
            ]);
        }
    }


    // Xử lý khi VNPAY redirect về
    /**
     * Xử lý phản hồi từ VNPAY, xác minh chữ ký và hiển thị kết quả
     */
    public function vnpayReturn()
    {
        // Lấy tất cả params VNPAY trả về
        $data = $_GET;
        //$vnp_HashSecret = defined('VNP_HASH_SECRET') ? VNP_HASH_SECRET : '';
        $vnp_HashSecret = "1CZQFRQ2K1MKJDKFG4LMUQ1NQU07Z003";
        if (isset($data['vnp_SecureHash'])) {
            $secureHash = $data['vnp_SecureHash'];
            unset($data['vnp_SecureHash']);
            unset($data['vnp_SecureHashType']);
            ksort($data);
            $hashData = '';
            foreach ($data as $key => $value) {
                if (($key !== 'vnp_SecureHash') && ($key !== 'vnp_SecureHashType')) {
                    $hashData .= $key . '=' . $value . '&';
                }
            }
            $hashData = rtrim($hashData, '&');
            $calculatedHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

            if ($calculatedHash === $secureHash) {
                // signature ok -> kiểm tra mã trả về
                $vnp_ResponseCode = isset($_GET['vnp_ResponseCode']) ? $_GET['vnp_ResponseCode'] : '';
                $vnp_TxnRef = isset($_GET['vnp_TxnRef']) ? $_GET['vnp_TxnRef'] : '';

                if ($vnp_ResponseCode === '00') {
                    // Thanh toán thành công -> đảm bảo đã trừ tồn kho & dọn cart (phòng trường hợp user đi trực tiếp vào link vnpay_return mà chưa xử lý)
                    // Nếu trước đó checkoutSave đã xử lý rồi thì các thao tác này sẽ an toàn idempotent (không trừ âm vì decrementStock kiểm tra)
                    $orderCode = $vnp_TxnRef;
                    // Không có giỏ trong session lúc này cũng vẫn xử lý dựa vào order_details
                    $this->finalizeOrderAndClearCart($orderCode);
                    
                    error_log("VNPAY SUCCESS: OrderCode=$orderCode");
                    error_log("Session user: " . (isset($_SESSION['user']['email']) ? $_SESSION['user']['email'] : 'NOT SET'));
                    error_log("Session shipping_snapshot: " . (isset($_SESSION['shipping_snapshot']) ? 'YES' : 'NO'));
                    error_log("Session cart_snapshot: " . (isset($_SESSION['cart_snapshot']) ? 'YES' : 'NO'));
                    
                    // Gửi email xác nhận đơn hàng
                    if (isset($_SESSION['user']['email']) && isset($_SESSION['shipping_snapshot']) && isset($_SESSION['cart_snapshot'])) {
                        $shipping = $_SESSION['shipping_snapshot'];
                        $cart = $_SESSION['cart_snapshot'];
                        $totalAmount = $_SESSION['totalAmount'] ?? 0;
                        
                        $shippingSpeedText = 'Giao hàng tiêu chuẩn';
                        if (isset($shipping['shipping_speed'])) {
                            if ($shipping['shipping_speed'] === 'fast') {
                                $shippingSpeedText = 'Giao hàng nhanh';
                            } elseif ($shipping['shipping_speed'] === 'store_pickup') {
                                $shippingSpeedText = 'Nhận tại cửa hàng';
                            }
                        }
                        
                        error_log("Calling sendOrderConfirmationEmail for VNPAY order");
                        $emailSent = $this->sendOrderConfirmationEmail(
                            $_SESSION['user']['email'],
                            $orderCode,
                            $totalAmount,
                            $cart,
                            $shipping['receiver'] ?? '',
                            $shipping['phone'] ?? '',
                            $shipping['address'] ?? '',
                            $shippingSpeedText,
                            $shipping['shipping_fee'] ?? 0,
                            'vnpay',
                            $shipping['transaction_info'] ?? ''
                        );
                        error_log("Email sent result: " . ($emailSent ? 'SUCCESS' : 'FAILED'));
                    } else {
                        error_log("Cannot send email - missing session data. Attempting to retrieve from database...");
                        
                        // Fallback: Lấy thông tin từ database nếu session không có
                        $orderModel = $this->model('OrderModel');
                        $order = $orderModel->getOrderByCode($orderCode);
                        
                        if ($order) {
                            $orderDetailModel = $this->model('OrderDetailModel');
                            $orderDetails = $orderDetailModel->getOrderDetailsByOrderId($order['id']);
                            
                            // Tạo cart array từ order details
                            $cart = [];
                            foreach ($orderDetails as $detail) {
                                $cart[] = [
                                    'tensp' => $detail['product_name'] ?? '',
                                    'qty' => $detail['quantity'] ?? 1,
                                    'giaxuat' => $detail['price'] ?? 0,
                                    'phantram' => 0, // Không có thông tin khuyến mãi từ DB
                                    'hinhanh' => $detail['product_image'] ?? ''
                                ];
                            }
                            
                            // Parse shipping info từ transaction_info nếu có
                            $txnInfo = $order['transaction_info'] ?? '';
                            $shippingFee = 30000; // Default
                            $shippingSpeedText = 'Giao hàng tiêu chuẩn';
                            
                            if (strpos($txnInfo, 'shipping:') !== false) {
                                preg_match('/shipping:[^(]+\(([0-9]+)\)/', $txnInfo, $matches);
                                if (isset($matches[1])) {
                                    $shippingFee = (int)$matches[1];
                                }
                                if (strpos($txnInfo, 'nhanh') !== false) {
                                    $shippingSpeedText = 'Giao hàng nhanh';
                                }
                            } elseif (strpos($txnInfo, 'pickup:') !== false) {
                                $shippingFee = 0;
                                $shippingSpeedText = 'Nhận tại cửa hàng';
                            }
                            
                            error_log("Sending email from database data");
                            $emailSent = $this->sendOrderConfirmationEmail(
                                $order['user_email'],
                                $orderCode,
                                $order['total_amount'],
                                $cart,
                                $order['receiver'] ?? '',
                                $order['phone'] ?? '',
                                $order['address'] ?? '',
                                $shippingSpeedText,
                                $shippingFee,
                                'vnpay',
                                $txnInfo
                            );
                            error_log("Email sent from DB result: " . ($emailSent ? 'SUCCESS' : 'FAILED'));
                        } else {
                            error_log("Order not found in database: $orderCode");
                        }
                    }
                    
                    $message = "Thanh toán VNPAY thành công. Mã đơn: $vnp_TxnRef. Email xác nhận đã được gửi.";
                } else {
                    $message = "Thanh toán VNPAY không thành công. Mã trả về: " . htmlspecialchars($vnp_ResponseCode);
                }
            } else {
                $message = 'Chu ky khong hop le.';
            }
        } else {
            $message = 'Tham so chua duoc truyen.';
        }

        $this->view('homePage', [
            'page' => 'OrderView',
            'listProductOrder' => [],
            'success' => $message
        ]);
    }

    /**
     * Hoàn tất đơn hàng: trừ tồn kho từng product theo order_details và xóa cart (DB + session)
     * Có thể gọi nhiều lần an toàn vì decrementStock chỉ trừ khi đủ hàng (sau lần đầu sẽ không còn trừ thêm do tồn đã giảm).
     * @param string $orderCode
     * @param array|null $cartSnapshot  (optional, session cart trước khi clear – nếu null sẽ lấy chi tiết đơn hàng)
     * @param string|null $email
     */
    private function finalizeOrderAndClearCart($orderCode, $cartSnapshot = null, $email = null, $markPaid = true)
    {
        $orderModel = $this->model('OrderModel');
        $detailModel = $this->model('OrderDetailModel');
        $productModel = $this->model('AdProducModel');
        $cartModel = $this->model('CartModel');

        // Lấy chi tiết đơn hàng để đảm bảo chính xác số lượng đã đặt
        $details = $orderModel->getOrderDetailsByCode($orderCode);
        if (empty($details)) {
            // Không có chi tiết -> không làm gì
            return;
        }
        // Trừ tồn kho theo từng dòng
        foreach ($details as $d) {
            $masp = $d['product_id'];
            $qty  = (int)$d['quantity'];
            if ($qty > 0) {
                $ok = $productModel->decrementStock($masp, $qty); // cố gắng giảm (clamp nếu cần)
                if (!$ok) {
                    @error_log('[FINALIZE] decrementStock failed for ' . $masp . ' qty=' . $qty . ' orderCode=' . $orderCode);
                } else {
                    @error_log('[FINALIZE] decremented stock for ' . $masp . ' qty=' . $qty . ' orderCode=' . $orderCode);
                }
            }
        }
        // Đánh dấu đã thanh toán nếu được yêu cầu
        if ($markPaid) {
            try { $orderModel->markPaidPreserveInfo($orderCode); } catch (Exception $e) { @error_log('[FINALIZE] markPaid error '.$e->getMessage()); }
        }
        // Nếu email chưa truyền vào, lấy từ đơn hàng
        if (!$email) {
            $orderRow = $orderModel->getOrderByCode($orderCode);
            if ($orderRow && !empty($orderRow['user_email'])) { $email = $orderRow['user_email']; }
            elseif (!empty($_SESSION['user']['email'])) { $email = $_SESSION['user']['email']; }
        }
        // Xóa giỏ trong DB nếu có email
        if ($email) {
            try { $cartModel->clearCartByEmail($email); } catch (Exception $e) { @error_log('[FINALIZE] clearCartByEmail error '.$e->getMessage()); }
        }
        // Xóa giỏ trong session (luôn thực hiện)
        if (isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }
        @error_log('[FINALIZE] Cleared cart for orderCode='.$orderCode.' email='.($email?:'NULL'));
    }

    /**
     * Tính giảm giá theo ngưỡng tổng tiền.
     * Chọn mức phần trăm cao nhất mà subtotal >= min.
     * Trả về: ['percent'=>int,'min'=>int,'discount'=>int,'total_after'=>int]
     */
    private function computeThresholdDiscount($subtotal)
    {
        $best = ['percent' => 0, 'min' => 0];
        // Lấy từ DB nếu có model; nếu không fallback sang hằng số.
        try {
            $tierModel = $this->model('ThresholdDiscountModel');
            $tiers = $tierModel->getActiveTiers();
        } catch (Exception $e) {
            // Không còn hằng số fallback; nếu lỗi model thì không áp dụng giảm
            $tiers = [];
        }
        foreach ($tiers as $tier) {
            // DB row hoặc hằng có key khác nhau: chuẩn hóa
            $minVal = isset($tier['min_total']) ? (int)$tier['min_total'] : (int)$tier['min'];
            $percentVal = (int)$tier['percent'];
            if ($subtotal >= $minVal && $percentVal >= $best['percent']) {
                $best = ['percent' => $percentVal, 'min' => $minVal];
            }
        }
        if ($best['percent'] > 0) {
            $discount = (int)round($subtotal * $best['percent'] / 100, 0);
            return [
                'percent' => $best['percent'],
                'min' => $best['min'],
                'discount' => $discount,
                'total_after' => max(0, $subtotal - $discount)
            ];
        }
        return [
            'percent' => 0,
            'min' => 0,
            'discount' => 0,
            'total_after' => $subtotal
        ];
    }

    /**
     * Hiển thị form nhập thông tin giao hàng sau khi đăng ký hoặc đăng nhập
     */
    public function checkoutInfo()
    {
        if (!isset($_SESSION['user'])) {
            header('location: ' . APP_URL . '/AuthController/Showlogin');
            exit();
        }
        $this->view("homePage", ["page" => "CheckoutInfoView"]);
    }

    /**
     * Áp dụng mã giảm giá (AJAX JSON) mà không reload form nhập giao hàng.
     * POST: coupon_code
     * Response: { valid, discount, cartTotal, discountedTotal, message }
     */
    public function applyCoupon()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        header('Content-Type: application/json; charset=utf-8');
        try {
            $code = isset($_POST['coupon_code']) ? trim($_POST['coupon_code']) : '';
            if ($code === '') {
                echo json_encode(['valid' => false, 'message' => 'Mã giảm giá trống']);
                return;
            }

            $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
            if (empty($cart)) {
                echo json_encode(['valid' => false, 'message' => 'Giỏ hàng trống']);
                return;
            }

            $couponModel = $this->model('CouponModel');
            $couponRow = $couponModel->findByCode($code);
            // Tính subtotal trước giảm ngưỡng
            $cartSubtotal = 0;
            foreach ($cart as $item) {
                $phantram = isset($item['phantram']) ? (float)$item['phantram'] : 0;
                $giaSauKM = $item['giaxuat'] * (1 - $phantram / 100);
                $cartSubtotal += ($giaSauKM * (int)$item['qty']);
            }
            // Áp dụng giảm ngưỡng trước (giống checkoutSave)
            $thresholdInfo = $this->computeThresholdDiscount($cartSubtotal);
            $totalAfterThreshold = $thresholdInfo['total_after'];

            list($valid, $msg) = $couponModel->validateCoupon($couponRow, $cartSubtotal);
            if (!$valid) {
                echo json_encode(['valid' => false, 'message' => $msg ?: 'Mã không hợp lệ']);
                return;
            }
            $applied = $couponModel->applyDiscount($couponRow, $totalAfterThreshold);
            // Lưu session để giữ khi đặt hàng
            $_SESSION['coupon'] = [
                'id' => $couponRow['id'],
                'code' => $couponRow['code'],
                'discount' => $applied['discount'],
                'total_after' => $applied['total_after']
            ];
            echo json_encode([
                'valid' => true,
                'discount' => (int)$applied['discount'],
                'cartTotal' => (int)$cartSubtotal,
                'discountedTotal' => (int)$applied['total_after'],
                'couponCode' => $code,
                'message' => 'Áp dụng mã thành công',
                'threshold' => $thresholdInfo
            ]);
        } catch (Exception $e) {
            echo json_encode(['valid' => false, 'message' => 'Lỗi hệ thống']);
        }
    }

    /**
     * Gửi email xác nhận đơn hàng cho khách hàng
     */
    private function sendOrderConfirmationEmail($email, $orderCode, $totalAmount, $cart, $receiver, $phone, $address, $shippingSpeed, $shippingFee, $paymentMethod, $transaction_info = '')
    {
        error_log("=== SENDING EMAIL START ===");
        error_log("To: $email | Order: $orderCode | Total: $totalAmount");
        
        require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
        require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
        require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'nttv9604@gmail.com'; // Thay bằng email của bạn
            $mail->Password = 'ryae yfan rkle pelu'; // Thay bằng App Password
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';
            $mail->SMTPDebug = 0; // Set to 2 for verbose debug output

            // Người gửi
            $mail->setFrom('nttv9604@gmail.com', 'Cửa hàng DQV');
            $mail->addAddress($email, $receiver);

            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = 'Xác nhận đơn hàng #' . $orderCode;
            
            // Tạo bảng sản phẩm
            $productRows = '';
            $subtotal = 0;
            foreach ($cart as $item) {
                $phantram = isset($item['phantram']) ? (float)$item['phantram'] : 0;
                $giaSauKM = $item['giaxuat'] * (1 - $phantram / 100);
                $thanhtien = $giaSauKM * $item['qty'];
                $subtotal += $thanhtien;
                
                $productRows .= '<tr>';
                $productRows .= '<td style="padding: 10px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($item['tensp']) . '</td>';
                $productRows .= '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">' . $item['qty'] . '</td>';
                $productRows .= '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: right;">' . number_format($giaSauKM, 0, ',', '.') . ' ₫</td>';
                $productRows .= '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: right;">' . number_format($thanhtien, 0, ',', '.') . ' ₫</td>';
                $productRows .= '</tr>';
            }
            
            $paymentMethodText = ($paymentMethod == 'store') ? 'Thanh toán tại cửa hàng' : (($paymentMethod == 'vnpay') ? 'VNPAY' : 'COD - Thanh toán khi nhận hàng');
            
            // Parse transaction_info for discounts
            $couponDiscount = 0; $thresholdDiscount = 0; $shippingAdjustments = [];
            if (!empty($transaction_info)) {
                $parts = explode('|', $transaction_info);
                array_shift($parts); // remove status
                foreach ($parts as $tok) {
                    $tok = trim($tok);
                    if (stripos($tok, 'coupon:') === 0) {
                        if (preg_match('/^coupon:(.*?)\(([-+]?\d+)\)$/', $tok, $m)) {
                            $couponDiscount += abs((int)$m[2]);
                        }
                    } elseif (stripos($tok, 'threshold:') === 0) {
                        if (preg_match('/^threshold:(.*?)\(([-+]?\d+)\)$/', $tok, $m)) {
                            $thresholdDiscount += abs((int)$m[2]);
                        }
                    } elseif (stripos($tok, 'shipping:') === 0 || stripos($tok, 'pickup:') === 0) {
                        $label = stripos($tok, 'shipping:') === 0 ? 'Phí vận chuyển' : 'Nhận tại cửa hàng';
                        if (preg_match('/^([^:]+):(.*?)\(([-+]?\d+)\)$/', $tok, $m)) {
                            $shippingAdjustments[] = ['label' => $m[2] ?: $label, 'amount' => (int)$m[3]];
                        } elseif (preg_match('/^([^:]+):\(([-+]?\d+)\)$/', $tok, $m)) {
                            $shippingAdjustments[] = ['label' => $label, 'amount' => (int)$m[2]];
                        }
                    }
                }
            }
            
            $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5;">
                <div style="background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h2 style="color: #0d6efd; margin-bottom: 20px; border-bottom: 2px solid #0d6efd; padding-bottom: 10px;">
                        Xác nhận đơn hàng
                    </h2>
                    
                    <p style="color: #333; font-size: 16px;">Xin chào <strong>' . htmlspecialchars($receiver) . '</strong>,</p>
                    <p style="color: #666;">Cảm ơn bạn đã đặt hàng tại cửa hàng DQV. Đơn hàng của bạn đã được tiếp nhận và đang xử lý.</p>
                    
                    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                        <h3 style="color: #0d6efd; margin-top: 0;">Thông tin đơn hàng</h3>
                        <p style="margin: 5px 0;"><strong>Mã đơn hàng:</strong> ' . htmlspecialchars($orderCode) . '</p>
                        <p style="margin: 5px 0;"><strong>Ngày đặt:</strong> ' . date('d/m/Y H:i') . '</p>
                        <p style="margin: 5px 0;"><strong>Phương thức thanh toán:</strong> ' . htmlspecialchars($paymentMethodText) . '</p>
                    </div>
                    
                    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                        <h3 style="color: #0d6efd; margin-top: 0;">Thông tin giao hàng</h3>
                        <p style="margin: 5px 0;"><strong>Người nhận:</strong> ' . htmlspecialchars($receiver) . '</p>
                        <p style="margin: 5px 0;"><strong>Số điện thoại:</strong> ' . htmlspecialchars($phone) . '</p>
                        <p style="margin: 5px 0;"><strong>Địa chỉ:</strong> ' . htmlspecialchars($address) . '</p>
                        <p style="margin: 5px 0;"><strong>Hình thức giao hàng:</strong> ' . htmlspecialchars($shippingSpeed) . ' (' . number_format($shippingFee, 0, ',', '.') . ' ₫)</p>
                    </div>
                    
                    <h3 style="color: #0d6efd;">Chi tiết đơn hàng</h3>
                    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                        <thead>
                            <tr style="background-color: #0d6efd; color: white;">
                                <th style="padding: 10px; text-align: left;">Sản phẩm</th>
                                <th style="padding: 10px; text-align: center;">SL</th>
                                <th style="padding: 10px; text-align: right;">Đơn giá</th>
                                <th style="padding: 10px; text-align: right;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            ' . $productRows . '
                        </tbody>
                    </table>
                    
                    <div style="text-align: right; margin-top: 20px; padding-top: 15px; border-top: 2px solid #ddd;">
                        <p style="margin: 5px 0; font-size: 16px;"><strong>Tạm tính:</strong> ' . number_format($subtotal, 0, ',', '.') . ' ₫</p>
                        ' . ($couponDiscount > 0 ? '<p style="margin: 5px 0; font-size: 16px;"><strong>Giảm mã giảm giá:</strong> -' . number_format($couponDiscount, 0, ',', '.') . ' ₫</p>' : '') . '
                        ' . ($thresholdDiscount > 0 ? '<p style="margin: 5px 0; font-size: 16px;"><strong>Giảm theo ngưỡng:</strong> -' . number_format($thresholdDiscount, 0, ',', '.') . ' ₫</p>' : '') . '
                        <p style="margin: 5px 0; font-size: 16px;"><strong>Phí vận chuyển:</strong> ' . number_format($shippingFee, 0, ',', '.') . ' ₫</p>
                        <p style="margin: 10px 0; font-size: 20px; color: #dc3545;"><strong>Tổng cộng:</strong> ' . number_format($totalAmount, 0, ',', '.') . ' ₫</p>
                    </div>
                    
                    <div style="margin-top: 30px; padding: 15px; background-color: #d1ecf1; border-left: 4px solid #0c5460; border-radius: 5px;">
                        <p style="margin: 0; color: #0c5460;">
                            <strong>Lưu ý:</strong> Đơn hàng của bạn sẽ được xử lý trong thời gian sớm nhất. 
                            Bạn sẽ nhận được thông báo khi đơn hàng được giao cho đơn vị vận chuyển.
                        </p>
                    </div>
                    
                    <p style="margin-top: 30px; color: #666;">
                        Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ với chúng tôi qua email hoặc hotline.
                    </p>
                    
                    <p style="color: #999; font-size: 14px; margin-top: 20px;">
                        Trân trọng,<br>
                        <strong>Đội ngũ DQV</strong>
                    </p>
                </div>
            </div>';

            $mail->send();
            error_log("=== EMAIL SENT SUCCESSFULLY ===");
            return true;
        } catch (Exception $e) {
            error_log("=== EMAIL SENDING FAILED ===");
            error_log("Error: " . $mail->ErrorInfo);
            error_log("Exception: " . $e->getMessage());
            return false;
        }
    }
}