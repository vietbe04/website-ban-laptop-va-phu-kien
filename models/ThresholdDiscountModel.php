<?php
require_once "BaseModel.php";

/**
 * Model ngưỡng giảm giá theo tổng đơn hàng
 */
class ThresholdDiscountModel extends BaseModel
{
    private $table = 'order_thresholds';

    /** Lấy tất cả bậc ngưỡng (tăng dần theo min_total) */
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY min_total ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Lấy các bậc đang bật (status=1) */
    public function getActiveTiers()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 1 ORDER BY min_total ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Tạo bậc ngưỡng mới */
    public function create($minTotal, $percent, $status = 1)
    {
        $sql = "INSERT INTO {$this->table} (min_total, percent, status, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([(int)$minTotal, (int)$percent, (int)$status]);
    }

    /** Xóa bậc theo id */
    public function remove($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /** Cập nhật trạng thái bật/tắt */
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([(int)$status, $id]);
    }
}
