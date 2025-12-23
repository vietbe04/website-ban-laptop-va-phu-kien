<?php
require_once "BaseModel.php";

class AdKhuyenMai extends BaseModel
{
    private $table = "khuyenmai";

    public function getAllWithProduct()
    {
        $sql = "SELECT k.*, s.tensp, s.giaXuat, s.hinhanh
                FROM khuyenmai k
                LEFT JOIN tblsanpham s ON k.masp = s.masp
                ORDER BY k.ngaybatdau DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getView()
    {
        $sql = "SELECT maLoaiSP FROM tblloaisp;";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($maLoaiSP, $masp, $phantram, $ngaybatdau, $ngayketthuc)
    {
        // Validate date range
        if (strtotime($ngayketthuc) < strtotime($ngaybatdau)) {
            throw new Exception('Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.');
        }
        // Check overlap for this product
        if ($this->hasOverlapForProduct($masp, $ngaybatdau, $ngayketthuc)) {
            throw new Exception('Sản phẩm ' . $masp . ' đã có khuyến mãi trong khoảng thời gian này.');
        }

        $sql = "INSERT INTO khuyenmai (maLoaiSP, masp, phantram, ngaybatdau, ngayketthuc)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$maLoaiSP, $masp, $phantram, $ngaybatdau, $ngayketthuc]);
    }

    public function deleteKm($km_id)
    {
        $sql = "DELETE FROM khuyenmai WHERE km_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$km_id]);
    }



    // trả về 1 record sản phẩm kèm phantram (ưu tiên bảng khuyenmai nếu đang hiệu lực)
    public function findWithDiscount($masp)
    {
        $sql = "
            SELECT 
                s.*,
                COALESCE(k.phantram, 0) AS phantram,
                k.ngaybatdau,
                k.ngayketthuc
            FROM tblsanpham s
            LEFT JOIN khuyenmai k
                ON s.masp = k.masp
                AND NOW() BETWEEN k.ngaybatdau AND k.ngayketthuc
            WHERE s.masp = ?
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$masp]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getProductsByCategory($maLoaiSP)
    {
        $sql = "SELECT masp FROM tblsanpham WHERE maLoaiSP = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$maLoaiSP]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertForCategory($maLoaiSP, $phantram, $ngaybatdau, $ngayketthuc)
    {
        // Lấy tất cả sản phẩm thuộc loại
        $products = $this->getProductsByCategory($maLoaiSP);

        // Chuẩn bị statement để chèn nhiều dòng
        // Validate date range
        if (strtotime($ngayketthuc) < strtotime($ngaybatdau)) {
            throw new Exception('Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.');
        }
        // Check for any conflicts in the category
        $conflicts = [];
        foreach ($products as $p) {
            if ($this->hasOverlapForProduct($p['masp'], $ngaybatdau, $ngayketthuc)) {
                $conflicts[] = $p['masp'];
            }
        }
        if (!empty($conflicts)) {
            throw new Exception('Không thể thêm khuyến mãi. Một số sản phẩm trong loại đã có khuyến mãi đang chạy: ' . implode(', ', array_slice($conflicts,0,10)));
        }

        $sql = "INSERT INTO khuyenmai (maLoaiSP, masp, phantram, ngaybatdau, ngayketthuc)
            VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);

        foreach ($products as $p) {
            $stmt->execute([
                $maLoaiSP,
                $p["masp"],
                $phantram,
                $ngaybatdau,
                $ngayketthuc
            ]);
        }

        return true;
    }

    /**
     * Kiểm tra khuyến mãi chồng lấp cho một sản phẩm
     * @param string $masp
     * @param string $start YYYY-MM-DD
     * @param string $end YYYY-MM-DD
     * @param int|null $excludeId optional km_id to exclude (for updates)
     * @return bool
     */
    public function hasOverlapForProduct($masp, $start, $end, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM khuyenmai WHERE masp = :masp AND NOT (ngayketthuc < :start OR ngaybatdau > :end)";
        if ($excludeId) {
            $sql .= " AND km_id != :excludeId";
        }
        $stmt = $this->db->prepare($sql);
        $params = [':masp' => $masp, ':start' => $start, ':end' => $end];
        if ($excludeId) {
            $params[':excludeId'] = (int)$excludeId;
        }
        $stmt->execute($params);
        $cnt = (int)$stmt->fetchColumn();
        return $cnt > 0;
    }

    public function updateKm($km_id, $maLoaiSP, $masp, $phantram, $ngaybatdau, $ngayketthuc)
    {
        // Validate date range
        if (strtotime($ngayketthuc) < strtotime($ngaybatdau)) {
            throw new Exception('Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.');
        }
        // Check overlap excluding current promotion
        if ($this->hasOverlapForProduct($masp, $ngaybatdau, $ngayketthuc, $km_id)) {
            throw new Exception('Sản phẩm ' . $masp . ' đã có khuyến mãi khác chồng lấp trong khoảng thời gian này.');
        }
        $sql = "UPDATE khuyenmai SET maLoaiSP = ?, masp = ?, phantram = ?, ngaybatdau = ?, ngayketthuc = ? WHERE km_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$maLoaiSP, $masp, $phantram, $ngaybatdau, $ngayketthuc, $km_id]);
    }

    public function findById($km_id)
    {
        $sql = "SELECT * FROM khuyenmai WHERE km_id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$km_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
