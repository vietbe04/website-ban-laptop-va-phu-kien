<?php

class Support extends Controller {
    
    public function chat() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }
        
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';
        if (empty($message)) {
            header('Content-Type: application/json');
            echo json_encode(['response' => 'Vui lòng nhập tin nhắn!', 'quickReplies' => []]);
            exit;
        }
        
        $response = $this->getBotResponse($message);
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    private function getBotResponse($message) {
        $messageLower = mb_strtolower($message, 'UTF-8');
        
        // TƯ VẤN SẢN PHẨM
        if (preg_match('/(laptop|máy tính|pc|cpu|ram|vga|ssd|hdd|linh kiện|phụ kiện|màn hình|bàn phím|chuột|tai nghe|monitor)/i', $messageLower)) {
            $response = "💻 **TƯ VẤN SẢN PHẨM**\n\n";
            $response .= "Chúng tôi cung cấp:\n\n";
            $response .= "📱 **LAPTOP & PC:**\n";
            $response .= "- Gaming: MSI, ASUS, Alienware (từ 20-50tr)\n";
            $response .= "- Văn phòng: Dell, HP, Lenovo (5-15tr)\n";
            $response .= "- Máy chủ: HP ProLiant, Dell PowerEdge\n\n";
            $response .= "🔧 **LINH KIỆN:**\n";
            $response .= "- CPU: Intel Core i9, AMD Ryzen\n";
            $response .= "- GPU: RTX 4090, RX 7900 XTX\n";
            $response .= "- RAM: DDR5, Corsair, G.Skill\n";
            $response .= "- SSD: Samsung, WD, SK Hynix\n\n";
            $response .= "Bạn quan tâm loại nào? 💡";
            
            $quickReplies = ['Gaming PC', 'Văn phòng', 'Linh kiện', 'Phụ kiện'];
            return ['response' => $response, 'quickReplies' => $quickReplies];
        }
        
        // KIỂM TRA ĐƠN HÀNG
        if (preg_match('/(đơn hàng|kiểm tra|trạng thái|vận chuyển|giao hàng|tracking)/i', $messageLower)) {
            if (!isset($_SESSION['user_email'])) {
                return [
                    'response' => "🔐 Để kiểm tra đơn hàng, vui lòng đăng nhập.\n\nBạn chưa có tài khoản? Đăng ký ngay để nhận ưu đãi!",
                    'quickReplies' => ['Đăng nhập', 'Đăng ký', 'Chat với admin']
                ];
            }
            
            $response = "📦 **KIỂM TRA ĐƠN HÀNG**\n\n";
            $response .= "Bạn có thể:\n";
            $response .= "✅ Xem lịch sử đơn hàng\n";
            $response .= "✅ Theo dõi tình trạng giao hàng\n";
            $response .= "✅ Xem chi tiết thanh toán\n";
            $response .= "✅ Quản lý đơn hàng trả lại\n\n";
            $response .= "Nhấn 'Xem lịch sử' hoặc nói với tôi mã đơn hàng!";
            
            $quickReplies = ['Xem lịch sử đơn hàng', 'Mã đơn hàng', 'Chat với admin'];
            return ['response' => $response, 'quickReplies' => $quickReplies];
        }
        
        // BẢO HÀNH & ĐỔI TRẢ
        if (preg_match('/(bảo hành|đổi trả|trả lại|lỗi|hỏng|sửa chữa|warranty|repair|return)/i', $messageLower)) {
            $response = "🔧 **CHÍNH SÁCH BẢO HÀNH & ĐỔI TRẢ**\n\n";
            $response .= "📋 **THỜI GIAN BẢO HÀNH:**\n";
            $response .= "🖥️ Laptop/PC: 12-24 tháng\n";
            $response .= "⚙️ CPU/RAM/VGA: 24-36 tháng\n";
            $response .= "💾 SSD/HDD: 12-60 tháng (tùy hãng)\n";
            $response .= "🖱️ Phụ kiện: 6-12 tháng\n\n";
            
            $response .= "🔄 **ĐỔI TRẢ:**\n";
            $response .= "✅ Đổi mới 100%: 7 ngày từ nhận hàng\n";
            $response .= "💰 Trả hoàn tiền: 3 ngày từ nhận hàng\n";
            $response .= "🎁 Lỗi nhà sản xuất: Không thời hạn\n\n";
            
            $response .= "❓ Có vấn đề với sản phẩm? Liên hệ ngay! 👇";
            
            $quickReplies = ['Tôi có hỏng hàng', 'Xem chính sách đầy đủ', 'Chat với admin'];
            return ['response' => $response, 'quickReplies' => $quickReplies];
        }
        
        // KHUYẾN MÃI
        if (preg_match('/(khuyến mãi|giảm giá|sale|discount|deal|promo|ưu đãi|flash sale)/i', $messageLower)) {
            $response = "🎉 **KHUYẾN MÃI HẤP DẪN**\n\n";
            $response .= "💝 **CHƯƠNG TRÌNH HIỆN TẠI:**\n";
            $response .= "🔴 Black Friday: Giảm tới 50% (chọn sản phẩm)\n";
            $response .= "🎁 Mua laptop tặng chuột gaming\n";
            $response .= "💳 Thanh toán thẻ: Hoàn 5% (tối đa 2tr)\n";
            $response .= "🚀 Mua 2 sản phẩm giảm thêm 10%\n\n";
            
            $response .= "📲 **THEO DÕI:**\n";
            $response .= "Ghé trang Khuyến mãi để update liên tục! 🏃‍♂️";
            
            $quickReplies = ['Xem khuyến mãi', 'Laptop yêu thích', 'Chat tư vấn'];
            return ['response' => $response, 'quickReplies' => $quickReplies];
        }
        
        // THANH TOÁN
        if (preg_match('/(thanh toán|payment|tiền|giá|chi phí|cách thanh toán|payment method)/i', $messageLower)) {
            $response = "💳 **PHƯƠNG THỨC THANH TOÁN**\n\n";
            $response .= "🚚 **THANH TOÁN KHI NHẬN HÀNG (COD):**\n";
            $response .= "✅ Không phí, an toàn\n";
            $response .= "✅ Thanh toán sau khi kiểm tra\n\n";
            
            $response .= "🏦 **CHUYỂN KHOẢN NGÂN HÀNG:**\n";
            $response .= "Vietcombank: 123456789\n";
            $response .= "Agribank: 987654321\n";
            $response .= "Tech Bank: 111222333\n\n";
            
            $response .= "💻 **THANH TOÁN ONLINE:**\n";
            $response .= "✅ VNPAY (Hoàn 2% cho thành viên)\n";
            $response .= "✅ Ví Momo, ZaloPay\n";
            $response .= "✅ Credit/Debit card\n\n";
            
            $response .= "🛡️ Tất cả thanh toán đều an toàn & được bảo mật!";
            
            $quickReplies = ['COD', 'Chuyển khoản', 'VNPAY'];
            return ['response' => $response, 'quickReplies' => $quickReplies];
        }
        
        // VẬN CHUYỂN
        if (preg_match('/(vận chuyển|giao hàng|ship|phí ship|miễn phí|delivery|shipping)/i', $messageLower)) {
            $response = "🚚 **CHÍNH SÁCH VẬN CHUYỂN**\n\n";
            $response .= "📦 **CHI PHÍ:**\n";
            $response .= "✅ Miễn phí: Đơn >= 500.000đ\n";
            $response .= "💰 Có phí: Đơn < 500.000đ (từ 30k-100k)\n";
            $response .= "🎁 Thành viên VIP: Miễn phí tất cả\n\n";
            
            $response .= "⏱️ **THỜI GIAN GIAO:**\n";
            $response .= "🏃 Hôm nay (nếu đặt trước 2h chiều)\n";
            $response .= "📅 1-3 ngày làm việc (khu vực khác)\n";
            $response .= "🌍 5-7 ngày (khu vực xa xôi)\n\n";
            
            $response .= "✉️ Bạn sẽ nhận mã tracking qua email & SMS!";
            
            $quickReplies = ['Nơi nhận hàng', 'Theo dõi đơn', 'Chat hỗ trợ'];
            return ['response' => $response, 'quickReplies' => $quickReplies];
        }
        
        // LIÊN HỆ TRỰC TIẾP VỚI ADMIN
        if (preg_match('/(chat.*admin|admin|quản trị|nói chuyện.*người|người thật|nhân viên|customer service)/i', $messageLower)) {
            return [
                'response' => "👨‍💼 **CHAT TRỰC TIẾP VỚI NHÂN VIÊN HỖ TRỢ**\n\n" .
                    "⏰ Giờ làm việc: Thứ 2-7, 8:00-17:00\n" .
                    "📞 Hotline: 1900-1234 (bấm phím 1)\n" .
                    "📧 Email: support@dqv.com\n" .
                    "💬 Chat Facebook: fb.com/dqvcomputer\n\n" .
                    "Chúng tôi sẵn sàng hỗ trợ bạn! 😊",
                'quickReplies' => ['Chat ngay', 'Gọi hotline', 'Gửi email', 'Quay lại']
            ];
        }
        
        // MẶC ĐỊNH - MENU CHÍNH
        $response = "👋 **XRÀO XIN CHÀO!**\n\n";
        $response .= "Tôi là trợ lý ảo của DQV Computer. Tôi có thể giúp:\n\n";
        $response .= "🛍️ Tư vấn sản phẩm\n";
        $response .= "📦 Kiểm tra đơn hàng\n";
        $response .= "💳 Phương thức thanh toán\n";
        $response .= "🚚 Vận chuyển & giao hàng\n";
        $response .= "🔧 Bảo hành & đổi trả\n";
        $response .= "🎉 Khuyến mãi & giảm giá\n";
        $response .= "👨‍💼 Chat với nhân viên hỗ trợ\n\n";
        $response .= "**Bạn cần giúp gì?** 😊";
        
        $quickReplies = ['Sản phẩm', 'Đơn hàng', 'Thanh toán', 'Chat admin'];
        return ['response' => $response, 'quickReplies' => $quickReplies];
    }
}

