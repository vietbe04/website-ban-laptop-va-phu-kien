<?php
/**
 * Controller quản lý giảm giá hợp nhất
 * - Khuyến mãi theo sản phẩm/loại, mã coupon, ngưỡng giảm giá
 */
class Discount extends Controller {
    /**
     * Hiển thị trang quản lý giảm giá (khuyến mãi/coupon/threshold)
     */
    public function show(){
        // Trang tổng hợp quản lý giảm giá (admin + staff)
        $this->requireRole(['admin','staff'], 'discount');
        $promoModel = $this->model('AdKhuyenMai');
        $productModel = $this->model('AdProducModel');
        $couponModel = $this->model('CouponModel');
        $thresholdModel = $this->model('ThresholdDiscountModel');

        // Dữ liệu cho khuyến mãi
        $products = $productModel->all('tblsanpham');
        $promoList = $promoModel->getAllWithProduct();
        $promoViewTypes = $promoModel->getView();
        // Dữ liệu cho coupon
        $coupons = $couponModel->getAll();
        // Dữ liệu cho ngưỡng giảm giá
        $tiers = $thresholdModel->getAll();

        $this->view('adminPage', [
            'page' => 'DiscountManagerView',
            'products' => $products,
            'promoList' => $promoList,
            'promoViewTypes' => $promoViewTypes,
            'coupons' => $coupons,
            'tiers' => $tiers
        ]);
    }
}
