<?php
/**
 * Model giỏ hàng: thêm/cập nhật/xóa và truy vấn giỏ theo email
 */
class CartModel extends DB
{
    // Lấy danh sách sản phẩm trong giỏ hàng theo email


    /**
     * Thêm hoặc cập nhật giỏ hàng cho 1 sản phẩm + biến thể (nếu có)
     * - $variantId có thể null (sản phẩm không chọn biến thể)
     * - Sử dụng so sánh NULL-safe <=> để phân biệt đúng dòng theo biến thể
     */
    /**
     * Thêm hoặc cập nhật một item trong giỏ (ràng buộc theo email+masp+biến thể)
     * Trả về số lượng mới sau cập nhật, tự động giới hạn theo tồn kho
     */
    public function addOrUpdateCart($email, $masp, $capacityVariantId, $colorVariantId, $soluong, $gia)
    {
        $pdo = $this->db ?? $this->con ?? null;
        if (!$pdo) {
            throw new Exception("Không tìm thấy kết nối PDO trong DB");
        }
        $stmt = $pdo->prepare("SELECT soluong FROM cart WHERE email = ? AND masp = ? AND variant_id <=> ? AND color_variant_id <=> ?");
        $stmt->execute([$email, $masp, $capacityVariantId, $colorVariantId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get available stock for this product
        $stmt2 = $pdo->prepare("SELECT soluong FROM tblsanpham WHERE masp = ?");
        $stmt2->execute([$masp]);
        $prod = $stmt2->fetch(PDO::FETCH_ASSOC);
        $available = $prod ? (int)$prod['soluong'] : 0;

        $newQty = 0;
        if ($row) {
            $current = (int)$row['soluong'];
            $desired = $current + (int)$soluong;
            // Cap desired to available stock
            $newQty = $desired > $available ? $available : $desired;
            if ($newQty > 0) {
                $stmt = $pdo->prepare("UPDATE cart SET soluong = ?, gia = ? WHERE email = ? AND masp = ? AND variant_id <=> ? AND color_variant_id <=> ?");
                $stmt->execute([$newQty, $gia, $email, $masp, $capacityVariantId, $colorVariantId]);
            } else {
                // If no stock left, remove the cart item
                $stmt = $pdo->prepare("DELETE FROM cart WHERE email = ? AND masp = ? AND variant_id <=> ? AND color_variant_id <=> ?");
                $stmt->execute([$email, $masp, $capacityVariantId, $colorVariantId]);
                $newQty = 0;
            }
        } else {
            $insertQty = (int)$soluong;
            if ($insertQty > $available) { $insertQty = $available; }
            if ($insertQty > 0) {
                $stmt = $pdo->prepare("INSERT INTO cart (email, masp, variant_id, color_variant_id, soluong, gia) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$email, $masp, $capacityVariantId, $colorVariantId, $insertQty, $gia]);
                $newQty = $insertQty;
            } else {
                // nothing to insert if no stock
                $newQty = 0;
            }
        }

        return $newQty;
    }
    /**
     * Xóa một item giỏ theo email + masp + biến thể
     */
    public function deleteCartItem($email, $masp, $capacityVariantId = null, $colorVariantId = null)
    {
        $pdo = $this->db ?? $this->con ?? null;
        if (!$pdo) {
            throw new Exception("Không tìm thấy kết nối PDO trong DB");
        }
        $stmt = $pdo->prepare("DELETE FROM cart WHERE email = ? AND masp = ? AND variant_id <=> ? AND color_variant_id <=> ?");
        $stmt->execute([$email, $masp, $capacityVariantId, $colorVariantId]);
        return true;
    }

    /**
     * Cập nhật số lượng item; clamp theo tồn kho
     */
    public function updateCartQty($email, $masp, $capacityVariantId, $colorVariantId, $qty)
    {
        $pdo = $this->db ?? $this->con ?? null;
        if (!$pdo) {
            throw new Exception("Không tìm thấy kết nối PDO trong DB");
        }

        // Cap the requested qty to available stock
        $stmt2 = $pdo->prepare("SELECT soluong FROM tblsanpham WHERE masp = ?");
        $stmt2->execute([$masp]);
        $prod = $stmt2->fetch(PDO::FETCH_ASSOC);
        $available = $prod ? (int)$prod['soluong'] : 0;

        $qty = (int)$qty;
        if ($available <= 0) {
            // No stock: remove item from cart
            $stmt = $pdo->prepare("DELETE FROM cart WHERE email = ? AND masp = ?");
            $stmt->execute([$email, $masp]);
            return true;
        }

        // Clamp to [1, available]
        if ($qty < 1) $qty = 1;
        if ($qty > $available) $qty = $available;

        $stmt = $pdo->prepare("UPDATE cart SET soluong = ? WHERE email = ? AND masp = ? AND variant_id <=> ? AND color_variant_id <=> ?");
        $stmt->execute([$qty, $email, $masp, $capacityVariantId, $colorVariantId]);
        return true;
    }
    /**
     * Đánh dấu note_cart (chọn mua) cho item
     */
    public function updateNoteCart($email, $masp, $capacityVariantId, $colorVariantId, $note_cart)
    {
        $pdo = $this->db ?? $this->con ?? null;
        if (!$pdo) {
            throw new Exception("Không tìm thấy kết nối PDO trong DB");
        }
        $stmt = $pdo->prepare("UPDATE cart SET note_cart = ? WHERE email = ? AND masp = ? AND variant_id <=> ? AND color_variant_id <=> ?");
        $stmt->execute([$note_cart, $email, $masp, $capacityVariantId, $colorVariantId]);
        return true;
    }

    /**
     * Lấy toàn bộ giỏ hàng theo email (tất cả sản phẩm)
     */
    public function getCartByEmail($email)
    {
        $pdo = $this->db ?? $this->con ?? null;
           $stmt = $pdo->prepare("SELECT c.masp, c.variant_id, c.color_variant_id, c.soluong AS qty, c.gia, c.note_cart,
                                   s.tensp, s.hinhanh, s.giaXuat,
                                   pv_cap.name AS capacity_variant_name, pv_cap.variant_type AS capacity_variant_type, pv_cap.price_per_kg,
                                   pv_color.name AS color_variant_name
                               FROM cart c
                               JOIN tblsanpham s ON c.masp = s.masp
                               LEFT JOIN product_variants pv_cap ON pv_cap.id = c.variant_id
                               LEFT JOIN product_variants pv_color ON pv_color.id = c.color_variant_id
                               WHERE c.email = ?");
        $stmt->execute([$email]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cart = [];
        foreach ($rows as $row) {
            $key = $row['masp'] . '#' . ($row['variant_id'] ?? 0) . '#' . ($row['color_variant_id'] ?? 0);
            $basePrice = ($row['capacity_variant_type'] === 'capacity' && $row['price_per_kg'] !== null)
                ? (float)$row['price_per_kg']
                : (float)$row['giaXuat'];
            $cart[$key] = [
                'masp' => $row['masp'],
                'capacity_variant_id' => $row['variant_id'],
                'capacity_variant_name' => $row['capacity_variant_name'],
                'color_variant_id' => $row['color_variant_id'],
                'color_variant_name' => $row['color_variant_name'],
                'tensp' => $row['tensp'],
                'hinhanh' => $row['hinhanh'],
                'giaxuat' => $basePrice,
                'qty' => (int)$row['qty'],
                'phantram' => 0,
                'note_cart' => (int)$row['note_cart']
            ];
        }
        return $cart;
    }

    /**
     * Lấy giỏ hàng chỉ gồm sản phẩm được chọn (note_cart = 1)
     */
    public function getSelectedCart($email)
    {
        $pdo = $this->db ?? $this->con ?? null;
         $stmt = $pdo->prepare("SELECT c.masp, c.variant_id, c.color_variant_id, c.soluong AS qty, c.gia, c.note_cart,
                           s.tensp, s.hinhanh, s.giaXuat,
                           pv_cap.name AS capacity_variant_name, pv_cap.variant_type AS capacity_variant_type, pv_cap.price_per_kg,
                           pv_color.name AS color_variant_name
                       FROM cart c
                       JOIN tblsanpham s ON c.masp = s.masp
                       LEFT JOIN product_variants pv_cap ON pv_cap.id = c.variant_id
                       LEFT JOIN product_variants pv_color ON pv_color.id = c.color_variant_id
                       WHERE c.email = ? AND c.note_cart = 1");
        $stmt->execute([$email]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cart = [];
        foreach ($rows as $row) {
            $key = $row['masp'] . '#' . ($row['variant_id'] ?? 0) . '#' . ($row['color_variant_id'] ?? 0);
            $basePrice = ($row['capacity_variant_type'] === 'capacity' && $row['price_per_kg'] !== null)
                ? (float)$row['price_per_kg']
                : (float)$row['giaXuat'];
            $cart[$key] = [
                'masp' => $row['masp'],
                'capacity_variant_id' => $row['variant_id'],
                'capacity_variant_name' => $row['capacity_variant_name'],
                'color_variant_id' => $row['color_variant_id'],
                'color_variant_name' => $row['color_variant_name'],
                'tensp' => $row['tensp'],
                'hinhanh' => $row['hinhanh'],
                'giaxuat' => $basePrice,
                'qty' => (int)$row['qty'],
                'phantram' => 0,
                'note_cart' => (int)$row['note_cart']
            ];
        }
        return $cart;
    }
    /**
     * Lấy một item giỏ theo khóa chính hợp thành
     */
    public function getCartItem($email, $masp, $capacityVariantId = null, $colorVariantId = null)
    {
        $pdo = $this->db ?? $this->con ?? null;
        if (!$pdo) {
            throw new Exception("Không tìm thấy kết nối PDO trong DB");
        }
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE email = ? AND masp = ? AND variant_id <=> ? AND color_variant_id <=> ?");
        $stmt->execute([$email, $masp, $capacityVariantId, $colorVariantId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Xóa toàn bộ giỏ hàng của 1 email (sau khi thanh toán thành công)
     */
    /**
     * Xóa toàn bộ giỏ hàng của email (sau thanh toán)
     */
    public function clearCartByEmail($email)
    {
        $pdo = $this->db ?? $this->con ?? null;
        if (!$pdo) {
            throw new Exception("Không tìm thấy kết nối PDO trong DB");
        }
        // Chuẩn hoá email (trim + lowercase) để tránh lệch chữ hoa/thường hoặc khoảng trắng
        $normalized = strtolower(trim($email));
        // Xóa case-insensitive
        // Chuẩn hoá email: trim, bỏ CR/LF/TAB, lowercase
        $raw = (string)$email;
        $sanitized = str_replace(["\r","\n","\t"], '', $raw);
        $normalized = strtolower(trim($sanitized));

        // Câu lệnh xóa với chuẩn hoá tương tự phía DB (dùng REPLACE để loại các ký tự ẩn)
        $sql = "DELETE FROM cart WHERE LOWER(TRIM(REPLACE(REPLACE(REPLACE(email, CHAR(13), ''), CHAR(10), ''), CHAR(9), ''))) = LOWER(TRIM(REPLACE(REPLACE(REPLACE(?, CHAR(13), ''), CHAR(10), ''), CHAR(9), '')))";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$normalized]);
        $affected = $stmt->rowCount();
        @error_log('[CART] clearCartByEmail normalized=' . $normalized . ' affected=' . $affected);

        // Fallback: nếu không xóa được gì, thử xóa theo biến thể viết hoa/thường nguyên bản
        if ($affected === 0 && $raw !== $normalized) {
            $stmt2 = $pdo->prepare("DELETE FROM cart WHERE email = ? OR LOWER(TRIM(email)) = LOWER(TRIM(?))");
            $stmt2->execute([$raw, $normalized]);
            @error_log('[CART] fallback delete raw=' . $raw . ' affected=' . $stmt2->rowCount());
        }
        return true;
    }
}
