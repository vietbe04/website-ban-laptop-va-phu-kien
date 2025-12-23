
<?php
session_start();
require_once "app/App.php";
require_once "app/DB.php";
require_once "app/Controller.php";
require_once "app/config.php";
// Nếu người dùng mở site bằng host khác (ví dụ: http://localhost/...),
// chuyển hướng sang APP_URL để dùng domain được cấu hình trong app/config.php
$parsed = parse_url(APP_URL);
$appHost = isset($parsed['host']) ? $parsed['host'] : '';
if (!empty($appHost) && isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== $appHost) {
	// Chuyển đến trang chính của APP_URL
	header('Location: ' . rtrim(APP_URL, '/') . '/Home/index');
	exit();
}

$App= new App;
//var_dump($_GET) ;
?> 