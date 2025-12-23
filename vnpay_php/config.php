<?php
// Ensure app config (APP_URL) is loaded before defining return URL
if (!defined('APP_URL')) {
	$appCfg = __DIR__ . '/../app/config.php';
	if (file_exists($appCfg)) { require_once $appCfg; }
}
if (session_status() === PHP_SESSION_NONE) { session_start(); }
date_default_timezone_set('Asia/Ho_Chi_Minh');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$vnp_TmnCode = "CM01NRXG"; //Mã định danh merchant kết nối (Terminal Id)
$vnp_HashSecret = "1CZQFRQ2K1MKJDKFG4LMUQ1NQU07Z003"; //Secret key
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
// Sử dụng APP_URL nếu đã được định nghĩa để tránh lệch domain (localhost vs ngrok)
if (defined('APP_URL') && APP_URL) {
	$vnp_Returnurl = rtrim(APP_URL, '/').'/vnpay_php/vnpay_return.php';
} else {
	// Attempt to derive from current host + DQV path
	$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
	$basePath = '/DQV';
	$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
	$vnp_Returnurl = $scheme . $host . $basePath . '/vnpay_php/vnpay_return.php';
}
@error_log('[VNPAY_CONFIG] vnp_Returnurl=' . $vnp_Returnurl . ' APP_URL=' . (defined('APP_URL') ? APP_URL : 'NULL'));
$vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
$apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
//Config input format
//Expire
$startTime = date("YmdHis");
$expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
