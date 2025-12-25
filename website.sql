-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 12, 2025 lúc 02:32 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


-- Ensure charset/collation variables are set to safe defaults (avoid NULL)
SET @OLD_CHARACTER_SET_CLIENT = IFNULL(@@CHARACTER_SET_CLIENT, 'utf8mb4');
SET @OLD_CHARACTER_SET_RESULTS = IFNULL(@@CHARACTER_SET_RESULTS, 'utf8mb4');
SET @OLD_COLLATION_CONNECTION = IFNULL(@@COLLATION_CONNECTION, 'utf8mb4_unicode_ci');
SET NAMES utf8mb4;

--
-- Cơ sở dữ liệu: `website`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `articles`
--

INSERT INTO `articles` (`id`, `title`, `content`, `image`, `status`, `created_at`) VALUES
(3, 'Rò rỉ camera Galaxy S27 Ultra: Không nâng cấp như mong đợi!!!', 'Rò rỉ mới nhất về camera Galaxy S27 Ultra, flagship dự kiến ra mắt vào đầu năm 2027 của Samsung có thể khiến nhiều người thất vọng.\r\n\r\nMặc dù phải đến đầu năm sau, Samsung mới chính thức ra mắt các flagship thuộc dòng Galaxy S26 nhưng mới đây, thông tin camera của model S27 Ultra đã bất ngờ được chia sẻ trên các trang mạng.\r\n\r\nĐã có nhiều đồn đoán cho rằng, Galaxy S27 Ultra cuối cùng bỏ qua cảm biến 200 megapixel 1/1.3 inch quen thuộc mà Samsung đã sử dụng kể từ S23 Ultra để sử dụng máy ảnh mới chất lượng. Tin đồn thú vị nhất liên quan đến LYT-901 mới của Sony — cảm biến 200MP 1/1.12 inch vừa được ra mắt gần đây và đang được kỳ vọng mang đến trải nghiệm nhiếp ảnh tuyệt vời cho người dùng.\r\n\r\nTuy nhiên, theo leaker đáng tin cậy Ice Universe, Samsung dường như sẽ không sử dụng cảm biến này. Nghe thì có vẻ hiển nhiên vì Samsung vốn tự sản xuất cảm biến của riêng mình, nhưng dự đoán của ông cho rằng kích thước cảm biến trên S27 Ultra sẽ không thay đổi lại khá hợp lý.\r\n\r\nThực tế, Samsung đã liên tục thu nhỏ cảm biến 200MP của mình qua từng thế hệ kể từ phiên bản đầu tiên. Nói cách khác, khả năng chúng ta thấy một cảm biến ISOCELL 200MP gần 1 inch (hoặc lớn hơn chút) trong tương lai gần là rất thấp.\r\n\r\nISOCELL HP1: 1/1.22″\r\nISOCELL HP2: 1/1.3″\r\nISOCELL HP3: 1/1.4″\r\nISOCELL HPX / HP9: 1/1.4″\r\nNếu bạn thắc mắc vì sao điều này quan trọng: cảm biến nhỏ hơn với kích thước điểm ảnh nhỏ hơn sẽ tạo ra tín hiệu kém chính xác, từ đó khiến chất lượng ảnh suy giảm. Điều này phù hợp với những gì chúng ta đã thấy trong các bài so sánh camera giữa Galaxy S25 Ultra và các đối thủ Trung Quốc như vivo X300 Pro.\r\n\r\nDo đó, chiếc smartphone cao cấp nhất của Samsung vào năm 2027 có thể một lần nữa tiếp tục sử dụng phiên bản “nâng cấp nhẹ” của cảm biến ISOCELL 1/1.3 inch mà chúng ta đã thấy suốt nhiều năm qua. Cùng chờ xem nhé.\r\n\r\nNguồn: Gizmochina', 'thong-tin-camera-galaxy-s27-ultra-1-20251128160710-f7c65f.jpg', 1, '2025-11-28 16:07:10'),
(4, 'POCO công bố lịch phát hành HyperOS 3: Đây là lịch trình và các thiết bị đủ điều kiện', 'Thương hiệu con của Xiaomi là POCO mới đây vừa xác nhận khung thời gian cũng như các thiết bị sẽ được cập nhật HyperOS 3.\r\n\r\nPOCO vừa chính thức xác nhận khung thời gian phát hành bản cập nhật HyperOS 3. Trước đó, Xiaomi đã công bố danh sách các thiết bị Xiaomi, Redmi và một số mẫu POCO đủ điều kiện nhận bản cập nhật HyperOS tiếp theo. Và mới đây, POCO đã tiếp tục hé lộ lịch trình triển khai HyperOS 3 dành riêng cho các sản phẩm của hãng, giúp người dùng dễ dàng theo dõi và chuẩn bị nâng cấp.\r\n\r\nTheo đó, POCO tại sự kiện ra mắt dòng POCO F8 trên toàn cầu đã tiết lộ lịch trình triển khai HyperOS 3 cho các điện thoại thông minh của mình. Giao diện người dùng này sẽ mang đến những thay đổi ấn tượng sau:\r\n\r\nHyper Island là một bổ sung mới đáng chú ý, có chức năng tương tự như Dynamic Island của Apple. Nó cũng hiển thị các thông báo đang hoạt động và các hoạt động trực tiếp khác.\r\nTích hợp AI sâu hơn với khả năng nhận dạng màn hình được cải thiện, tiện ích thông minh, công cụ viết AI,...\r\n\r\nCác tính năng bảo mật tốt hơn cùng khả năng tương thích tốt hơn với hệ sinh thái Apple.\r\nGiao diện người dùng được cải tiến cho trải nghiệm sử dụng mượt mà và linh hoạt hơn, hình ảnh động tinh tế hơn và tốc độ khởi chạy ứng dụng nhanh hơn.\r\n\r\nDưới đây là lịch trình và các điện thoại POCO được cập nhật HyperOS 3:\r\n\r\nTừ tháng 10 đến tháng 11/2025\r\n\r\nPOCO F7 Ultra\r\nPOCO F7 Pro\r\nPOCO F7\r\nPOCO X7 Pro\r\nPOCO X7 Pro Iron Man Edition\r\nPOCO X7\r\nPOCO F6 Pro\r\nPOCO F6\r\nPOCO M6+\r\nPOCO M6\r\nPOCO M7 Pro\r\nPOCO M7\r\nTừ tháng 12/2025 đến tháng 3/2026\r\n\r\nPOCO F5 Pro\r\nPOCO F5\r\nPOCO F5 X6\r\nPOCO M7 Pro\r\nPOCO C85\r\nPOCO Pad\r\nPOCO Pad X1\r\nPOCO Pad M1', 'Xiaomi-POCO-HyperOS-3-1-20251128160829-e1ede6.jpg', 1, '2025-11-28 16:08:29'),
(5, 'Rò rỉ thông tin Snapdragon 8 Gen 6: Tiến trình 2nm TSMC và CPU Oryon siêu mạnh!', 'Mặc dù Qualcomm chỉ mới ra mắt chip Snapdragon 8 Gen 5 nhưng mới đây, thông tin về phiên bản kế nhiệm của nó là Snapdragon 8 Gen 6 đã được chia sẻ trên các trang mạng.\r\n\r\nMột báo cáo mới vừa tiết lộ cho chúng ta những thông tin đáng chú ý về vi xử lý Snapdragon 8 Gen 6, dự kiến sẽ được sử dụng trên các thiết bị di động trong tương lai.\r\n\r\nCụ thể, leaker nổi tiếng Digital Chat Station vừa tiết lộ thông tin cho biết Snapdragon 8 Gen 6 sẽ áp dụng quy trình sản xuất 2nm tiên tiến của TSMC cùng kiến trúc CPU Oryon mạnh mẽ với thiết kế  2+3+3. Đây có thể là nỗ lực nhằm đưa hiệu năng chuẩn flagship xuống các thiết bị có mức giá khoảng 550 USD (tương đương 14.5 triệu đồng).\r\n\r\nDCS cho biết dù CPU của Snapdragon 8 Gen 6 vẫn giữ nguyên thông số chuẩn flagship, phần GPU có thể sẽ được điều chỉnh nhẹ so với các phiên bản “Ultra”. Điều này giúp chipset duy trì sức mạnh xử lý ấn tượng đồng thời tối ưu khả năng tản nhiệt trên tiến trình 2nm mới.\r\n\r\nCác phân tích thị trường cho thấy mẫu chip mới này được định hướng để phục vụ hiệu quả phân khúc cận cao cấp. Nó dự kiến sẽ xuất hiện trên các thiết bị có mức giá dưới 20 triệu đồng. Nhờ tiến trình 2nm của TSMC, các nhà sản xuất có thể mang đến cho người dùng trải nghiệm cao cấp với hiệu suất mạnh mẽ mà không phải gánh mức chi phí quá cao như những mẫu flagship siêu cao cấp.\r\n\r\nNguồn: xiaomitime', 'thong-tin-snapdragon-8-gen-6-cover-20251128160927-aea40b.jpg', 1, '2025-11-28 16:09:27'),
(6, 'OPPO Reno15 Pro Max vừa đạt chứng nhận quan trọng, sắp ra mắt toàn cầu?', 'Rò rỉ mới nhất cho thấy OPPO đang có kế hoạch ra mắt điện thoại Reno15 Pro Max trên toàn cầu trong thời gian tới.\r\n\r\nĐầu tháng này, OPPO đã ra mắt Reno15 và Reno15 Pro tại Trung Quốc. Tiếp theo, thương hiệu này sẽ công bố Reno 15c tại thị trường nội địa. Đối với thị trường toàn cầu, dòng Reno15 dường như sẽ bao gồm tối đa 4 model, bao gồm phiên bản Reno15 Pro Max hoàn toàn mới.\r\n\r\nCụ thể, một rò rỉ vừa tiết lộ cho chúng ta số model các điện thoại Reno15 dành cho thị trường toàn cầu:\r\n\r\nReno15 (CPH2825)\r\nReno15 F (CPH2801)\r\nReno15 Pro (CPH2813)\r\nReno15 Pro Max (CPH2811)\r\n\r\nẢnh chụp màn hình ở trên cho thấy cơ quan chứng nhận NBTC của Thái Lan đã phê duyệt Reno15, Reno 15 Pro và Reno 15 Pro Max. Ngoài ra, cơ quan EEC của Châu Âu, TDRA của UAE và các nền tảng chứng nhận TUV đã phê duyệt Reno15, 15F và 15 Pro, qua đó xác nhận chúng sẽ hỗ trợ sạc nhanh có dây 80W.\r\n\r\nDanh sách TKDN của Indonesia đã tiết lộ Reno 15F, trong khi nền tảng chứng nhận IMDA của Singapore liệt kê cả ba mẫu máy, nhưng vẫn chưa phê duyệt Reno15 Pro Max.\r\n\r\nCác chứng nhận trên cũng cho thấy không phải tất cả 4 điện thoại Reno15 sắp tới đều có mặt ở tất cả các khu vực. Với Reno15 Pro Max, các tin đồn trước đó cho biết điện thoại này sẽ có màn hình OLED LTPO 1.5K 120Hz 6,78 inch, chip Dimensity 9400, cụm ba camera sau 200 megapixel, camera trước 50 megapixel và pin 6,500mAh.\r\n\r\nNguồn: Gizmochina', 'Oppo-Reno-15-Pro-NBTC-cover-20251128161019-172b33.jpg', 1, '2025-11-28 16:10:19'),
(7, 'OPPO A6x lộ giá cực mềm: chip Dimensity 6300, pin 6500 mAh chỉ từ 3.69 triệu!', 'Rò rỉ mới nhất vừa tiết lộ cho chúng ta thông tin giá bán mẫu điện thoại giá rẻ tiếp theo mà OPPO đang phát triển, có tên gọi là OPPO A6x.\r\n\r\nTheo các báo cáo xuất hiện thời gian qua, OPPO đang phát triển một chiếc điện thoại A-Series mới, có tên gọi là OPPO A6x. Hôm nay, thông tin giá bán của sản phẩm này đã được tiết lộ\r\n\r\nCụ thể, leaker nổi tiếng Abhishek Yadav vừa tiết lộ thông tin cho biết OPPO A6x sẽ sớm ra mắt với 3 phiên bản cấu hình bộ nhớ là 4GB + 64GB, 4GB + 128GB và 6GB + 128GB. Chúng có giá lần lượt là 12,499 INR (khoảng 3.69 triệu đồng), 13,499 INR (khoảng 3.98 triệu đồng) và 14,999 INR (khoảng 4.43 triệu đồng).\r\n\r\nVề cấu hình, các rò rỉ trước đó cho biết OPPO A6x sẽ được trang bị màn hình LCD có kích thước 6.75 inch, hỗ trợ độ phân giải HD+ và tần số quét 120Hz. Bên trong, mẫu máy giá rẻ này có thể được trang bị chip MediaTek Dimensity 6300, pin dung lượng lớn 6,500mAh với hỗ trợ sạc nhanh có dây 45W.\r\n\r\nVề khả năng nhiếp ảnh, OPPO A6x sẽ có camera sau 13MP với cảm biến phụ VGA, trong khi camera trước 5MP cho ảnh selfie và gọi video. Chiếc điện thoại giá rẻ này chạy sẵn hệ điều hành Android 15 với giao diện tùy chỉnh ColorOS 15. Các tính năng đáng chú ý khác bao gồm khả năng kháng nước và chống bụi chuẩn IP64, thân máy mỏng 8.58mm, nặng 212 gram.\r\n\r\nNguồn: Gizmochina', 'gia-oppo-a6x-1-20251128161055-e96a89.jpg', 1, '2025-11-28 16:10:55'),
(8, 'Insta360 chốt ngày ra mắt Antigravity A1: Drone 360 độ trọng lượng dưới 250g', 'Insta360 vừa xác nhận sẽ mở bán mẫu flycam Antigravity A1 vào ngày 4/12, gây chú ý nhờ tích hợp camera 360 độ trong thân hình nhỏ gọn dưới 250g.\r\n\r\nInsta360 vừa tung ra đoạn video teaser chính thức, xác nhận ngày ra mắt của mẫu drone đầu tiên mang tên Antigravity A1.\r\n\r\nTheo đó, sản phẩm sẽ bắt đầu nhận đơn đặt hàng từ ngày 4/12/2025. Dù mức giá cụ thể và các gói phụ kiện đi kèm chưa được công bố, nhưng thông tin kỹ thuật xác nhận đây là dòng drone có trọng lượng dưới 250g - mức trọng lượng lý tưởng với các quy định đăng ký bay khắt khe tại nhiều quốc gia.\r\n\r\nĐiểm khác biệt lớn nhất của Antigravity A1 so với các đối thủ từ DJI nằm ở hệ thống camera 360 độ thừa hưởng từ các dòng action cam của hãng. Công nghệ này cho phép áp dụng quy trình \"ghi hình trước, chọn khung hình sau\" (shoot first, frame later).\r\n\r\nThay vì phải căng thẳng căn chỉnh góc máy trong khi điều khiển bay, người dùng có thể ghi lại toàn bộ không gian xung quanh và thoải mái chọn lại góc quay, tỷ lệ khung hình mong muốn thông qua ứng dụng chỉnh sửa sau khi chuyến bay kết thúc.\r\n\r\nVề trải nghiệm bay, Antigravity A1 hướng tới phong cách FPV (góc nhìn thứ nhất) khi kết hợp cùng kính Goggles và bộ điều khiển chuyển động (Motion Controller). Thiết bị được trang bị đầy đủ các tính năng an toàn như định vị GPS để tự động quay về (Return-to-home) và cảm biến vật thể, giúp đơn giản hóa thao tác điều khiển cho người mới bắt đầu.\r\n\r\nSự xuất hiện của Antigravity A1 cũng làm cộng đồng đổ dồn sự chú ý về tin đồn việc DJI đang phát triển một mẫu drone tương tự có tên DJI Avata 360. Theo các nguồn tin rò rỉ, sản phẩm đối trọng từ DJI dự kiến sở hữu cảm biến kép 1/1.1 inch, hỗ trợ quay video 8K 50fps và chụp ảnh tĩnh 38MP. Cuộc đua drone tích hợp camera 360 độ dự kiến sẽ trở nên sôi động ngay trong dịp cuối năm nay.\r\n\r\nNguồn: Tech Radar', 'insta360-ngay-ra-mat-antigravity-a1-thumb-20251128161509-77910c.jpg', 1, '2025-11-28 16:15:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `article_comments`
--

CREATE TABLE `article_comments` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `article_comments`
--

INSERT INTO `article_comments` (`id`, `article_id`, `user_id`, `fullname`, `content`, `status`, `created_at`) VALUES
(0, 8, 16, 'Nguyễn Thị Tường Vy', 'Hữu ích', 1, '2025-12-11 16:12:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `article_likes`
--

CREATE TABLE `article_likes` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `article_likes`
--

INSERT INTO `article_likes` (`id`, `article_id`, `user_id`, `created_at`) VALUES
(0, 1, 16, '2025-11-27 15:28:19'),
(0, 2, 20, '2025-11-28 10:28:21'),
(0, 1, 20, '2025-11-28 10:28:34'),
(0, 8, 16, '2025-11-28 21:33:41'),
(0, 8, 26, '2025-11-28 23:34:13'),
(0, 6, 16, '2025-12-12 00:20:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `masp` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `color_variant_id` int(11) DEFAULT NULL,
  `soluong` int(11) NOT NULL,
  `gia` int(20) NOT NULL,
  `note_cart` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chat_conversations`
--

CREATE TABLE `chat_conversations` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `admin_email` varchar(255) DEFAULT NULL,
  `status` enum('waiting','assigned','closed') DEFAULT 'waiting',
  `topic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chat_conversations`
--

INSERT INTO `chat_conversations` (`id`, `session_id`, `user_email`, `user_name`, `admin_email`, `status`, `topic`, `created_at`, `updated_at`) VALUES
(9, 'm51h1rf7hg02t4v8utde50fonl', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', 'nttv9604@gmail.com', 'assigned', 'Hỗ trợ trực tiếp', '2025-12-11 13:21:55', '2025-12-11 16:31:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) DEFAULT NULL,
  `sender` varchar(255) DEFAULT NULL,
  `sender_type` enum('user','admin','bot') DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `conversation_id`, `sender`, `sender_type`, `message`, `is_read`, `created_at`) VALUES
(31, 9, 'Nguyễn Thị Tường Vy', 'user', 'Chat với admin', 1, '2025-12-11 13:21:55'),
(32, 9, 'Nguyễn Thị Tường Vy', 'user', 'Tôi cần hỗ trợ', 1, '2025-12-11 13:22:07'),
(33, 9, 'Nguyễn Thị Tường Vy', 'admin', 'Bạn cần chúng tôi hỗ trợ về vấn đề j', 1, '2025-12-11 13:22:33'),
(34, 9, 'Nguyễn Thị Tường Vy', 'user', 'Tôi muốn hỏi về chính sách bảo hàng của mày Laptop ASUS TUF Gaming F16 FX607VJ-RL034W', 1, '2025-12-11 13:23:22'),
(35, 9, 'Nguyễn Thị Tường Vy', 'admin', 'Dạ về sản phẩm ASUS của bên em đều được bảo hàng 24 tháng và 1 đổi 1 trong vòng 30 ngày ạ', 1, '2025-12-11 13:24:53'),
(38, 9, 'Nguyễn Thị Tường Vy', 'user', 'Chat với admin', 0, '2025-12-11 16:31:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(64) NOT NULL,
  `type` enum('percent','fixed') NOT NULL DEFAULT 'percent',
  `value` int(11) NOT NULL DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `min_total` int(11) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `type`, `value`, `start_date`, `end_date`, `status`, `min_total`, `usage_limit`, `used_count`, `created_at`) VALUES
(6, '111', 'percent', 5, '2025-11-28', '2025-12-04', 0, 20000000, NULL, 0, '2025-11-28 15:51:08'),
(7, '222', 'percent', 10, '2025-11-28', '2025-12-06', 0, NULL, NULL, 3, '2025-11-28 16:14:37');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khuyenmai`
--

CREATE TABLE `khuyenmai` (
  `km_id` int(100) NOT NULL,
  `maLoaiSP` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `masp` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `phantram` int(11) NOT NULL,
  `ngaybatdau` date NOT NULL,
  `ngayketthuc` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `khuyenmai`
--

INSERT INTO `khuyenmai` (`km_id`, `maLoaiSP`, `masp`, `phantram`, `ngaybatdau`, `ngayketthuc`) VALUES
(17, 'Laptop ASUS', '001', 5, '2025-11-28', '2025-12-18');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_code` varchar(50) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `user_email` varchar(100) DEFAULT NULL,
  `receiver` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `transaction_info` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `created_at`, `user_email`, `receiver`, `phone`, `address`, `transaction_info`) VALUES
(86, 24, 'HD1764319455', 41990000.00, '2025-11-28 15:44:15', 'tuongvy9062004@gmail.com', 'Bn', '123456789', 'kbdsvsgsk', 'dathantoan'),
(87, 20, 'HD1764338576', 41990000.00, '2025-11-28 21:02:56', 'quocviet16114@gmail.com', 'Dương Quốc Việt 123', '0987654321', 'aghsjhđsjfbj', 'chothanhtoan'),
(88, 16, 'HD1764338838', 20700500.00, '2025-11-28 21:07:18', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '0987654321', '1234567890', 'dathanhtoan| dathanhtoan'),
(89, 16, 'HD1764338967', 20700500.00, '2025-11-28 21:09:27', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '0987654321', '0987654321', 'chothanhtoan'),
(90, 16, 'HD1764340116', 20700500.00, '2025-11-28 21:28:36', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '0987654321', '0987654321', 'chothanhtoan'),
(91, 16, 'HD1764340146', 20700500.00, '2025-11-28 21:29:06', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '0987654321', '0987654321', 'dathanhtoan| dathanhtoan'),
(92, 16, 'HD1764340330', 20700500.00, '2025-11-28 21:32:10', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '0987654321', '0987654321', 'dathanhtoan| dathanhtoan'),
(93, 16, 'HD1764340552', 38940500.00, '2025-11-28 21:35:52', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '0987654321', '0987654321', 'chothanhtoan'),
(94, 16, 'HD1764347412', 86577727.00, '2025-11-28 23:30:12', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '1', '1', 'dathantoan| coupon:222 (-9619748) | threshold:5'),
(95, 16, 'HD1764925424', 98886925.00, '2025-12-05 16:03:44', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '0123456789', 'sgfdhgfhdfgdf', 'payment:bank|shipping:home|notify_email:1|notify_s'),
(96, 16, 'HD1764932606', 23790500.00, '2025-12-05 18:03:26', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '0123456789', 'edgrdgr', 'chothanhtoan | pickup:Nhận tại cửa hàng(+0)'),
(97, 16, 'HD1764932759', 18630450.00, '2025-12-05 18:05:59', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '0123456789', 'fgtrgf', 'chonhantaicuahang | coupon:222 (-2070050) | pickup'),
(98, 16, 'HD1764932833', 20700500.00, '2025-12-05 18:07:13', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '0123456789', 'bbbbb', 'chonhantaicuahang | pickup:Nhận tại cửa hàng(+0)'),
(99, 16, 'HD1764933633', 20490000.00, '2025-12-05 18:20:33', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '0123456789', 'a', 'chonhantaicuahang | pickup:Nhận tại cửa hàng(+0)'),
(100, 16, 'HD1764941247', 15020000.00, '2025-12-05 20:27:27', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '0123456789', 'sádfd', 'chothanhtoan | shipping:Giao hàng tiêu chuẩn(+3000'),
(101, 16, 'HD1764941300', 13990000.00, '2025-12-05 20:28:20', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '0123456789', '111111', 'chonhantaicuahang | pickup:Nhận tại cửa hàng(+0)'),
(102, 16, 'HD1764942488', 42040000.00, '2025-12-05 20:48:08', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '0123456789', 'aa', 'chothanhtoan | shipping:Giao hàng nhanh(+50000)'),
(103, 16, 'HD1764942543', 20540000.00, '2025-12-05 20:49:03', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '0123456789', 'bbbbbb', 'dathanhtoan| shipping:Giao hàng nhanh(+50000) | '),
(104, 16, 'HD1764942748', 19720000.00, '2025-12-05 20:52:28', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '534354', 'ccccc', 'dathanhtoan| shipping:Giao hàng tiêu chuẩn(+3000'),
(105, 16, 'HD1764943185', 25040000.00, '2025-12-05 20:59:45', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '0123456789', 'nnnnn', 'dathanhtoan| shipping:Giao hàng nhanh(+50000) | '),
(106, 16, 'HD1764943420', 20750500.00, '2025-12-05 21:03:40', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '0987654321', 'dddd', 'dathanhtoan| shipping:Giao hàng nhanh(+50000) | '),
(107, 16, 'HD1764943694', 14040000.00, '2025-12-05 21:08:14', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '235345', '11111', 'dathanhtoan| shipping:Giao hàng nhanh(+50000) | '),
(108, 16, 'HD1764943833', 14040000.00, '2025-12-05 21:10:33', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '534354', 'aaaaaa', 'dathanhtoan| shipping:Giao hàng nhanh(+50000) | '),
(109, 16, 'HD1764943939', 20490000.00, '2025-12-05 21:12:19', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '1', '1', 'dathanhtoan|pickup:Nhận tại cửa hàng(+0)'),
(110, 16, 'HD1764943978', 20730500.00, '2025-12-05 21:12:58', 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '534354', 'sdfsaghesr', 'dathanhtoan| shipping:Giao hàng tiêu chuẩn(+3000');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `variant_name` varchar(100) DEFAULT NULL,
  `color_variant_id` int(11) DEFAULT NULL,
  `color_variant_name` varchar(100) DEFAULT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `sale_price` decimal(15,2) NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `product_type` varchar(100) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `variant_id`, `variant_name`, `color_variant_id`, `color_variant_name`, `quantity`, `price`, `sale_price`, `total`, `image`, `product_type`, `product_name`) VALUES
(118, '86', '004', NULL, NULL, NULL, NULL, 1.000, 41990000.00, 41990000.00, 41990000.00, '4.webp', NULL, 'Laptop ASUS ROG Strix G16 G615JMR-S5155W'),
(119, '87', '004', NULL, NULL, NULL, NULL, 1.000, 41990000.00, 41990000.00, 41990000.00, '4.webp', NULL, 'Laptop ASUS ROG Strix G16 G615JMR-S5155W'),
(120, '88', '001', NULL, NULL, NULL, NULL, 1.000, 21790000.00, 20700500.00, 20700500.00, '1.webp', NULL, 'Laptop ASUS TUF Gaming F16 FX607VJ-RL034W'),
(121, '89', '001', NULL, NULL, NULL, NULL, 1.000, 21790000.00, 20700500.00, 20700500.00, '1.webp', NULL, 'Laptop ASUS TUF Gaming F16 FX607VJ-RL034W'),
(122, '90', '001', NULL, NULL, NULL, NULL, 1.000, 21790000.00, 20700500.00, 20700500.00, '1.webp', NULL, 'Laptop ASUS TUF Gaming F16 FX607VJ-RL034W'),
(123, '91', '001', NULL, NULL, NULL, NULL, 1.000, 21790000.00, 20700500.00, 20700500.00, '1.webp', NULL, 'Laptop ASUS TUF Gaming F16 FX607VJ-RL034W'),
(124, '92', '001', NULL, NULL, NULL, NULL, 1.000, 21790000.00, 20700500.00, 20700500.00, '1.webp', NULL, 'Laptop ASUS TUF Gaming F16 FX607VJ-RL034W'),
(125, '93', '001', 11, 'VGA 8GB RTX5070, I7 14650HX, IPS 165Hz 100%sRGB, RAM 16GB, Ổ cứng 1T, Màn Hình 16', NULL, NULL, 1.000, 40990000.00, 38940500.00, 38940500.00, '1.webp', NULL, 'Laptop ASUS TUF Gaming F16 FX607VJ-RL034W'),
(126, '94', '001', NULL, NULL, NULL, NULL, 1.000, 21790000.00, 20700500.00, 20700500.00, '1.webp', NULL, 'Laptop ASUS TUF Gaming F16 FX607VJ-RL034W'),
(127, '94', '002', NULL, NULL, NULL, NULL, 1.000, 20490000.00, 20490000.00, 20490000.00, '2.webp', NULL, 'Laptop ASUS TUF Gaming A15 FA506NCG-HN184W'),
(128, '94', '003', NULL, NULL, NULL, NULL, 1.000, 14990000.00, 14990000.00, 14990000.00, '3.webp', NULL, 'Laptop ASUS Vivobook 15 X1502VA-BQ885W'),
(129, '94', '004', NULL, NULL, NULL, NULL, 1.000, 41990000.00, 41990000.00, 41990000.00, '4.webp', NULL, 'Laptop ASUS ROG Strix G16 G615JMR-S5155W'),
(130, '94', '016', NULL, NULL, NULL, NULL, 1.000, 3090000.00, 3090000.00, 3090000.00, '16.webp', NULL, 'Tai nghe Bluetooth Apple AirPods 4 | Chính hãng Apple Việt Nam'),
(131, '95', '001', NULL, NULL, NULL, NULL, 3.000, 21790000.00, 20700500.00, 62101500.00, '1.webp', NULL, 'Laptop ASUS TUF Gaming F16 FX607VJ-RL034W'),
(132, '95', '004', NULL, NULL, NULL, NULL, 1.000, 41990000.00, 41990000.00, 41990000.00, '4.webp', NULL, 'Laptop ASUS ROG Strix G16 G615JMR-S5155W'),
(133, '96', '001', NULL, NULL, NULL, NULL, 1.000, 21790000.00, 20700500.00, 20700500.00, '1.webp', NULL, 'Laptop ASUS TUF Gaming F16 FX607VJ-RL034W'),
(134, '96', '016', NULL, NULL, NULL, NULL, 1.000, 3090000.00, 3090000.00, 3090000.00, '16.webp', NULL, 'Tai nghe Bluetooth Apple AirPods 4 | Chính hãng Apple Việt Nam'),
(135, '97', '001', NULL, NULL, NULL, NULL, 1.000, 21790000.00, 20700500.00, 20700500.00, '1.webp', NULL, 'Laptop ASUS TUF Gaming F16 FX607VJ-RL034W'),
(136, '98', '001', NULL, NULL, NULL, NULL, 1.000, 21790000.00, 20700500.00, 20700500.00, '1.webp', NULL, 'Laptop ASUS TUF Gaming F16 FX607VJ-RL034W'),
(137, '99', '002', NULL, NULL, NULL, NULL, 1.000, 20490000.00, 20490000.00, 20490000.00, '2.webp', NULL, 'Laptop ASUS TUF Gaming A15 FA506NCG-HN184W'),
(138, '100', '003', NULL, NULL, NULL, NULL, 1.000, 14990000.00, 14990000.00, 14990000.00, '3.webp', NULL, 'Laptop ASUS Vivobook 15 X1502VA-BQ885W'),
(139, '101', '008', NULL, NULL, NULL, NULL, 1.000, 13990000.00, 13990000.00, 13990000.00, '8.webp', NULL, 'Mac mini M4 2024 10CPU 10GPU 16GB 256GB | Chính hãng Apple Việt Nam'),
(140, '102', '004', NULL, NULL, NULL, NULL, 1.000, 41990000.00, 41990000.00, 41990000.00, '4.webp', NULL, 'Laptop ASUS ROG Strix G16 G615JMR-S5155W'),
(141, '103', '002', NULL, NULL, NULL, NULL, 1.000, 20490000.00, 20490000.00, 20490000.00, '2.webp', NULL, 'Laptop ASUS TUF Gaming A15 FA506NCG-HN184W'),
(142, '104', '006', NULL, NULL, NULL, NULL, 1.000, 19690000.00, 19690000.00, 19690000.00, '6.webp', NULL, 'Apple MacBook Air M2 2024 8CPU 8GPU 16GB 256GB I Chính hãng Apple Việt Nam'),
(143, '105', '007', NULL, NULL, NULL, NULL, 1.000, 24990000.00, 24990000.00, 24990000.00, '7.webp', NULL, 'MacBook Air M4 13 inch 2025 10CPU 8GPU 16GB 256GB | Chính hãng Apple Việt Nam'),
(144, '106', '001', NULL, NULL, NULL, NULL, 1.000, 21790000.00, 20700500.00, 20700500.00, '1.webp', NULL, 'Laptop ASUS TUF Gaming F16 FX607VJ-RL034W'),
(145, '107', '008', NULL, NULL, NULL, NULL, 1.000, 13990000.00, 13990000.00, 13990000.00, '8.webp', NULL, 'Mac mini M4 2024 10CPU 10GPU 16GB 256GB | Chính hãng Apple Việt Nam'),
(146, '108', '008', NULL, NULL, NULL, NULL, 1.000, 13990000.00, 13990000.00, 13990000.00, '8.webp', NULL, 'Mac mini M4 2024 10CPU 10GPU 16GB 256GB | Chính hãng Apple Việt Nam'),
(147, '109', '002', NULL, NULL, NULL, NULL, 1.000, 20490000.00, 20490000.00, 20490000.00, '2.webp', NULL, 'Laptop ASUS TUF Gaming A15 FA506NCG-HN184W'),
(148, '110', '001', NULL, NULL, NULL, NULL, 1.000, 21790000.00, 20700500.00, 20700500.00, '1.webp', NULL, 'Laptop ASUS TUF Gaming F16 FX607VJ-RL034W');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_thresholds`
--

CREATE TABLE `order_thresholds` (
  `id` int(11) NOT NULL,
  `min_total` int(11) NOT NULL,
  `percent` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_thresholds`
--

INSERT INTO `order_thresholds` (`id`, `min_total`, `percent`, `status`, `created_at`) VALUES
(3, 100000000, 5, 1, '2025-11-28 22:51:23');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `masp` varchar(20) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`id`, `masp`, `filename`, `is_main`, `created_at`) VALUES
(16, '001', '1_11_1764311093_1134.webp', 0, '2025-11-28 13:24:53'),
(17, '001', '1_10_1764311093_6233.webp', 0, '2025-11-28 13:24:53'),
(18, '001', '1_8_1764311093_8550.webp', 0, '2025-11-28 13:24:53'),
(19, '001', '1_7_1764311093_1078.webp', 0, '2025-11-28 13:24:53'),
(20, '001', '1_6_1764311093_6695.webp', 0, '2025-11-28 13:24:53'),
(21, '001', '1_5_1764311093_9143.webp', 0, '2025-11-28 13:24:53'),
(22, '001', '1_4_1764311093_8537.webp', 0, '2025-11-28 13:24:53'),
(23, '001', '1_3_1764311093_2830.webp', 0, '2025-11-28 13:24:53'),
(24, '001', '1_2_1764311093_5208.webp', 0, '2025-11-28 13:24:53'),
(25, '002', '2_10_1764312715_1177.webp', 0, '2025-11-28 13:51:55'),
(26, '002', '2_9_1764312715_8127.webp', 0, '2025-11-28 13:51:55'),
(27, '002', '2_8_1764312715_8188.webp', 0, '2025-11-28 13:51:55'),
(28, '002', '2_7_1764312715_6066.webp', 0, '2025-11-28 13:51:55'),
(29, '002', '2_6_1764312715_1777.webp', 0, '2025-11-28 13:51:55'),
(30, '002', '2_5_1764312715_9685.webp', 0, '2025-11-28 13:51:55'),
(31, '002', '2_4_1764312715_9306.webp', 0, '2025-11-28 13:51:55'),
(32, '002', '2_3_1764312715_4710.webp', 0, '2025-11-28 13:51:55'),
(33, '002', '2_2_1764312715_4411.webp', 0, '2025-11-28 13:51:55'),
(34, '002', '2_1_1764312715_7148.webp', 0, '2025-11-28 13:51:55'),
(35, '003', '3_10_1764313350_3301.webp', 0, '2025-11-28 14:02:30'),
(36, '003', '3_9_1764313350_9050.webp', 0, '2025-11-28 14:02:30'),
(37, '003', '3_8_1764313350_8058.webp', 0, '2025-11-28 14:02:30'),
(38, '003', '3_7_1764313350_3564.webp', 0, '2025-11-28 14:02:30'),
(39, '003', '3_6_1764313350_9782.webp', 0, '2025-11-28 14:02:30'),
(40, '003', '3_5_1764313350_9813.webp', 0, '2025-11-28 14:02:30'),
(41, '003', '3_4_1764313350_6409.webp', 0, '2025-11-28 14:02:30'),
(42, '003', '3_3_1764313350_7835.webp', 0, '2025-11-28 14:02:30'),
(43, '003', '3_2_1764313350_5816.webp', 0, '2025-11-28 14:02:30'),
(44, '003', '3_1_1764313350_6509.webp', 0, '2025-11-28 14:02:30'),
(45, '004', '4_10_1764314518_6812.webp', 0, '2025-11-28 14:21:58'),
(46, '004', '4_9_1764314518_8619.webp', 0, '2025-11-28 14:21:58'),
(47, '004', '4_8_1764314518_9567.webp', 0, '2025-11-28 14:21:58'),
(48, '004', '4_7_1764314518_7877.webp', 0, '2025-11-28 14:21:58'),
(49, '004', '4_6_1764314518_5828.webp', 0, '2025-11-28 14:21:58'),
(50, '004', '4_5_1764314518_5084.webp', 0, '2025-11-28 14:21:58'),
(51, '004', '4_4_1764314518_3293.webp', 0, '2025-11-28 14:21:58'),
(52, '004', '4_3_1764314518_5905.webp', 0, '2025-11-28 14:21:58'),
(53, '004', '4_2_1764314518_5651.webp', 0, '2025-11-28 14:21:58'),
(54, '004', '4_1_1764314518_6510.webp', 0, '2025-11-28 14:21:58'),
(55, '005', '5_6_1764314785_2562.webp', 0, '2025-11-28 14:26:25'),
(56, '005', '5_5_1764314785_2607.webp', 0, '2025-11-28 14:26:25'),
(57, '005', '5_4_1764314785_6155.webp', 0, '2025-11-28 14:26:25'),
(58, '005', '5_3_1764314785_5831.webp', 0, '2025-11-28 14:26:25'),
(59, '005', '5_2_1764314785_5990.webp', 0, '2025-11-28 14:26:25'),
(60, '005', '5_1_1764314785_6024.webp', 0, '2025-11-28 14:26:25'),
(61, '006', '6_9_1764315192_5908.webp', 0, '2025-11-28 14:33:12'),
(62, '006', '6_8_1764315192_1392.webp', 0, '2025-11-28 14:33:12'),
(63, '006', '6_7_1764315192_8902.webp', 0, '2025-11-28 14:33:12'),
(64, '006', '6_6_1764315192_2064.webp', 0, '2025-11-28 14:33:12'),
(65, '006', '6_5_1764315192_5444.webp', 0, '2025-11-28 14:33:12'),
(66, '006', '6_4_1764315192_4477.webp', 0, '2025-11-28 14:33:12'),
(67, '006', '6_3_1764315192_1450.webp', 0, '2025-11-28 14:33:12'),
(68, '006', '6_2_1764315192_4027.webp', 0, '2025-11-28 14:33:12'),
(69, '006', '6_1_1764315192_1425.webp', 0, '2025-11-28 14:33:12'),
(70, '007', '7_10_1764315624_8102.webp', 0, '2025-11-28 14:40:24'),
(71, '007', '7_9_1764315624_1175.webp', 0, '2025-11-28 14:40:24'),
(72, '007', '7_8_1764315624_6202.webp', 0, '2025-11-28 14:40:24'),
(73, '007', '7_7_1764315624_8944.webp', 0, '2025-11-28 14:40:24'),
(74, '007', '7_6_1764315624_2611.webp', 0, '2025-11-28 14:40:24'),
(75, '007', '7_5_1764315624_6681.webp', 0, '2025-11-28 14:40:24'),
(76, '007', '7_4_1764315624_8344.webp', 0, '2025-11-28 14:40:24'),
(77, '007', '7_3_1764315624_2509.webp', 0, '2025-11-28 14:40:24'),
(78, '007', '7_2_1764315624_8060.webp', 0, '2025-11-28 14:40:24'),
(79, '007', '7_1_1764315624_3731.webp', 0, '2025-11-28 14:40:24'),
(80, '008', '8_7_1764316026_8443.webp', 0, '2025-11-28 14:47:06'),
(81, '008', '8_6_1764316026_8108.webp', 0, '2025-11-28 14:47:06'),
(82, '008', '8_5_1764316026_7220.webp', 0, '2025-11-28 14:47:06'),
(83, '008', '8_4_1764316026_2313.webp', 0, '2025-11-28 14:47:06'),
(84, '008', '8_3_1764316026_6787.webp', 0, '2025-11-28 14:47:06'),
(85, '008', '8_2_1764316026_7268.webp', 0, '2025-11-28 14:47:06'),
(86, '008', '8_1_1764316026_6932.webp', 0, '2025-11-28 14:47:06'),
(87, '009', '9_7_1764316403_6859.webp', 0, '2025-11-28 14:53:23'),
(88, '009', '9_6_1764316403_5146.webp', 0, '2025-11-28 14:53:23'),
(89, '009', '9_5_1764316403_7071.webp', 0, '2025-11-28 14:53:23'),
(90, '009', '9_4_1764316403_1072.webp', 0, '2025-11-28 14:53:23'),
(91, '009', '9_3_1764316403_9790.webp', 0, '2025-11-28 14:53:23'),
(92, '009', '9_2_1764316403_9062.webp', 0, '2025-11-28 14:53:23'),
(93, '009', '9_1_1764316403_6539.webp', 0, '2025-11-28 14:53:23'),
(94, '010', '10_10_1764316820_4100.webp', 0, '2025-11-28 15:00:20'),
(95, '010', '10_9_1764316820_1062.webp', 0, '2025-11-28 15:00:20'),
(96, '010', '10_8_1764316820_3595.webp', 0, '2025-11-28 15:00:20'),
(97, '010', '10_7_1764316820_4476.webp', 0, '2025-11-28 15:00:20'),
(98, '010', '10_6_1764316820_5362.webp', 0, '2025-11-28 15:00:20'),
(99, '010', '10_5_1764316820_8020.webp', 0, '2025-11-28 15:00:20'),
(100, '010', '10_4_1764316820_6656.webp', 0, '2025-11-28 15:00:20'),
(101, '010', '10_3_1764316820_8693.webp', 0, '2025-11-28 15:00:20'),
(102, '010', '10_2_1764316820_2204.png', 0, '2025-11-28 15:00:20'),
(103, '010', '10_1_1764316820_2966.webp', 0, '2025-11-28 15:00:20'),
(104, '011', '11_7_1764317342_4611.webp', 0, '2025-11-28 15:09:02'),
(105, '011', '11_6_1764317342_5231.webp', 0, '2025-11-28 15:09:02'),
(106, '011', '11_5_1764317342_8589.webp', 0, '2025-11-28 15:09:02'),
(107, '011', '11_4_1764317342_2505.webp', 0, '2025-11-28 15:09:02'),
(108, '011', '11_3_1764317342_1252.webp', 0, '2025-11-28 15:09:02'),
(109, '011', '11_2_1764317342_5087.webp', 0, '2025-11-28 15:09:02'),
(110, '011', '11_1_1764317342_7084.webp', 0, '2025-11-28 15:09:02'),
(111, '012', '12_10_1764317674_7380.webp', 0, '2025-11-28 15:14:34'),
(112, '012', '12_9_1764317674_7081.webp', 0, '2025-11-28 15:14:34'),
(113, '012', '12_8_1764317674_6620.webp', 0, '2025-11-28 15:14:34'),
(114, '012', '12_7_1764317674_3773.webp', 0, '2025-11-28 15:14:34'),
(115, '012', '12_6_1764317674_2200.webp', 0, '2025-11-28 15:14:34'),
(116, '012', '12_5_1764317674_6598.webp', 0, '2025-11-28 15:14:34'),
(117, '012', '12_4_1764317674_7075.webp', 0, '2025-11-28 15:14:34'),
(118, '012', '12_3_1764317674_2334.webp', 0, '2025-11-28 15:14:34'),
(119, '012', '12_2_1764317674_2631.webp', 0, '2025-11-28 15:14:34'),
(120, '012', '12_1_1764317674_8606.webp', 0, '2025-11-28 15:14:34'),
(121, '013', '14_7_1764318038_1354.webp', 0, '2025-11-28 15:20:38'),
(122, '013', '14_6_1764318038_6517.webp', 0, '2025-11-28 15:20:38'),
(123, '013', '14_5_1764318038_8326.png', 0, '2025-11-28 15:20:38'),
(124, '013', '13_4_1764318038_5647.webp', 0, '2025-11-28 15:20:38'),
(125, '013', '13_3_1764318038_8267.webp', 0, '2025-11-28 15:20:38'),
(126, '013', '13_2_1764318038_3323.webp', 0, '2025-11-28 15:20:38'),
(127, '013', '13_1_1764318038_6701.webp', 0, '2025-11-28 15:20:38'),
(128, '014', '14_8_1764318257_4167.webp', 0, '2025-11-28 15:24:17'),
(129, '014', '14_7_1764318257_9239.webp', 0, '2025-11-28 15:24:17'),
(130, '014', '14_6_1764318257_3958.webp', 0, '2025-11-28 15:24:17'),
(131, '014', '14_5_1764318257_9359.webp', 0, '2025-11-28 15:24:17'),
(132, '014', '14_4_1764318257_2117.webp', 0, '2025-11-28 15:24:17'),
(133, '014', '14_3_1764318257_2873.webp', 0, '2025-11-28 15:24:17'),
(134, '014', '14_2_1764318257_7475.webp', 0, '2025-11-28 15:24:17'),
(135, '014', '14_1_1764318257_4516.webp', 0, '2025-11-28 15:24:17'),
(136, '015', '15.8_1764318451_1504.webp', 0, '2025-11-28 15:27:31'),
(137, '015', '15.7_1764318451_6645.webp', 0, '2025-11-28 15:27:31'),
(138, '015', '15.6_1764318451_9584.webp', 0, '2025-11-28 15:27:31'),
(139, '015', '15.5_1764318451_4001.webp', 0, '2025-11-28 15:27:31'),
(140, '015', '15.4_1764318451_9857.webp', 0, '2025-11-28 15:27:31'),
(141, '015', '15.3_1764318451_1885.webp', 0, '2025-11-28 15:27:31'),
(142, '015', '15.2_1764318451_9781.webp', 0, '2025-11-28 15:27:31'),
(143, '015', '15.1_1764318451_5115.webp', 0, '2025-11-28 15:27:31'),
(144, '016', '16_7_1764318786_3958.webp', 0, '2025-11-28 15:33:06'),
(145, '016', '16_6_1764318786_1146.webp', 0, '2025-11-28 15:33:06'),
(146, '016', '16_5_1764318786_8283.webp', 0, '2025-11-28 15:33:06'),
(147, '016', '16_4_1764318786_9956.webp', 0, '2025-11-28 15:33:06'),
(148, '016', '16_3_1764318786_8053.webp', 0, '2025-11-28 15:33:06'),
(149, '016', '16_2_1764318786_6912.webp', 0, '2025-11-28 15:33:06'),
(150, '016', '16_1_1764318786_1441.webp', 0, '2025-11-28 15:33:06'),
(151, '017', '17_7_1764318979_4310.webp', 0, '2025-11-28 15:36:19'),
(152, '017', '17_6_1764318979_9648.webp', 0, '2025-11-28 15:36:19'),
(153, '017', '17_5_1764318979_6937.webp', 0, '2025-11-28 15:36:19'),
(154, '017', '17_4_1764318979_2349.webp', 0, '2025-11-28 15:36:19'),
(155, '017', '17_3_1764318979_1002.webp', 0, '2025-11-28 15:36:19'),
(156, '017', '17_2_1764318979_6103.webp', 0, '2025-11-28 15:36:19'),
(157, '017', '17_1_1764318979_5235.webp', 0, '2025-11-28 15:36:19'),
(158, '018', '18_7_1764319117_7260.webp', 0, '2025-11-28 15:38:37'),
(159, '018', '18_6_1764319117_9909.webp', 0, '2025-11-28 15:38:37'),
(160, '018', '18_5_1764319117_9465.webp', 0, '2025-11-28 15:38:37'),
(161, '018', '18_4_1764319117_7838.webp', 0, '2025-11-28 15:38:37'),
(162, '018', '18_3_1764319117_2243.webp', 0, '2025-11-28 15:38:37'),
(163, '018', '18_2_1764319117_4637.webp', 0, '2025-11-28 15:38:37'),
(164, '018', '18_1_1764319117_4331.webp', 0, '2025-11-28 15:38:37'),
(165, '019', '19_8_1764319349_1202.webp', 0, '2025-11-28 15:42:29'),
(166, '019', '19_7_1764319349_4340.webp', 0, '2025-11-28 15:42:29'),
(167, '019', '19_6_1764319349_8376.webp', 0, '2025-11-28 15:42:29'),
(168, '019', '19_5_1764319349_4276.webp', 0, '2025-11-28 15:42:29'),
(169, '019', '19_4_1764319349_6037.webp', 0, '2025-11-28 15:42:29'),
(170, '019', '19_3_1764319349_7693.webp', 0, '2025-11-28 15:42:29'),
(171, '019', '19_2_1764319349_9606.webp', 0, '2025-11-28 15:42:29'),
(172, '019', '19_1_1764319349_6409.webp', 0, '2025-11-28 15:42:29'),
(173, '020', '20_5_1764319531_2736.webp', 0, '2025-11-28 15:45:31'),
(174, '020', '20_4_1764319532_6154.webp', 0, '2025-11-28 15:45:32'),
(175, '020', '20_3_1764319532_7569.webp', 0, '2025-11-28 15:45:32'),
(176, '020', '20_2_1764319532_3781.webp', 0, '2025-11-28 15:45:32'),
(177, '020', '20_1_1764319532_8379.webp', 0, '2025-11-28 15:45:32'),
(178, '021', '21_5_1764319692_4833.webp', 0, '2025-11-28 15:48:12'),
(179, '021', '21_4_1764319692_1441.webp', 0, '2025-11-28 15:48:12'),
(180, '021', '21_3_1764319692_1559.webp', 0, '2025-11-28 15:48:12'),
(181, '021', '21_2_1764319692_2700.webp', 0, '2025-11-28 15:48:12'),
(182, '021', '21_1_1764319692_2426.webp', 0, '2025-11-28 15:48:12'),
(183, '022', '22_3_1764319822_6809.webp', 0, '2025-11-28 15:50:22'),
(184, '022', '22_2_1764319822_6924.webp', 0, '2025-11-28 15:50:22'),
(185, '022', '22_1_1764319822_9185.webp', 0, '2025-11-28 15:50:22'),
(186, '023', '23_5_1764320070_7271.webp', 0, '2025-11-28 15:54:30'),
(187, '023', '23_4_1764320070_5085.webp', 0, '2025-11-28 15:54:30'),
(188, '023', '23_3_1764320070_9103.webp', 0, '2025-11-28 15:54:30'),
(189, '023', '23_2_1764320070_7288.webp', 0, '2025-11-28 15:54:30'),
(190, '023', '23_1_1764320070_4916.webp', 0, '2025-11-28 15:54:30'),
(191, '024', '24_7_1764320449_9905.webp', 0, '2025-11-28 16:00:49'),
(192, '024', '24_6_1764320449_4015.webp', 0, '2025-11-28 16:00:49'),
(193, '024', '24_5_1764320449_4851.webp', 0, '2025-11-28 16:00:49'),
(194, '024', '24_4_1764320449_5630.webp', 0, '2025-11-28 16:00:49'),
(195, '024', '24_3_1764320449_1668.webp', 0, '2025-11-28 16:00:49'),
(196, '024', '24_2_1764320449_1055.webp', 0, '2025-11-28 16:00:49'),
(197, '024', '24_1_1764320449_3079.webp', 0, '2025-11-28 16:00:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `fullname` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `product_id` varchar(100) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text DEFAULT NULL,
  `images` text DEFAULT NULL COMMENT 'JSON array ch???a ???????ng d???n c??c ???nh ????nh gi??',
  `approved` tinyint(4) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `email`, `fullname`, `product_id`, `rating`, `comment`, `images`, `approved`, `created_at`) VALUES
(2, 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '001', 5, 'tốt', NULL, 1, '2025-11-28 22:03:35'),
(4, 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', '002', 5, 'tốt', '[\"review_002_1764944930_0.jpg\",\"review_002_1764944930_1.jpg\",\"review_002_1764944930_2.jpg\",\"review_002_1764944930_3.jpg\",\"review_002_1764944930_4.jpg\"]', 1, '2025-12-05 21:28:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `masp` varchar(20) NOT NULL,
  `variant_type` enum('color','capacity') NOT NULL DEFAULT 'color',
  `name` varchar(100) NOT NULL,
  `price_per_kg` int(11) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_variants`
--

INSERT INTO `product_variants` (`id`, `masp`, `variant_type`, `name`, `price_per_kg`, `active`, `created_at`) VALUES
(9, '001', 'color', 'Xám', NULL, 1, '2025-11-28 13:25:34'),
(10, '001', 'color', 'Đen', NULL, 1, '2025-11-28 13:25:41'),
(11, '001', 'capacity', 'VGA 8GB RTX5070, I7 14650HX, IPS 165Hz 100%sRGB, RAM 16GB, Ổ cứng 1T, Màn Hình 16', 40990000, 1, '2025-11-28 13:27:29'),
(12, '001', 'capacity', 'VGA 4GB RTX3050, R7-7445HS, ÍP 144Hz 45%NTSC  RAM 16GB, Ổ cứng 512GB, Màn Hình 15.6', 20490000, 1, '2025-11-28 13:48:52'),
(13, '002', 'color', 'Đen', NULL, 1, '2025-11-28 13:52:12'),
(14, '002', 'color', 'Xám', NULL, 1, '2025-11-28 13:52:16'),
(15, '002', 'capacity', 'VGA 6GB RTX3050, CORE 5-210H, IPS 144Hz 45%NTSC, RAM 8GB, Ổ cứng 512GB, Màn Hình 16', 21790000, 1, '2025-11-28 13:54:04'),
(16, '002', 'capacity', 'VGA 8GB RTX5060, i7-14650HX, IPS 165Hz 100%sRGB, RAM 16GB, Ổ cứng 1T, Màn hình 16', 35990000, 1, '2025-11-28 13:56:59'),
(17, '003', 'color', 'Bạc', NULL, 1, '2025-11-28 14:02:45'),
(18, '003', 'color', 'Đen', NULL, 1, '2025-11-28 14:02:48'),
(19, '003', 'capacity', 'CORE 5-120U, RAM 16GB, Màn hình 15.6', 15990000, 1, '2025-11-28 14:05:18'),
(20, '003', 'capacity', 'CORE 3-1215U, RAM 8GB, Màn hình 15.6', 9990000, 1, '2025-11-28 14:06:12'),
(21, '004', 'color', 'Đen', NULL, 1, '2025-11-28 14:22:15'),
(22, '004', 'capacity', 'VGA 24GB RTX5090, CORE U9-275HX, RAM64GB, Ổ cứng 4T, Màn hình 16', 141990000, 1, '2025-11-28 14:23:43'),
(23, '005', 'color', 'Be', NULL, 1, '2025-11-28 14:26:47'),
(24, '005', 'color', 'Trắng', NULL, 1, '2025-11-28 14:26:52'),
(25, '005', 'color', 'Xanh', NULL, 1, '2025-11-28 14:26:56'),
(26, '005', 'color', 'Xám', NULL, 1, '2025-11-28 14:26:59'),
(27, '005', 'color', 'Đen', NULL, 1, '2025-11-28 14:27:03'),
(28, '005', 'capacity', 'CORE U5-125H, RAM 16GB, Ổ cứng 512GB, Màn hình 14', 25490000, 1, '2025-11-28 14:28:06'),
(29, '005', 'capacity', 'CORE U9-285H, RAM 32GB, Ổ cứng 1T, Màn hình 14 có cảm ứng', 34490000, 1, '2025-11-28 14:29:55'),
(30, '006', 'color', 'Đen', NULL, 1, '2025-11-28 14:33:54'),
(31, '006', 'color', 'Trắng vàng', NULL, 1, '2025-11-28 14:34:01'),
(32, '006', 'color', 'Bạc', NULL, 1, '2025-11-28 14:34:05'),
(33, '006', 'color', 'Xám', NULL, 1, '2025-11-28 14:34:09'),
(34, '006', 'capacity', '8CPU-8GPU, 8GB-256GB', 17490000, 1, '2025-11-28 14:34:56'),
(35, '007', 'color', 'Ánh sao', NULL, 1, '2025-11-28 14:41:35'),
(36, '007', 'color', 'Đêm xanh thẳm', NULL, 1, '2025-11-28 14:41:45'),
(37, '007', 'color', 'Bạc', NULL, 1, '2025-11-28 14:41:51'),
(38, '007', 'color', 'Xanh da trời', NULL, 1, '2025-11-28 14:42:00'),
(39, '007', 'capacity', '10CPU-10GPU, 24GB-1TB, Sạc 70W', 39490000, 1, '2025-11-28 14:42:43'),
(40, '007', 'capacity', '10CPU-10GPU, 24GB-512GB, Sạc 70W', 35490000, 1, '2025-11-28 14:43:26'),
(41, '007', 'capacity', '10CPU-10GPU, 16GB-1TB', 34990000, 1, '2025-11-28 14:44:18'),
(42, '008', 'color', 'Bạc', NULL, 1, '2025-11-28 14:47:36'),
(43, '008', 'capacity', '10CPU-10GPU, 32GB-1TB', 34490000, 1, '2025-11-28 14:48:00'),
(44, '008', 'capacity', '10CPU-10GPU, 24GB-1TB', 29290000, 1, '2025-11-28 14:48:27'),
(45, '008', 'capacity', '10CPU-10GPU, 24GB-256GB', 19290000, 1, '2025-11-28 14:48:51'),
(46, '009', 'color', 'Bạc', NULL, 1, '2025-11-28 14:55:04'),
(47, '009', 'color', 'Đen', NULL, 1, '2025-11-28 14:55:07'),
(48, '009', 'capacity', '14CPU-20GPU, 48GB-1TB, Nano-Sạc 96W', 72990000, 1, '2025-11-28 14:55:46'),
(49, '009', 'capacity', '14CPU-20GPU, 48GB-1TB', 67190000, 1, '2025-11-28 14:56:22'),
(50, '009', 'capacity', '14CPU-20GPU, 48GB-512GB, Sạc 96W', 66690000, 1, '2025-11-28 14:57:15'),
(51, '010', 'color', 'Ánh sao', NULL, 1, '2025-11-28 15:00:35'),
(52, '010', 'color', 'Xanh da trời', NULL, 1, '2025-11-28 15:00:41'),
(53, '010', 'color', 'Bạc', NULL, 1, '2025-11-28 15:00:45'),
(54, '010', 'color', 'Đêm xanh thẳm', NULL, 1, '2025-11-28 15:00:51'),
(55, '010', 'capacity', '10CPU-10GPU, 24GB-512GB, Sạc 70W', 39590000, 1, '2025-11-28 15:01:43'),
(56, '010', 'capacity', '10CPU-10GPU, 24GB-256GB, Sạc 70W', 36990000, 1, '2025-11-28 15:02:44'),
(57, '010', 'capacity', '10CPU-10GPU, 16GB-512GB, Sạc 70W', 33990000, 1, '2025-11-28 15:03:21'),
(58, '011', 'color', 'Đen', NULL, 1, '2025-11-28 15:09:17'),
(59, '011', 'capacity', 'VGA 8GB RTX5070, CORE i9-14900HX, IPS 165Hz 100%DCI-P3, RAM 32GB, Ổ cứng 1T', 45490000, 1, '2025-11-28 15:11:39'),
(61, '015', 'color', 'Đen', NULL, 1, '2025-11-28 15:28:10'),
(62, '015', 'capacity', 'VGA 8GB RTX5070, i9-14900HX, IPS 165Hz 100%DCI-P3, RAM 32GB, Ổ cứng 1TB', 45490000, 1, '2025-11-28 15:29:13'),
(63, '024', 'color', 'Xám đen', NULL, 1, '2025-11-28 16:01:10'),
(64, '024', 'color', 'Hông', NULL, 1, '2025-11-28 16:01:13'),
(65, '024', 'color', 'Xám khói', NULL, 1, '2025-11-28 16:01:25');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL COMMENT 'Mã nhà cung cấp',
  `name` varchar(255) NOT NULL COMMENT 'Tên nhà cung cấp',
  `contact_person` varchar(100) DEFAULT NULL COMMENT 'Người liên hệ',
  `phone` varchar(20) DEFAULT NULL COMMENT 'Số điện thoại',
  `email` varchar(100) DEFAULT NULL COMMENT 'Email',
  `address` text DEFAULT NULL COMMENT 'Địa chỉ',
  `tax_code` varchar(50) DEFAULT NULL COMMENT 'Mã số thuế',
  `bank_account` varchar(100) DEFAULT NULL COMMENT 'Số tài khoản ngân hàng',
  `bank_name` varchar(255) DEFAULT NULL COMMENT 'Tên ngân hàng',
  `status` tinyint(1) DEFAULT 1 COMMENT 'Trạng thái: 1=hoạt động, 0=ngừng hợp tác',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Thông tin nhà cung cấp';

--
-- Đang đổ dữ liệu cho bảng `suppliers`
--

INSERT INTO `suppliers` (`id`, `code`, `name`, `contact_person`, `phone`, `email`, `address`, `tax_code`, `bank_account`, `bank_name`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'NCC001', 'Công ty ASUS Việt Nam', 'Nguyễn Văn A', '0901234567', 'contact@asus.vn', '123 Điện Biên Phủ, Q.Bình Thạnh, TP.HCM', '0123456789', NULL, NULL, 1, NULL, '2025-12-05 23:24:48', '2025-12-05 23:24:48'),
(2, 'NCC002', 'Công ty MSI Việt Nam', 'Trần Thị B', '0902345678', 'info@msi.vn', '456 Lê Lợi, Q.1, TP.HCM', '0987654321', NULL, NULL, 1, NULL, '2025-12-05 23:24:48', '2025-12-05 23:24:48'),
(3, 'NCC003', 'Apple Vietnam Co., Ltd', 'Lê Văn C', '0903456789', 'sales@apple.vn', '789 Nguyễn Huệ, Q.1, TP.HCM', '0111222333', NULL, NULL, 1, NULL, '2025-12-05 23:24:48', '2025-12-05 23:24:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `supplier_contracts`
--

CREATE TABLE `supplier_contracts` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL COMMENT 'ID nhà cung cấp',
  `contract_number` varchar(100) NOT NULL COMMENT 'Số hợp đồng',
  `contract_name` varchar(255) NOT NULL COMMENT 'Tên hợp đồng',
  `start_date` date NOT NULL COMMENT 'Ngày bắt đầu',
  `end_date` date DEFAULT NULL COMMENT 'Ngày kết thúc',
  `contract_value` decimal(15,2) DEFAULT 0.00 COMMENT 'Giá trị hợp đồng',
  `payment_terms` text DEFAULT NULL COMMENT 'Điều khoản thanh toán',
  `delivery_terms` text DEFAULT NULL COMMENT 'Điều khoản giao hàng',
  `status` enum('active','expired','terminated') DEFAULT 'active' COMMENT 'Trạng thái: active=hiệu lực, expired=hết hạn, terminated=chấm dứt',
  `file_path` varchar(255) DEFAULT NULL COMMENT 'Đường dẫn file hợp đồng',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Hợp đồng nhà cung cấp';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `supplier_products`
--

CREATE TABLE `supplier_products` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL COMMENT 'ID nhà cung cấp',
  `product_code` varchar(50) DEFAULT NULL COMMENT 'Mã sản phẩm của NCC',
  `product_name` varchar(255) NOT NULL COMMENT 'Tên sản phẩm',
  `category` varchar(100) DEFAULT NULL COMMENT 'Danh mục',
  `unit` varchar(50) DEFAULT NULL COMMENT 'Đơn vị tính',
  `unit_price` decimal(15,2) DEFAULT 0.00 COMMENT 'Đơn giá',
  `currency` varchar(10) DEFAULT 'VND' COMMENT 'Đơn vị tiền tệ',
  `min_order_quantity` int(11) DEFAULT 1 COMMENT 'Số lượng đặt hàng tối thiểu',
  `lead_time_days` int(11) DEFAULT 0 COMMENT 'Thời gian giao hàng (ngày)',
  `warranty_period` varchar(100) DEFAULT NULL COMMENT 'Thời gian bảo hành',
  `status` tinyint(1) DEFAULT 1 COMMENT 'Trạng thái: 1=còn cung cấp, 0=ngừng cung cấp',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Danh mục hàng hóa cung cấp';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblfeedback`
--

CREATE TABLE `tblfeedback` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `admin_reply` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `answered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tblfeedback`
--

INSERT INTO `tblfeedback` (`id`, `user_email`, `fullname`, `subject`, `content`, `status`, `admin_reply`, `created_at`, `updated_at`, `answered_at`) VALUES
(5, 'nttv9604@gmail.com', 'Nguyễn Thị Tường Vy', 'sản phẩm', 'hỏng', 0, NULL, '2025-12-05 11:07:09', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblloaisp`
--

CREATE TABLE `tblloaisp` (
  `maLoaiSP` varchar(20) NOT NULL,
  `tenLoaiSP` varchar(50) NOT NULL,
  `moTaLoaiSP` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tblloaisp`
--

INSERT INTO `tblloaisp` (`maLoaiSP`, `tenLoaiSP`, `moTaLoaiSP`) VALUES
('Laptop ASUS', 'Laptop ASUS', 'Bảo hành 24 tháng. 1 đổi 1 trong 30 ngày'),
('Laptop MSI', 'Laptop MSI', 'Bảo hành 24 tháng. 1 đổi 1 trong 30 ngày'),
('MACBOOK', 'MACBOOK', 'Bảo hành 12 tháng. 1 đổi 1 trong 30 ngày'),
('Phụ kiện', 'Phụ kiện', 'Bảo hành 12 tháng. 1 đổi 1 trong 30 ngày');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblsanpham`
--

CREATE TABLE `tblsanpham` (
  `maLoaiSP` varchar(20) NOT NULL,
  `masp` varchar(20) NOT NULL,
  `tensp` varchar(200) NOT NULL,
  `hinhanh` varchar(50) NOT NULL,
  `soluong` int(11) NOT NULL DEFAULT 0,
  `giaNhap` int(11) NOT NULL,
  `giaXuat` int(11) NOT NULL,
  `mota` varchar(5000) NOT NULL,
  `createDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tblsanpham`
--

INSERT INTO `tblsanpham` (`maLoaiSP`, `masp`, `tensp`, `hinhanh`, `soluong`, `giaNhap`, `giaXuat`, `mota`, `createDate`) VALUES
('Laptop ASUS', '001', 'Laptop ASUS TUF Gaming F16 FX607VJ-RL034W', '1.webp', 141, 16500000, 21790000, 'Loại card đồ họa	\r\nNVIDIA GeForce RTX 3050 6GB GDDR6\r\nIntel Iris Xe Graphics\r\n\r\nDung lượng RAM	\r\n16GB\r\n\r\nLoại RAM	\r\nDDR4-3200 SO-DIMM\r\n\r\nSố khe ram	\r\n2 khe (1 x 16GB, nâng cấp tối đa 64GB)\r\n\r\nỔ cứng	\r\n512GB PCIe 4.0 NVMe M.2 SSD (2 Khe cắm M.2 hỗ trợ SATA hoặc NVMe, tối đa 2TB)\r\n\r\nKích thước màn hình	\r\n16 inches\r\n\r\nCông nghệ màn hình	\r\nĐộ sáng 300nits\r\nĐộ phủ màu NTSC 45%\r\nMàn hình chống chói\r\n\r\nPin	\r\n56WHrs, 4S1P, 4-cell Li-ion\r\n\r\nHệ điều hành	\r\nWindows 11 Home\r\n\r\nĐộ phân giải màn hình	\r\n1920 x 1200 pixels (WUXGA)\r\n\r\nLoại CPU	\r\nIntel Core 5 210H 2.2 GHz (12MB Cache, up to 4.8 GHz, 8 lõi, 12 luồng)\r\n\r\nCổng giao tiếp	\r\n1x Cổng LAN RJ45\r\n1x USB 3.2 Gen 2 Type-C hỗ trợ DisplayPort / cấp nguồn (tốc độ dữ liệu lên đến 10Gbps)\r\n2x USB 3.2 Gen 1 Type-A (tốc độ dữ liệu lên đến 5Gbps)\r\n1x HDMI 2.1 FRL\r\n1x 3.5mm Combo Audio Jack', '2025-11-28'),
('Laptop ASUS', '002', 'Laptop ASUS TUF Gaming A15 FA506NCG-HN184W', '2.webp', 127, 16950000, 20490000, 'Loại card đồ họa	\r\nNVIDIA GeForce RTX 3050 4GB GDDR6 Up to 1675MHz at 60W (75W with Dynamic Boost)\r\nAMD Radeon Graphics\r\n\r\nDung lượng RAM	\r\n16GB\r\n\r\nLoại RAM	\r\nDDR5-5600 SO-DIMM\r\n\r\nSố khe ram	\r\n2 khe (nâng cấp tối đa 64GB)\r\n\r\nỔ cứng	\r\n512GB PCIe 4.0 NVMe M.2 SSD )2 Khe cắm M.2 hỗ trợ cả SATA hoặc NVMe, tối đa 1TB)\r\n\r\nKích thước màn hình	\r\n15.6 inches\r\n\r\nCông nghệ màn hình	\r\nĐộ sáng 250nits\r\nĐộ phủ màu NTSC 45% / SRGB 62.5% / Adobe 47.34%\r\nMàn hình chống chói\r\nAdaptive-Sync\r\n\r\nPin	\r\n48WHrs, 3S1P, 3-cell Li-ion\r\n\r\nHệ điều hành	\r\nWindows 11 Home\r\n\r\nĐộ phân giải màn hình	\r\n1920 x 1080 pixels (FullHD)\r\n\r\nLoại CPU	\r\nAMD Ryzen 7 7445HS 3.2GHz (22MB Cache, up to 4.7GHz, 6 lõi, 12 luồng)\r\n\r\nCổng giao tiếp	\r\n1x cổng LAN RJ45\r\n1x cổng USB 3.2 Gen 2 Type-C hỗ trợ DisplayPort (tốc độ dữ liệu lên đến 10Gbps)\r\n3x cổng USB 3.2 Gen 1 Type-A (tốc độ dữ liệu lên đến 5Gbps)\r\n1x cổng HDMI 2.1 TMDS\r\n1x 3.5mm Combo Audio Jack', '2025-11-28'),
('Laptop ASUS', '003', 'Laptop ASUS Vivobook 15 X1502VA-BQ885W', '3.webp', 69, 10490000, 14990000, 'Loại card đồ họa	\r\nIntel HD Graphics\r\n\r\nDung lượng RAM	\r\n16GB\r\n\r\nLoại RAM	\r\nDDR4\r\n\r\nSố khe ram	\r\n2 khe (8GB DDR4 on board + 8GB DDR4 SO-DIMM, Có thể nâng cấp; Cần tháo vỏ dưới/trên)\r\n\r\nỔ cứng	\r\n512GB M.2 NVMe PCIe 4.0 SSD\r\n\r\nKích thước màn hình	\r\n15.6 inches\r\n\r\nCông nghệ màn hình	\r\nĐộ sáng 250nits\r\nĐộ phủ màu 45% NTSC\r\nMàn hình chống chói\r\nTÜV Rheinland-certified\r\n\r\nPin	\r\n42WHrs, 3S1P, 3-cell Li-ion\r\n\r\nHệ điều hành	\r\nWindows 11 Home\r\n\r\nĐộ phân giải màn hình	\r\n1920 x 1080 pixels (FullHD)\r\n\r\nLoại CPU	\r\nIntel Core i5-13420H 2.1 GHz (12MB Cache, up to 4.6 GHz, 8 lõi, 12 luồng)\r\n\r\nCổng giao tiếp	\r\n1x USB 2.0 Type-A (480Mbps)\r\n1x USB 3.2 Gen 1 Type-C hỗ trợ cấp nguồn (5Gbps)\r\n2x USB 3.2 Gen 1 Type-A (5Gbps)\r\n1x HDMI 1.4\r\n1x Giắc cắm âm thanh kết hợp 3.5mm\r\n1x DC-in', '2025-11-28'),
('Laptop ASUS', '004', 'Laptop ASUS ROG Strix G16 G615JMR-S5155W', '4.webp', 58, 35490000, 41990000, 'Loại card đồ họa	\r\nNVIDIA GeForce RTX 5060 8GB GDDR7\r\nIntel UHD Graphics\r\n\r\nDung lượng RAM	\r\n32GB\r\n\r\nLoại RAM	\r\nDDR5-5600 SO-DIMM, tốc độ bộ nhớ của hệ thống thay đổi tùy theo CPU\r\n\r\nSố khe ram	\r\n2x SO-DIMM (Tối đa 64GB)\r\n\r\nỔ cứng	\r\n1TB PCIe 4.0 NVMe M.2 SSD (2 Khe cắm M.2 hỗ trợ SATA hoặc NVMe, tối đa 2TB)\r\n\r\nKích thước màn hình	\r\n16 inches\r\n\r\nCông nghệ màn hình	\r\nĐộ sáng 500nits\r\nĐộ phủ màu DCI-P3 100%\r\nMàn hình chống chói\r\nG-Sync\r\nROG Nebula Display\r\nDolby Vision HDR\r\nROG intelligent cooling\r\n\r\nPin	\r\n90WHrs, 4S1P, 4-cell Li-ion\r\n\r\nHệ điều hành	\r\nWindows 11 Home\r\n\r\nĐộ phân giải màn hình	\r\n2560 x 1600 (WQXGA)\r\n\r\nLoại CPU	\r\nIntel Core i7 14650HX 2.2 GHz (30MB Cache, up to 5.2 GHz, 16 lõi, 24 luồng)\r\n\r\nCổng giao tiếp	\r\n1x LAN RJ45\r\n1x Thunderbolt 4 hỗ trợ DisplayPort / Sạc / G-SYNC (40Gbps)\r\n1x USB 3.2 Gen 2 Type-C hỗ trợ DisplayPort / Sạc (10Gbps)\r\n3x USB 3.2 Gen 2 Type-A (10Gbps)\r\n1x HDMI 2.1 FRL\r\n1x Giắc cắm âm thanh kết hợp 3.5mm', '2025-11-28'),
('Laptop ASUS', '005', 'Laptop ASUS Zenbook S 16 UM5606KA-RK127WS', '5.webp', 40, 30090000, 35990000, 'Chip AI	\r\nAMD XDNA NPU up to 50TOPS\r\n\r\nLoại card đồ họa	\r\nAMD Radeon Graphics\r\n\r\nDung lượng RAM	\r\n24GB\r\n\r\nLoại RAM	\r\nLPDDR5X Onboard\r\n\r\nỔ cứng	\r\n1TB M.2 NVMe PCIe 4.0 SSD\r\n\r\nKích thước màn hình	\r\n16 inches\r\n\r\nCông nghệ màn hình	\r\nThời gian phản hồi 0.2ms\r\nĐộ sáng 400nits\r\nĐộ sáng tối đa 500nits HDR\r\nĐộ phủ màu 100% DCI-P3\r\nHDR True Black 500\r\n1.07 tỷ màu\r\nGiảm 70% ánh sáng xanh có hại\r\nTÜV Rheinland-certified\r\nSGS Eye Care Display\r\n\r\nPin	\r\n78WHrs, 2S2P, 4-cell Li-ion\r\n\r\nHệ điều hành	\r\nWindows 11 Home + Microsoft Office Home 2024 + Microsoft 365 Basic\r\n\r\nĐộ phân giải màn hình	\r\n2880 x 1800 pixels\r\n\r\nLoại CPU	\r\nAMD Ryzen AI 7 350 2.0GHz (24MB Cache, up to 5.0GHz, 8 lõi, 16 luồng)\r\n\r\nCổng giao tiếp	\r\n1x USB 3.2 Gen 2 Type-A (10Gbps)\r\n2x USB 4.0 Gen 3 Type-C hỗ trợ hiển thị/cung cấp điện (40Gbps)\r\n1x HDMI 2.1 TMDS\r\n1x Giắc cắm âm thanh kết hợp 3.5mm\r\nĐầu đọc thẻ SD 4.0', '2025-11-28'),
('MACBOOK', '006', 'Apple MacBook Air M2 2024 8CPU 8GPU 16GB 256GB I Chính hãng Apple Việt Nam', '6.webp', 199, 16090000, 19690000, 'Loại card đồ họa	\r\n8 nhân GPU, 16 nhân Neural Engine\r\n\r\nDung lượng RAM	\r\n16GB\r\n\r\nỔ cứng	\r\n256GB\r\n\r\nKích thước màn hình	\r\n13.6 inches\r\n\r\nCông nghệ màn hình	\r\nLiquid Retina Display\r\n\r\nPin	\r\n52,6 Wh\r\n\r\nHệ điều hành	\r\nMacOS\r\n\r\nĐộ phân giải màn hình	\r\n2560 x 1664 pixels\r\n\r\nLoại CPU	\r\nApple M2 8 nhân\r\n\r\nCổng giao tiếp	\r\n2 x Thunderbolt 3\r\nJack tai nghe 3.5 mm\r\nMagSafe 3', '2025-11-28'),
('MACBOOK', '007', 'MacBook Air M4 13 inch 2025 10CPU 8GPU 16GB 256GB | Chính hãng Apple Việt Nam', '7.webp', 99, 19900000, 24990000, 'Loại card đồ họa	\r\nGPU 8 lõi\r\nNeural Engine 16 lõi\r\nCông nghệ dò tia tốc độ cao bằng phần cứng\r\nBăng thông bộ nhớ 120GB/s\r\n\r\nDung lượng RAM	\r\n16GB\r\n\r\nỔ cứng	\r\n256GB\r\n\r\nKích thước màn hình	\r\n13.6 inches\r\n\r\nCông nghệ màn hình	\r\nMàn hình Liquid Retina\r\nCó đèn nền LED\r\nMật độ 224 pixel mỗi inch\r\nĐộ sáng 500 nit\r\nHỗ trợ một tỷ màu\r\nDải màu rộng (P3)\r\nCông nghệ True Tone\r\n\r\nPin	\r\nThời gian xem video trực tuyến lên đến 18 giờ\r\nThời gian duyệt web trên mạng không dây lên đến 15 giờ\r\nPin Li-Po 53.8 watt‑giờ tích hợp\r\n\r\nHệ điều hành	\r\nmacOS\r\n\r\nĐộ phân giải màn hình	\r\n2560 x 1664 pixels\r\n\r\nLoại CPU	\r\nCPU 10 lõi với 4 lõi hiệu năng và 6 lõi tiết kiệm điện\r\n\r\nCổng giao tiếp	\r\nCổng sạc MagSafe 3\r\nJack cắm tai nghe 3.5 mm\r\nHai cổng Thunderbolt 4 (USB-C) hỗ trợ: Sạc / DisplayPort / Thunderbolt 4 (lên đến 40Gb/s) / USB 4 (lên đến 40Gb/s)', '2025-11-28'),
('MACBOOK', '008', 'Mac mini M4 2024 10CPU 10GPU 16GB 256GB | Chính hãng Apple Việt Nam', '8.webp', 47, 9900000, 13990000, 'Loại card đồ họa	\r\nGPU 10 lõi\r\n\r\nDung lượng RAM	\r\n16GB\r\n\r\nỔ cứng	\r\n256GB\r\n\r\nCông nghệ màn hình	\r\nHỗ trợ đồng thời đến ba màn hình\r\nĐầu ra video kỹ thuật số Thunderbolt 4\r\n\r\nHệ điều hành	\r\nmacOS\r\n\r\nLoại CPU	\r\nApple M4 10 lõi với 4 lõi hiệu năng và 6 lõi tiết kiệm điện\r\nNeural Engine 16 lõi\r\n\r\nCổng giao tiếp	\r\nMặt trước:\r\nHai cổng USB‑C hỗ trợ cho USB 3 (lên đến 10Gb/s)\r\nJack cắm tai nghe 3,5 mm\r\nMặt sau (M4):\r\nCổng Gigabit Ethernet (có thể lựa chọn cấu hình Ethernet 10Gb)\r\nCổng HDMI\r\nThunderbolt 4 (lên đến 40Gb/s)\r\nUSB 4 (lên đến 40Gb/s)', '2025-11-28'),
('MACBOOK', '009', 'MacBook Pro 14 M4 Pro 12CPU 16GPU 24GB 512GB | Chính hãng Apple Việt Nam', '9.webp', 60, 43900000, 49900000, 'Loại card đồ họa	\r\n16 lõi\r\nNeural Engine 16 lõi\r\n\r\nDung lượng RAM	\r\n24GB\r\n\r\nỔ cứng	\r\n512GB\r\n\r\nKích thước màn hình	\r\n14.2 inches\r\n\r\nCông nghệ màn hình	\r\nMàn hình Liquid Retina XDR\r\nXDR (Extreme Dynamic Range)\r\nĐộ sáng XDR: 1.000 nit ở chế độ toàn màn hình, độ sáng đỉnh 1.600 nit (chỉ nội dung HDR)\r\n1 tỷ màu\r\nDải màu rộng (P3)\r\nCông nghệ True Tone\r\nHỗ trợ tối đa hai màn hình ngoài\r\n\r\nPin	\r\nPin Li-Po 72.4 watt-giờ\r\nThời gian xem video trực tuyến lên đến 22 giờ\r\nThời gian duyệt web trên mạng không dây lên đến 14 giờ\r\n\r\nHệ điều hành	\r\nmacOS\r\n\r\nĐộ phân giải màn hình	\r\n3024 x 1964 pixels\r\n\r\nLoại CPU	\r\nApple M4 Pro 12 lõi với 8 lõi hiệu năng và 4 lõi tiết kiệm điện\r\n\r\nCổng giao tiếp	\r\nKhe thẻ nhớ SDXC\r\nCổng HDMI\r\nJack cắm tai nghe 3.5 mm\r\nCổng MagSafe 3\r\nBa cổng Thunderbolt 5 (USB‑C) hỗ trợ: Sạc, DisplayPort, Thunderbolt 5 (lên đến 120Gb/s), Thunderbolt 4 (lên đến 40Gb/s), USB 4 (lên đến 40Gb/s)', '2025-11-28'),
('MACBOOK', '010', 'MacBook Air M4 15 inch 2025 10CPU 10GPU 16GB 512GB | Chính hãng Apple Việt Nam', '10.webp', 65, 28490000, 33990000, 'Loại card đồ họa	\r\nGPU 10 lõi\r\nNeural Engine 16 lõi\r\nCông nghệ dò tia tốc độ cao bằng phần cứng\r\nBăng thông bộ nhớ 120GB/s\r\n\r\nDung lượng RAM	\r\n16GB\r\n\r\nỔ cứng	\r\n512GB\r\n\r\nKích thước màn hình	\r\n15.3 inches\r\n\r\nCông nghệ màn hình	\r\nMàn hình Liquid Retina\r\nCó đèn nền LED\r\nMật độ 224 pixel mỗi inch\r\nĐộ sáng 500 nit\r\nHỗ trợ một tỷ màu\r\nDải màu rộng (P3)\r\nCông nghệ True Tone\r\n\r\nPin	\r\nThời gian xem video trực tuyến lên đến 18 giờ\r\nThời gian duyệt web trên mạng không dây lên đến 15 giờ\r\nPin Li-Po 66.5 watt‑giờ tích hợp\r\n\r\nHệ điều hành	\r\nmacOS\r\n\r\nĐộ phân giải màn hình	\r\n2880 x 1864 pixels\r\n\r\nLoại CPU	\r\nCPU 10 lõi với 4 lõi hiệu năng và 6 lõi tiết kiệm điện\r\n\r\nCổng giao tiếp	\r\nCổng sạc MagSafe 3\r\nJack cắm tai nghe 3.5 mm\r\nHai cổng Thunderbolt 4 (USB-C) hỗ trợ: Sạc / DisplayPort / Thunderbolt 4 (lên đến 40Gb/s) / USB 4 (lên đến 40Gb/s)', '2025-11-28'),
('Laptop MSI', '011', 'Laptop MSI Gaming Katana A15 AI B8VE-402VN', '11.webp', 70, 19900000, 24990000, 'Loại card đồ họa	\r\nNVIDIA GeForce RTX 4050 Laptop GPU 6GB GDDR6\r\n\r\nDung lượng RAM	\r\n16GB\r\n\r\nLoại RAM	\r\nDDR5-5600\r\n\r\nSố khe ram	\r\n2 khe (Nâng cấp tối đa 64GB)\r\n\r\nỔ cứng	\r\n512GB SSD 2x M.2 (NVMe PCIe Gen4)\r\n\r\nKích thước màn hình	\r\n15.6 inches\r\n\r\nPin	\r\n3-Cell 53.5 Battery (Whr)\r\n\r\nHệ điều hành	\r\nWindows 11 Home\r\n\r\nĐộ phân giải màn hình	\r\n1920 x 1080 pixels (FullHD)\r\n\r\nLoại CPU	\r\nAMD Ryzen R7-8845HS có kiến ​​trúc AMD XDNA\r\n\r\nCổng giao tiếp	\r\n1x Mic-in/Headphone-out Combo Jack\r\n1x Type-C (USB3.2 Gen1 / DP)\r\n2x Type-A USB3.2 Gen1\r\n1x Type-A USB2.0\r\n1x HDMI 2.1 (8K @ 60Hz / 4K @ 120Hz)\r\n1x RJ45', '2025-11-27'),
('Laptop MSI', '012', 'Laptop MSI Stealth A16 AI+ A3XWFG-018VN', '12.webp', 50, 48900000, 53990000, 'Chip AI	\r\nAMD Ryzen AI - 50 AI TOPS\r\n\r\nLoại card đồ họa	\r\nNVIDIA GeForce RTX 5060, 8GB GDDR7\r\nAMD Graphics\r\n\r\nDung lượng RAM	\r\n32GB\r\n\r\nLoại RAM	\r\nDDR5 - 5600Mhz\r\n\r\nSố khe ram	\r\n2 khe (2 x 16GB)\r\n\r\nỔ cứng	\r\n1TB NVMe PCIe SSD Gen4x4 w/o DRAM, 2 khe đã sử dụng 1 khe\r\n\r\nKích thước màn hình	\r\n16 inches\r\n\r\nCông nghệ màn hình	\r\nĐộ phủ màu 100% DCI-P3 (Typ.)\r\n\r\nPin	\r\n4 cell / 99.9 Whr\r\n\r\nHệ điều hành	\r\nWindows 11 Home SEA\r\n\r\nĐộ phân giải màn hình	\r\n2560 x 1600 pixels (2.5K)\r\n\r\nLoại CPU	\r\nAMD Ryzen AI 9 HX 370 (Tốc độ CPU (Base): 2Ghz / Tốc độ CPU Tối đa (Turbo Boost): 5.1GHz / Bộ nhớ đệm: 24MB)\r\n\r\nCổng giao tiếp	\r\n1x Type-C (USB4 / DisplayPort/ Power Delivery 3.0/ Thunderbolt 4)\r\n2x Type-A USB3.2 Gen2\r\n1x HDMI 2.1 (8K @ 60Hz / 4K @ 120Hz)\r\n1x RJ45', '2025-11-26'),
('Laptop MSI', '013', 'Laptop MSI Vector 16 HX AI A2XWHG-010VN', '13.webp', 40, 38990000, 47990000, 'Loại card đồ họa	\r\nNVIDIA GeForce RTX 5070 Ti Laptop GPU, GDDR7 12GB\r\nIntel Graphics\r\n\r\nDung lượng RAM	\r\n16GB\r\n\r\nLoại RAM	\r\nDDR5-5600\r\n\r\nSố khe ram	\r\n2 khe (2x 8GB, nâng cấp tối đa 96GB)\r\n\r\nỔ cứng	\r\n512GB*1 (1x M.2 SSD slot (NVMe PCIe Gen4), 1x M.2 SSD slot (NVMe PCIe Gen5) Compatible)\r\n\r\nKích thước màn hình	\r\n16 inches\r\n\r\nCông nghệ màn hình	\r\nMàn hình chống chói\r\nG to G 3ms\r\nĐộ phủ màu 100% DCI-P3\r\nĐộ sáng 500 nits\r\n\r\nPin	\r\n4-Cell 90 Whrs (Whr)\r\n\r\nHệ điều hành	\r\nWindows 11 Home\r\n\r\nĐộ phân giải màn hình	\r\n2560 x 1600 pixels (2.5K)\r\n\r\nLoại CPU	\r\nIntel Core Ultra 7 255HX với Intel AI Boost (NPU), 20 lõi (8 P-cores + 12 E-cores), 20 luồng, 36MB cache, Max Turbo Frequency 5.2 GHz\r\n\r\nCổng giao tiếp	\r\n2x Thunderbolt 5 (DisplayPort/ Power Delivery 3.1)\r\n2x Type-A USB3.2 Gen2\r\n1x Đầu đọc thẻ SD Express\r\n1x HDMI 2.1 (8K @ 60Hz / 4K @ 120Hz)\r\n1x RJ45\r\n1x SD Express', '2025-11-25'),
('Laptop MSI', '014', 'Laptop MSI Gaming Thin 15 B13UC-2081VN', '14.webp', 70, 14990000, 18290000, 'Loại card đồ họa	\r\nNVIDIA GeForce RTX 3050 4GB GDDR6 128-bit\r\n\r\nDung lượng RAM	\r\n16GB\r\n\r\nLoại RAM	\r\nDDR4-3200 MHz\r\n\r\nSố khe ram	\r\n2 khe (2x 8GB, nâng cấp tối đa 64GB)\r\n\r\nỔ cứng	\r\n512GB PCIE (1x M.2 SSD slot (NVMe PCIe Gen4), 1x 2.5\" SATA HDD)\r\n\r\nKích thước màn hình	\r\n15.6 inches\r\n\r\nPin	\r\n3-Cell 52.4 Battery (Whr)\r\n\r\nHệ điều hành	\r\nWindows 11 Home\r\n\r\nĐộ phân giải màn hình	\r\n1920 x 1080 pixels (FullHD)\r\n\r\nLoại CPU	\r\nIntel Core i5-13420H\r\n\r\nCổng giao tiếp	\r\n1x Mic-in, 1x Headphone-out\r\n1x Type-C (USB3.2 Gen1 / DP)\r\n3x Type-A USB3.2 Gen1\r\n1x HDMI (4K @ 30Hz)\r\n1x RJ45', '2025-11-23'),
('Laptop MSI', '015', 'Laptop MSI Katana 15 B13VEK-2256VN', '15.webp', 50, 19990000, 24490000, 'Loại card đồ họa	\r\nIntel UHD Graphics + NVIDIA GeForce RTX 4050 6GB GDDR6\r\n\r\nDung lượng RAM	\r\n16GB\r\n\r\nLoại RAM	\r\nDDR5 5200MHz\r\n\r\nSố khe ram	\r\n2 khe (1x 16GB, Hỗ trợ nâng cấp tối đa 64GB)\r\n\r\nỔ cứng	\r\n1x 512GB M.2-2280 SSD slot (NVMe PCIe Gen4x4) (Hỗ trợ nâng cấp: 1x M.2 SSD slot (NVMe PCIe Gen4))\r\n\r\nKích thước màn hình	\r\n15.6 inches\r\n\r\nCông nghệ màn hình	\r\nĐộ phủ màu 45% NTSC\r\nMàn hình chống chói\r\n\r\nPin	\r\n3-cell, 53.5WHr\r\n\r\nHệ điều hành	\r\nWindows 11 Home\r\n\r\nĐộ phân giải màn hình	\r\n1920 x 1080 pixels (FullHD)\r\n\r\nLoại CPU	\r\nIntel Core i7-13620H\r\n\r\nCổng giao tiếp	\r\n1x Type-C (USB3.2 Gen1 / DisplayPort)\r\n2x Type-A USB3.2 Gen1\r\n1x Type-A USB2.0\r\n1x HDMI (4K @ 30Hz)\r\n1x RJ45\r\n1x Mic-in/Headphone-out Combo Jack', '2025-11-22'),
('Phụ kiện', '016', 'Tai nghe Bluetooth Apple AirPods 4 | Chính hãng Apple Việt Nam', '16.webp', 199, 2500000, 3090000, 'Kích thước	\r\nTai nghe: 30.2 x 18.3 x 18.1 mm\r\nHộp sạc: 46.2 x 50.1 x 21.2 mm\r\n\r\nTrọng lượng	\r\nTai nghe: 4.3g\r\nHộp sạc: 32.3g\r\n\r\nCông nghệ âm thanh	\r\nTrình điều khiển Apple với độ lệch tương phản cao có thể tùy chỉnh\r\nBộ khuếch đại với độ lệch tương phản cao có thể tùy chỉnh\r\nTách Lời Nói\r\nChế độ Âm Thanh Không Gian Cá Nhân Hóa với tính năng theo dõi chuyển động đầu chủ động\r\nEQ Thích Ứng H\r\n\r\nMicro	\r\nCó\r\n\r\nThời lượng sử dụng Pin	\r\nThời gian nghe lên đến 5 giờ với một lần sạc\r\nThời gian đàm thoại lên đến 4,5 giờ với một lần sạc\r\n\r\nPhương thức điều khiển	\r\nCảm biến lực\r\n\r\nChipset	\r\nChip tai nghe H2\r\n\r\nHãng sản xuất	\r\nApple Chính hãng', '2025-11-28'),
('Phụ kiện', '017', 'Tai nghe Bluetooth Apple AirPods Pro 3 2025 Type-C | Chính hãng (MFHP4ZP/A)', '17.webp', 150, 5490000, 6790000, 'Kích thước	\r\nTai Nghe: 30,9 x 19,2 x 27,0 mm\r\nHộp Sạc: 47,2 mm x 62,2 x 21,8 mm\r\n\r\nTrọng lượng	\r\nTai Nghe: 5,55 gram\r\nHộp Sạc + Tai Nghe: 43,99 gram\r\n\r\nCông nghệ âm thanh	\r\nTrình điều khiển Apple với độ lệch tương phản cao có thể tùy chỉnh\r\nBộ khuếch đại với độ lệch tương phản cao có thể tùy chỉnh\r\nChủ Động Khử Tiếng Ồn tai bạn chưa từng nghe\r\nÂm Thanh Thích Ứng\r\nChế độ Xuyên Âm\r\nNhận Biết Cuộc Hội Thoại\r\n\r\nMicro	\r\nCó\r\n\r\nCổng kết nối	\r\nType C\r\n\r\nThời lượng sử dụng Pin	\r\nTai nghe: lên đến 8 giờ\r\nTai nghe + hộp sạc: lên đến 24 giờ\r\n\r\nPhương thức điều khiển	\r\nCảm ứng chạm\r\n\r\nTính năng khác	\r\nKiểm tra thính giác\r\nThiết bị trợ thính\r\nGiảm âm thanh lớn\r\nCảm biến theo dõi nhịp tim khi tập luyện\r\nChống bụi, chống mồ hôi và chống nước (IP57)\r\n\r\nChipset	\r\nChip Apple H2\r\n\r\nHãng sản xuất	\r\nApple Chính hãng', '2025-11-28'),
('Phụ kiện', '018', 'Chuột không dây Logitech MX Master 2S', '18.webp', 140, 849000, 1390000, 'Pin	\r\nThời gian sử dụng đến 70 ngày\r\n\r\nĐộ phân giải	\r\n4000 DPI\r\n\r\nCách kết nối	\r\nBluetooth\r\n\r\nĐộ dài dây / Khoảng cách kết nối	\r\n10 m\r\n\r\nHãng sản xuất	\r\nLogitech\r\n\r\nLõi pin	\r\nLi-Po\r\n\r\nDung lượng Pin	\r\n500 mAh\r\n\r\nTính năng khác	\r\n- Công nghệ quang Darkfield Laser: cho phép điều khiển chuột ở bất cứ bề mặt nào (thủy tinh hay các bề mặt có độ gồ ghề, lồi lõm cao)\r\n- Chức năng FLOW cho phép copy file, hình ảnh,thư mục từ máy này sang máy khác mà không cần USB', '2025-11-26'),
('Phụ kiện', '019', 'Máy cầm tay chơi game ROG Xbox Ally', '19.webp', 30, 10990000, 13490000, 'Kích thước màn hình	\r\n7.0 inches\r\n\r\nCông nghệ màn hình	\r\nIPS\r\n\r\nChipset	\r\nAMD Ryzen™ Z2 A Processor\r\n\r\nDung lượng RAM	\r\n16 GB\r\n\r\nBộ nhớ trong	\r\n512 GB\r\n\r\nPin	\r\n60Wh\r\n\r\nHệ điều hành	\r\nWindows 11 Home\r\n\r\nĐộ phân giải màn hình	\r\n1920 x 1080 pixels (FullHD)\r\n\r\nTính năng màn hình	\r\nMàn hình cảm ứng\r\nTần số quét 120Hz\r\nThời gian phản hồi 7ms\r\nTấm nền IPS\r\nĐộ sáng tối đa 500 nits\r\nĐạt chuẩn 100% sRGB\r\nCông nghệ FreeSync Premium\r\n\r\nLoại CPU	\r\n4 cores, 8 threads, 2.8GHz (up to 3.8GHz)\r\n\r\nTương thích	\r\nWindows 11 + Xbox Game Pass / Armoury Crate', '2025-11-28'),
('Phụ kiện', '020', 'Giá đỡ MacBook/Laptop WIWU ZM-902', '20.webp', 300, 135000, 200000, 'Hãng sản xuất	\r\nWiwu\r\n\r\nChất liệu	\r\nKim loại + Silicon chống trượt', '2025-11-28'),
('Phụ kiện', '021', 'Sạc Anker Zolo 3C1A 140W kèm cáp USB-C B2697', '21.webp', 150, 835000, 1030000, 'Công suất sạc	\r\n140W\r\n\r\nSử dụng tối đa	\r\n4 thiết bị\r\n\r\nĐầu ra	\r\n3 x USB-C, 1 x USB-A\r\n\r\nTiện ích	\r\nSạc nhanh\r\nMàn hình thông minh kép hiển thị nhiệt độ\r\nKhi nhiệt độ cao hơn ngưỡng, hệ thống điều chỉnh để giảm nhiệt\r\n\r\nCông nghệ/Đạt chứng nhận	\r\nCông nghệ Power IQ 3.0\r\n\r\nChiều dài dây	\r\n1.5m\r\n\r\nHãng sản xuất	\r\nAnker', '2025-11-28'),
('Phụ kiện', '022', 'Củ sạc Apple Type-C 70W MXN53ZA/A', '22.webp', 120, 1135000, 1475000, 'Công suất sạc	\r\n70W\r\n\r\nSử dụng tối đa	\r\n1 thiết bị\r\n\r\nĐầu ra	\r\nUSB-C\r\n\r\nTiện ích	\r\nSạc nhanh\r\nBảo vệ quá dòng, chống quá nhiệt và chống quá áp\r\n\r\nHãng sản xuất	\r\nApple Chính hãng', '2025-11-28'),
('Phụ kiện', '023', 'Router Wifi TP-Link Archer C54 băng tần kép AC1200', '23.webp', 500, 230000, 329000, 'Chuẩn Wi-Fi	\r\nWi-Fi 5 (802.11ac)\r\n\r\nBăng tần sóng	\r\n2.4GHz & 5GHz\r\n\r\nCổng kết nối	\r\n1 cổng WAN\r\n4 cổng LAN\r\n\r\nKết nối và điều khiển	\r\nỨng dụng Tether\r\n\r\nĐộ mạnh của sóng (các thiết bị mạng)	\r\n300Mbps (2.4GHz)\r\n867Mbps (5GHz)\r\n\r\nĐộ phủ sóng tối đa	\r\n25 m\r\n\r\nSố lượng user tối đa	\r\n25 - 30 user\r\n\r\nSố Ăng ten	\r\n4 x Ăng ten cố định\r\n\r\nHãng sản xuất	\r\nTP-Link', '2025-11-28'),
('Phụ kiện', '024', 'Bàn phím không dây Logitech MX Keys mini', '24.webp', 60, 1990000, 2490000, 'Loại bàn phím	\r\nMini-size\r\n\r\nSố phím	\r\n79 phím\r\n\r\nTương thích	\r\nWindows, macOS, iOS, iPadOS, Linux, ChromeOS, Android\r\n\r\nKết nối	\r\nBluetooth Low Energy\r\nTương thích USB Receiver Logi Bolt (Không đi kèm)\r\n\r\nKhoảng cách kết nối (Độ dài dây)	\r\n10 mét\r\n\r\nĐèn LED	\r\nLED trắng\r\n\r\nThời gian dùng	\r\nSử dụng 10 ngày với 1 lần sạc đầy\r\nSử dụng đến 5 tháng khi tắt đèn nền\r\n\r\nHãng sản xuất	\r\nLogitech', '2025-11-28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbluser`
--

CREATE TABLE `tbluser` (
  `user_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_token` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `account_status` varchar(20) NOT NULL DEFAULT 'active',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `is_locked` tinyint(1) DEFAULT 0 COMMENT 'Trạng thái khóa tài khoản: 0=hoạt động, 1=bị khóa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tbluser`
--

INSERT INTO `tbluser` (`user_id`, `fullname`, `email`, `password`, `is_verified`, `verification_token`, `created_at`, `role`, `account_status`, `reset_token`, `reset_expires`, `is_locked`) VALUES
(16, 'Nguyễn Thị Tường Vy', 'nttv9604@gmail.com', '$2y$10$Pqx9.Q8mWveBOBCvzL5eWOWEfYRiFOZKcoq.rKep.Blhw/qX1aAs.', 0, 35, '2025-11-05 19:31:06', 'admin', 'active', NULL, NULL, 0),
(18, 'Dương Quốc Việt', 'quocviet161104@gmail.com', '$2y$10$tM51n4fKeMGCa0SJWagYAupliyc2BYlNmxWSwcJ8XIL0CGHKe9Mty', 0, 0, '2025-11-14 10:02:07', 'staff', 'active', NULL, NULL, 0),
(20, 'Dương Quốc Việt 123', 'quocviet16114@gmail.com', '$2y$10$a19EFJ9Pnxnlg7mjnVmlJufe36r0jgnIYlCaBrDBgZ5fTe2f3pW/G', 0, 0, '2025-11-14 10:02:07', 'user', 'active', NULL, NULL, 1),
(24, 'Bn', 'tuongvy9062004@gmail.com', '$2y$10$WN01UVfiQ4cKkfhlt0WcsOk9sBGpV2ft8aYpVebVkVrhSHJOnsjvO', 0, 0, '2025-11-28 15:43:25', 'user', 'active', NULL, NULL, 0),
(27, 'ZitPe', 'anhvietkl2004@gmail.com', '$2y$10$KL.IcWxDUQdVMoE0xcmfKeIPjeh9gnbip9W6Cs6A6AIsDKP3afcJi', 0, 0, '2025-11-28 23:48:40', 'user', 'active', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Danh s??ch s???n ph???m y??u th??ch';

--
-- Đang đổ dữ liệu cho bảng `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_email`, `product_id`, `created_at`) VALUES
(11, 'nttv9604@gmail.com', '001', '2025-12-11 17:21:20');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Chỉ mục cho bảng `chat_conversations`
--
ALTER TABLE `chat_conversations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_id` (`session_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_user_email` (`user_email`),
  ADD KEY `idx_admin_email` (`admin_email`);

--
-- Chỉ mục cho bảng `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conversation` (`conversation_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Chỉ mục cho bảng `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `khuyenmai`
--
ALTER TABLE `khuyenmai`
  ADD PRIMARY KEY (`km_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `order_thresholds`
--
ALTER TABLE `order_thresholds`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `masp` (`masp`);

--
-- Chỉ mục cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`email`);

--
-- Chỉ mục cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `masp` (`masp`);

--
-- Chỉ mục cho bảng `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `supplier_contracts`
--
ALTER TABLE `supplier_contracts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contract_number` (`contract_number`),
  ADD KEY `idx_supplier_id` (`supplier_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_contract_number` (`contract_number`);

--
-- Chỉ mục cho bảng `supplier_products`
--
ALTER TABLE `supplier_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_supplier_id` (`supplier_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_category` (`category`);

--
-- Chỉ mục cho bảng `tblfeedback`
--
ALTER TABLE `tblfeedback`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tblloaisp`
--
ALTER TABLE `tblloaisp`
  ADD PRIMARY KEY (`maLoaiSP`);

--
-- Chỉ mục cho bảng `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_account_status` (`account_status`),
  ADD KEY `idx_is_locked` (`is_locked`);

--
-- Chỉ mục cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_email`,`product_id`),
  ADD KEY `idx_user_email` (`user_email`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT cho bảng `chat_conversations`
--
ALTER TABLE `chat_conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT cho bảng `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `khuyenmai`
--
ALTER TABLE `khuyenmai`
  MODIFY `km_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT cho bảng `order_thresholds`
--
ALTER TABLE `order_thresholds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=198;

--
-- AUTO_INCREMENT cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT cho bảng `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `supplier_contracts`
--
ALTER TABLE `supplier_contracts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `supplier_products`
--
ALTER TABLE `supplier_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `tblfeedback`
--
ALTER TABLE `tblfeedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `chat_conversations` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `supplier_contracts`
--
ALTER TABLE `supplier_contracts`
  ADD CONSTRAINT `supplier_contracts_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `supplier_products`
--
ALTER TABLE `supplier_products`
  ADD CONSTRAINT `supplier_products_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;
COMMIT;

-- Restore charset/collation safely
SET CHARACTER_SET_CLIENT = IFNULL(@OLD_CHARACTER_SET_CLIENT, 'utf8mb4');
SET CHARACTER_SET_RESULTS = IFNULL(@OLD_CHARACTER_SET_RESULTS, 'utf8mb4');
SET COLLATION_CONNECTION = IFNULL(@OLD_COLLATION_CONNECTION, 'utf8mb4_unicode_ci');
