<?php
require_once "BaseModel.php";

/**
 * Model mã giảm giá (coupon): CRUD, xác thực và áp dụng giảm
 */
class CouponModel extends BaseModel
{
    private $table = 'coupons'; // expected table name

    /**
     * Tìm coupon theo mã
     */
    public function findByCode($code)
    {
        $sql = "SELECT * FROM {$this->table} WHERE code = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo coupon mới
     */
    public function create($code, $type, $value, $start_date, $end_date, $status = 1, $min_total = null, $usage_limit = null)
    {
        // expire any past coupons first
        $this->expirePastCoupons();

        // if coupon with same code exists
        $existing = $this->findByCode($code);
        if ($existing) {
            // if existing is active, do not allow duplicate
            if ((int)$existing['status'] === 1) {
                throw new Exception('Mã đã tồn tại và đang hoạt động. Không thể tạo trùng.');
            }
            // otherwise reuse/update the existing inactive coupon (reset usage)
            $this->update($existing['id'], $code, $type, $value, $start_date, $end_date, $status, $min_total, $usage_limit);
            $this->resetUsage($existing['id']);
            return true;
        }

        $sql = "INSERT INTO {$this->table} (code, type, value, start_date, end_date, status, min_total, usage_limit, used_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$code, $type, $value, $start_date, $end_date, $status, $min_total, $usage_limit]);
    }

    /**
     * Cập nhật coupon theo id
     */
    public function update($id, $code, $type, $value, $start_date, $end_date, $status, $min_total, $usage_limit)
    {
        $sql = "UPDATE {$this->table} SET code = ?, type = ?, value = ?, start_date = ?, end_date = ?, status = ?, min_total = ?, usage_limit = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$code, $type, $value, $start_date, $end_date, $status, $min_total, $usage_limit, $id]);
    }

    /**
     * Xóa coupon theo id
     */
    public function deleteById($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Kiểm tra tính hợp lệ của coupon so với tổng giỏ hàng
     * @return array [bool hợp lệ, string thông báo]
     */
    public function validateCoupon($coupon, $cartTotal)
    {
        // $coupon is an associative row returned by findByCode
        if (!$coupon) return [false, 'Mã khuyến mãi không tồn tại'];
        if ((int)$coupon['status'] !== 1) return [false, 'Mã không còn hiệu lực'];
        $now = date('Y-m-d');
        if (!empty($coupon['start_date']) && $now < $coupon['start_date']) return [false, 'Mã chưa đến hạn sử dụng'];
        if (!empty($coupon['end_date']) && $now > $coupon['end_date']) return [false, 'Mã đã hết hạn'];
        if (!empty($coupon['usage_limit']) && $coupon['used_count'] >= $coupon['usage_limit']) return [false, 'Mã đã đạt giới hạn sử dụng'];
        if (!empty($coupon['min_total']) && $cartTotal < $coupon['min_total']) {
            $min = number_format((float)$coupon['min_total'], 0, ',', '.');
            return [false, 'Tổng đơn chưa đủ điều kiện áp dụng mã. Tổng tối thiểu yêu cầu: ' . $min . ' ₫'];
        }
        return [true, 'Hợp lệ'];
    }

    /**
     * Tính số tiền giảm và tổng sau giảm khi áp mã
     * @return array ['discount'=>int,'total_after'=>int]
     */
    public function applyDiscount($coupon, $cartTotal)
    {
        // returns ['discount' => x, 'total_after' => y]
        $discount = 0;
        if (!$coupon) return ['discount' => 0, 'total_after' => $cartTotal];
        if ($coupon['type'] === 'percent') {
            $discount = round($cartTotal * ((float)$coupon['value'] / 100), 0);
        } else { // fixed
            $discount = round((float)$coupon['value'], 0);
        }
        $totalAfter = max(0, $cartTotal - $discount);
        return ['discount' => $discount, 'total_after' => $totalAfter];
    }

    /**
     * Tăng bộ đếm số lần sử dụng của coupon
     */
    public function incrementUsage($id)
    {
        $sql = "UPDATE {$this->table} SET used_count = used_count + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Lấy toàn bộ coupon
     */
    public function getAll()
    {
        // Expire past coupons before returning
        $this->expirePastCoupons();
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mark coupons as inactive if their end_date has passed
     */
    public function expirePastCoupons()
    {
        $today = date('Y-m-d');
        $sql = "UPDATE {$this->table} SET status = 0 WHERE status = 1 AND end_date IS NOT NULL AND end_date < ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$today]);
    }

    /**
     * Reset used_count to zero for a coupon (when reusing an inactive code)
     */
    public function resetUsage($id)
    {
        $sql = "UPDATE {$this->table} SET used_count = 0 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
