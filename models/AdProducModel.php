<?php
require_once "BaseModel.php";
/**
 * Model sản phẩm: CRUD, kho, tìm kiếm và phân trang
 */
class AdProducModel extends BaseModel{
    private $table="tblsanpham";
    /**
     * Thêm sản phẩm mới
     */
    public function insert($maLoaiSP,$masp,$tensp,$hinhanh,$soluong,
    $giaNhap,$giaXuat,$mota,$createDate) {
        // Kiểm tra bảng có trong danh sách không
        if (!array_key_exists($this->table, $this->primaryKeys)) {
            throw new Exception("Bảng không hợp lệ hoặc chưa được định nghĩa.");
        }
        // Kiểm tra xem mã  sản phẩm đã tồn tại chưa
        $column = $this->primaryKeys[$this->table];
        if($this->check($this->table, $column, $masp)>0){
            echo "Mã sản phẩm đã tồn tại. Vui lòng chọn mã khác.";
            return;
        }
        else{
            // Chuẩn bị câu lệnh INSERT
                $sql = "INSERT INTO tblsanpham (maLoaiSP,masp,tensp,hinhanh,soluong,
                giaNhap,giaXuat,mota,createDate) 
                    VALUES (:maLoaiSP,:masp,:tensp,:hinhanh,:soluong,:giaNhap,
                    :giaXuat,:mota,:createDate)";
            try {
                $stmt = $this->db->prepare($sql);
                // Gán giá trị cho các tham số
                $stmt->bindParam(':maLoaiSP', $maLoaiSP);
                $stmt->bindParam(':masp', $masp);
                $stmt->bindParam(':tensp', $tensp);
                $stmt->bindParam(':hinhanh', $hinhanh);
                $stmt->bindParam(':soluong', $soluong);
                $stmt->bindParam(':giaNhap', $giaNhap);
                $stmt->bindParam(':giaXuat', $giaXuat);
                $stmt->bindParam(':mota', $mota);
                $stmt->bindParam(':createDate', $createDate);
                $stmt->execute();
                echo "Thêm sản phẩm thành công.";
            } catch (PDOException $e) {
                echo "Thất bại" . $e->getMessage();
            } 
        }    
    }
    
    /**
     * Cập nhật sản phẩm theo mã
     */
    public function update($maLoaiSP,$masp,$tensp,$hinhanh,$soluong,$giaNhap,
    $giaXuat,$mota,$createDate, $originalMasp = null) {
        // Nếu không cung cấp $originalMasp, dùng $masp làm khóa tìm
        $orig = $originalMasp ?? $masp;
        // Chuẩn bị câu lệnh UPDATE (WHERE dùng :orig_masp để tránh nhầm lẫn)
        $sql = "UPDATE tblsanpham SET 
                maLoaiSP = :maLoaiSP,
                masp = :masp, 
                tensp = :tensp,
                hinhanh = :hinhanh,
                soluong = :soluong,
                giaNhap = :giaNhap,
                giaXuat = :giaXuat,
                mota = :mota,
                createDate = :createDate
                WHERE masp = :orig_masp";
        try {
            $stmt = $this->db->prepare($sql); 
            // Gán giá trị cho các tham số
            $stmt->bindParam(':maLoaiSP', $maLoaiSP);
            $stmt->bindParam(':masp', $masp);
            $stmt->bindParam(':tensp', $tensp);
            $stmt->bindParam(':hinhanh', $hinhanh);
            $stmt->bindParam(':soluong', $soluong);
            $stmt->bindParam(':giaNhap', $giaNhap);
            $stmt->bindParam(':giaXuat', $giaXuat);
            $stmt->bindParam(':mota', $mota);
            $stmt->bindParam(':createDate', $createDate);
            $stmt->bindParam(':orig_masp', $orig);
            // Thực thi câu lệnh
            $stmt->execute();
            //echo "Cập nhật loại sản phẩm thành công.";
        } catch (PDOException $e) {
            echo "Cập nhật không thành công: " . $e->getMessage();
        }
    }

    /**
     * Cập nhật số lượng tồn kho cho một sản phẩm (masp)
     * @param string $masp
     * @param int $soluong
     */
    public function setStock($masp, $soluong) {
        $sql = "UPDATE tblsanpham SET soluong = :soluong WHERE masp = :masp";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':soluong', $soluong);
            $stmt->bindParam(':masp', $masp);
            $stmt->execute();
        } catch (PDOException $e) {
            // Không làm gián đoạn, chỉ log nếu cần
            error_log('Error updating stock for ' . $masp . ': ' . $e->getMessage());
        }
    }

    /**
     * Lấy số lượng tồn hiện tại của sản phẩm
     * @param string $masp
     * @return int
     */
    public function getStock($masp) {
        $sql = "SELECT soluong FROM tblsanpham WHERE masp = :masp";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':masp' => $masp]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int)$row['soluong'] : 0;
        } catch (PDOException $e) {
            error_log('Error getting stock for ' . $masp . ': ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Giảm tồn kho: đảm bảo chỉ giảm khi còn đủ số lượng (atomic)
     * Trả về true nếu giảm thành công, false nếu không đủ tồn hoặc lỗi
     * @param string $masp
     * @param int $qty
     * @return bool
     */
    public function decrementStock($masp, $qty) {
        // Use a safe update that clamps stock at 0 to ensure the deduction happens
        // even if current stock is less than requested quantity. This avoids cases
        // where a previous conditional update (soluong >= :qty) would skip the
        // decrement and leave the order unaccounted.
        $sql = "UPDATE tblsanpham SET soluong = GREATEST(0, soluong - :qty) WHERE masp = :masp";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':qty' => (int)$qty, ':masp' => $masp]);
            if ($stmt->rowCount() > 0) {
                return true;
            }
            // No rows affected -> product may not exist
            error_log('decrementStock: no row updated for ' . $masp . ' qty=' . $qty);
            return false;
        } catch (PDOException $e) {
            error_log('Error decrementing stock for ' . $masp . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Tìm kiếm sản phẩm theo bộ lọc: masp (exact), tensp (partial), price range
     * @param array $filters ['masp', 'tensp', 'price_min', 'price_max']
     * @return array
     */
    /**
     * Tìm kiếm sản phẩm theo bộ lọc (hỗ trợ tham số hợp nhất 'q')
     */
    public function search(array $filters = []) {
        $sql = "SELECT * FROM tblsanpham WHERE 1=1";
        $params = [];

        // Cải thiện tìm kiếm: cho phép tìm gần đúng cả mã và tên sản phẩm
        if (!empty($filters['q'])) {
            $sql .= " AND (masp LIKE :q_masp OR tensp LIKE :q_tensp)";
            $params[':q_masp'] = '%' . $filters['q'] . '%';
            $params[':q_tensp'] = '%' . $filters['q'] . '%';
        } else {
            if (!empty($filters['masp'])) {
                $sql .= " AND masp LIKE :masp";
                $params[':masp'] = '%' . $filters['masp'] . '%';
            }

            if (!empty($filters['tensp'])) {
                $sql .= " AND tensp LIKE :tensp";
                $params[':tensp'] = '%' . $filters['tensp'] . '%';
            }
        }

        if (isset($filters['price_min']) && $filters['price_min'] !== '') {
            $sql .= " AND giaXuat >= :price_min";
            $params[':price_min'] = (float)$filters['price_min'];
        }

        if (isset($filters['price_max']) && $filters['price_max'] !== '') {
            $sql .= " AND giaXuat <= :price_max";
            $params[':price_max'] = (float)$filters['price_max'];
        }

        $sql .= " ORDER BY createDate DESC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Product search error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy sản phẩm theo mã loại (chính xác)
     * @param string $maLoaiSP
     * @return array
     */
    public function getByCategory($maLoaiSP) {
        $sql = "SELECT * FROM tblsanpham WHERE maLoaiSP = :cat ORDER BY createDate DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':cat' => $maLoaiSP]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Product getByCategory error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tất cả sản phẩm với phân trang
     * @param int $limit Số lượng sản phẩm mỗi trang
     * @param int $offset Vị trí bắt đầu
     * @return array
     */
    public function getProductsWithPagination($limit, $offset) {
        $sql = "SELECT * FROM tblsanpham ORDER BY createDate DESC LIMIT :limit OFFSET :offset";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Product getProductsWithPagination error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm kiếm sản phẩm với phân trang
     * @param array $filters Các bộ lọc tìm kiếm
     * @param int $limit Số lượng sản phẩm mỗi trang
     * @param int $offset Vị trí bắt đầu
     * @return array
     */
    /**
     * Tìm kiếm có phân trang theo bộ lọc
     */
    public function searchWithPagination(array $filters = [], $limit, $offset) {
        $sql = "SELECT * FROM tblsanpham WHERE 1=1";
        $params = [];

        // Cải thiện tìm kiếm: cho phép tìm gần đúng cả mã và tên sản phẩm
        if (!empty($filters['q'])) {
            $sql .= " AND (masp LIKE :q_masp OR tensp LIKE :q_tensp)";
            $params[':q_masp'] = '%' . $filters['q'] . '%';
            $params[':q_tensp'] = '%' . $filters['q'] . '%';
        } else {
            if (!empty($filters['masp'])) {
                $sql .= " AND masp LIKE :masp";
                $params[':masp'] = '%' . $filters['masp'] . '%';
            }

            if (!empty($filters['tensp'])) {
                $sql .= " AND tensp LIKE :tensp";
                $params[':tensp'] = '%' . $filters['tensp'] . '%';
            }
        }

        // Support filtering by product category (exact match)
        if (!empty($filters['maLoaiSP'])) {
            $sql .= " AND maLoaiSP = :maLoaiSP";
            $params[':maLoaiSP'] = $filters['maLoaiSP'];
        }

        if (isset($filters['price_min']) && $filters['price_min'] !== '') {
            $sql .= " AND giaXuat >= :price_min";
            $params[':price_min'] = (float)$filters['price_min'];
        }

        if (isset($filters['price_max']) && $filters['price_max'] !== '') {
            $sql .= " AND giaXuat <= :price_max";
            $params[':price_max'] = (float)$filters['price_max'];
        }

        $sql .= " ORDER BY createDate DESC LIMIT :limit OFFSET :offset";

        try {
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Product searchWithPagination error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy sản phẩm theo mã loại với phân trang
     * @param string $maLoaiSP
     * @param int $limit Số lượng sản phẩm mỗi trang
     * @param int $offset Vị trí bắt đầu
     * @return array
     */
    public function getByCategoryWithPagination($maLoaiSP, $limit, $offset) {
        $sql = "SELECT * FROM tblsanpham WHERE maLoaiSP = :cat ORDER BY createDate DESC LIMIT :limit OFFSET :offset";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':cat', $maLoaiSP);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Product getByCategoryWithPagination error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm tổng số sản phẩm
     * @return int
     */
    public function countAllProducts() {
        $sql = "SELECT COUNT(*) as total FROM tblsanpham";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log('Product countAllProducts error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Đếm số sản phẩm theo bộ lọc
     * @param array $filters Các bộ lọc tìm kiếm
     * @return int
     */
    /**
     * Đếm số sản phẩm theo bộ lọc (hỗ trợ 'q')
     */
    public function countProducts(array $filters = []) {
        $sql = "SELECT COUNT(*) as total FROM tblsanpham WHERE 1=1";
        $params = [];

        // Cải thiện tìm kiếm: cho phép tìm gần đúng cả mã và tên sản phẩm
        if (!empty($filters['q'])) {
            $sql .= " AND (masp LIKE :q_masp OR tensp LIKE :q_tensp)";
            $params[':q_masp'] = '%' . $filters['q'] . '%';
            $params[':q_tensp'] = '%' . $filters['q'] . '%';
        } else {
            if (!empty($filters['masp'])) {
                $sql .= " AND masp LIKE :masp";
                $params[':masp'] = '%' . $filters['masp'] . '%';
            }

            if (!empty($filters['tensp'])) {
                $sql .= " AND tensp LIKE :tensp";
                $params[':tensp'] = '%' . $filters['tensp'] . '%';
            }
        }

        if (isset($filters['price_min']) && $filters['price_min'] !== '') {
            $sql .= " AND giaXuat >= :price_min";
            $params[':price_min'] = (float)$filters['price_min'];
        }

        if (isset($filters['price_max']) && $filters['price_max'] !== '') {
            $sql .= " AND giaXuat <= :price_max";
            $params[':price_max'] = (float)$filters['price_max'];
        }

        // Support filtering by product category
        if (!empty($filters['maLoaiSP'])) {
            $sql .= " AND maLoaiSP = :maLoaiSP";
            $params[':maLoaiSP'] = $filters['maLoaiSP'];
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log('Product countProducts error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Đếm số sản phẩm theo mã loại
     * @param string $maLoaiSP
     * @return int
     */
    public function countProductsByCategory($maLoaiSP) {
        $sql = "SELECT COUNT(*) as total FROM tblsanpham WHERE maLoaiSP = :cat";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':cat' => $maLoaiSP]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log('Product countProductsByCategory error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Tìm kiếm nâng cao với phân trang, lọc và sắp xếp
     * @param array $filters Bộ lọc: q, maLoaiSP, price_min, price_max, color, capacity, in_stock
     * @param string $sortBy Tiêu chí sắp xếp: price_asc, price_desc, popularity, rating, newest
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function searchWithPaginationAdvanced(array $filters = [], $sortBy = 'newest', $limit = 20, $offset = 0) {
        $sql = "SELECT DISTINCT p.* FROM tblsanpham p";
        $joins = [];
        $where = ["1=1"];
        $params = [];

        // Join với biến thể nếu lọc theo màu/dung lượng
        if (!empty($filters['color']) || !empty($filters['capacity'])) {
            $joins[] = "LEFT JOIN product_variants pv ON p.masp = pv.masp AND pv.active = 1";
        }

        // Tìm kiếm từ khóa
        if (!empty($filters['q'])) {
            $where[] = "(p.masp LIKE :q_masp OR p.tensp LIKE :q_tensp OR p.mota LIKE :q_mota)";
            $params[':q_masp'] = '%' . $filters['q'] . '%';
            $params[':q_tensp'] = '%' . $filters['q'] . '%';
            $params[':q_mota'] = '%' . $filters['q'] . '%';
        }

        // Lọc theo danh mục
        if (!empty($filters['maLoaiSP'])) {
            $where[] = "p.maLoaiSP = :maLoaiSP";
            $params[':maLoaiSP'] = $filters['maLoaiSP'];
        }

        // Lọc theo khoảng giá
        if (isset($filters['price_min']) && $filters['price_min'] !== '') {
            $where[] = "p.giaXuat >= :price_min";
            $params[':price_min'] = (float)$filters['price_min'];
        }
        if (isset($filters['price_max']) && $filters['price_max'] !== '') {
            $where[] = "p.giaXuat <= :price_max";
            $params[':price_max'] = (float)$filters['price_max'];
        }

        // Lọc theo màu sắc
        if (!empty($filters['color'])) {
            $where[] = "pv.variant_type = 'color' AND pv.name = :color";
            $params[':color'] = $filters['color'];
        }

        // Lọc theo dung lượng
        if (!empty($filters['capacity'])) {
            $where[] = "pv.variant_type = 'capacity' AND pv.name = :capacity";
            $params[':capacity'] = $filters['capacity'];
        }

        // Lọc theo tình trạng kho
        if (isset($filters['in_stock']) && $filters['in_stock'] === '1') {
            $where[] = "p.soluong > 0";
        }

        // Build query
        if (!empty($joins)) {
            $sql .= " " . implode(" ", $joins);
        }
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        // Sắp xếp
        $orderBy = $this->getSortOrderSQL($sortBy);
        $sql .= " ORDER BY " . $orderBy;
        $sql .= " LIMIT :limit OFFSET :offset";

        try {
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Product searchWithPaginationAdvanced error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm số sản phẩm cho tìm kiếm nâng cao
     */
    public function countProductsAdvanced(array $filters = []) {
        $sql = "SELECT COUNT(DISTINCT p.masp) as total FROM tblsanpham p";
        $joins = [];
        $where = ["1=1"];
        $params = [];

        // Join với biến thể nếu lọc theo màu/dung lượng
        if (!empty($filters['color']) || !empty($filters['capacity'])) {
            $joins[] = "LEFT JOIN product_variants pv ON p.masp = pv.masp AND pv.active = 1";
        }

        // Tìm kiếm từ khóa
        if (!empty($filters['q'])) {
            $where[] = "(p.masp LIKE :q_masp OR p.tensp LIKE :q_tensp OR p.mota LIKE :q_mota)";
            $params[':q_masp'] = '%' . $filters['q'] . '%';
            $params[':q_tensp'] = '%' . $filters['q'] . '%';
            $params[':q_mota'] = '%' . $filters['q'] . '%';
        }

        // Lọc theo danh mục
        if (!empty($filters['maLoaiSP'])) {
            $where[] = "p.maLoaiSP = :maLoaiSP";
            $params[':maLoaiSP'] = $filters['maLoaiSP'];
        }

        // Lọc theo khoảng giá
        if (isset($filters['price_min']) && $filters['price_min'] !== '') {
            $where[] = "p.giaXuat >= :price_min";
            $params[':price_min'] = (float)$filters['price_min'];
        }
        if (isset($filters['price_max']) && $filters['price_max'] !== '') {
            $where[] = "p.giaXuat <= :price_max";
            $params[':price_max'] = (float)$filters['price_max'];
        }

        // Lọc theo màu sắc
        if (!empty($filters['color'])) {
            $where[] = "pv.variant_type = 'color' AND pv.name = :color";
            $params[':color'] = $filters['color'];
        }

        // Lọc theo dung lượng
        if (!empty($filters['capacity'])) {
            $where[] = "pv.variant_type = 'capacity' AND pv.name = :capacity";
            $params[':capacity'] = $filters['capacity'];
        }

        // Lọc theo tình trạng kho
        if (isset($filters['in_stock']) && $filters['in_stock'] === '1') {
            $where[] = "p.soluong > 0";
        }

        // Build query
        if (!empty($joins)) {
            $sql .= " " . implode(" ", $joins);
        }
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log('Product countProductsAdvanced error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy SQL sắp xếp theo tiêu chí
     */
    private function getSortOrderSQL($sortBy) {
        switch ($sortBy) {
            case 'price_asc':
                return "p.giaXuat ASC";
            case 'price_desc':
                return "p.giaXuat DESC";
            case 'popularity':
                // Sắp xếp theo số lượng đã bán (cần tính từ order_details)
                return "(SELECT COALESCE(SUM(od.quantity), 0) FROM order_details od 
                         INNER JOIN orders o ON od.order_id = o.id 
                         WHERE od.product_id = p.masp 
                         AND (LOWER(o.transaction_info) REGEXP 'da.*than.*toan' 
                              OR LOWER(o.transaction_info) LIKE '%completed%' 
                              OR LOWER(o.transaction_info) LIKE '%paid%')) DESC";
            case 'rating':
                // Sắp xếp theo điểm đánh giá trung bình
                return "(SELECT COALESCE(AVG(r.rating), 0) FROM reviews r WHERE r.product_id = p.masp) DESC";
            case 'newest':
            default:
                return "p.createDate DESC";
        }
    }

    /**
     * Lấy tất cả màu sắc có sẵn
     */
    public function getAllColors() {
        $sql = "SELECT DISTINCT name FROM product_variants WHERE variant_type = 'color' AND active = 1 ORDER BY name";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'name');
        } catch (PDOException $e) {
            error_log('getAllColors error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tất cả dung lượng có sẵn
     */
    public function getAllCapacities() {
        $sql = "SELECT DISTINCT name FROM product_variants WHERE variant_type = 'capacity' AND active = 1 ORDER BY name";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'name');
        } catch (PDOException $e) {
            error_log('getAllCapacities error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy sản phẩm bán chạy nhất (dựa trên số lượng đã bán)
     * @param int $limit Số lượng sản phẩm cần lấy
     * @return array
     */
    public function getBestSellingProducts($limit = 8) {
        // Chỉ tính các đơn đã thanh toán (transaction_info chứa token chỉ trạng thái thanh toán)
        $paidCond = "(LOWER(o.transaction_info) REGEXP 'da.*than.*toan' OR LOWER(o.transaction_info) LIKE '%completed%' OR LOWER(o.transaction_info) LIKE '%paid%')";
        $sql = "SELECT p.*, COALESCE(SUM(od.quantity), 0) AS total_sold
            FROM tblsanpham p
            LEFT JOIN order_details od ON p.masp = od.product_id
            LEFT JOIN orders o ON od.order_id = o.id AND $paidCond
            GROUP BY p.masp
            ORDER BY total_sold DESC, p.masp ASC
            LIMIT $limit";
        return $this->select($sql);
    }

    /**
     * Lấy số lượng đã bán cho danh sách mã sản phẩm (chỉ đơn đã thanh toán)
     * @param array $maspList
     * @return array [masp => total_sold]
     */
    public function getSoldCounts(array $maspList) {
        if (empty($maspList)) { return []; }
        // Chuẩn bị placeholders an toàn
        $placeholders = implode(',', array_fill(0, count($maspList), '?'));
        $paidCond = "(LOWER(o.transaction_info) REGEXP 'da.*than.*toan' OR LOWER(o.transaction_info) LIKE '%completed%' OR LOWER(o.transaction_info) LIKE '%paid%')";
        $sql = "SELECT od.product_id AS masp, COALESCE(SUM(od.quantity),0) AS total_sold
            FROM order_details od
            INNER JOIN orders o ON od.order_id = o.id AND $paidCond
            WHERE od.product_id IN ($placeholders)
            GROUP BY od.product_id";
        try {
            $stmt = $this->db->prepare($sql);
            foreach ($maspList as $i => $id) { $stmt->bindValue($i+1, $id); }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $map = [];
            foreach ($rows as $r) { $map[$r['masp']] = (int)$r['total_sold']; }
            return $map;
        } catch (PDOException $e) {
            error_log('getSoldCounts error: '.$e->getMessage());
            return [];
        }
    }


}