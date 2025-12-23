<?php
// require_once 'BaseModel.php';
/**
 * Model chi tiết đơn hàng: ghi nhận các dòng sản phẩm trong đơn
 */
class OrderDetailModel extends BaseModel {
    protected $table = 'order_details';

    /**
     * Thêm chi tiết đơn hàng bao gồm thông tin biến thể dung lượng và màu sắc (nếu có)
     */
    public function addOrderDetail(
        $orderId,
        $productId,
        $capacityVariantId,
        $capacityVariantName,
        $colorVariantId,
        $colorVariantName,
        $quantity,
        $price,
        $salePrice,
        $total,
        $image,
        $productName
    ) {
        if (is_null($orderId)) {
            throw new Exception("orderId không được để trống khi thêm chi tiết đơn hàng.");
        }

        // 12 cột -> cần 12 placeholder
        $sql = "INSERT INTO {$this->table} 
            (order_id, product_id, variant_id, variant_name, color_variant_id, color_variant_name, quantity, price, sale_price, total, image, product_name) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        return $this->query($sql, [
            $orderId,
            $productId,
            $capacityVariantId,
            $capacityVariantName,
            $colorVariantId,
            $colorVariantName,
            $quantity,
            $price,
            $salePrice,
            $total,
            $image,
            $productName
        ]);
    }

    /**
     * Lấy danh sách chi tiết theo `order_id` (bao gồm thông tin biến thể)
     */
    public function getOrderDetailsByOrderId($orderId) {
        if (empty($orderId)) return [];
        $sql = "SELECT order_id, product_id, product_name, variant_id, variant_name, color_variant_id, color_variant_name, quantity, price, sale_price, total, image FROM {$this->table} WHERE order_id = ?";
        return $this->select($sql, [$orderId]);
    }
}
