<?php
/**
 * Controller báo cáo doanh thu (dùng layout admin)
 */
class Report extends Controller {
    private $reportModel;
    public function __construct() {
        $this->reportModel = $this->model('ReportModel');
    }

    /**
     * Hiển thị form và kết quả báo cáo theo loại (ngày/tháng/năm)
     */
    public function Show() {
        // Chỉ admin mới được xem báo cáo
        $this->requireRole(['admin'], 'report');
    $type = $_GET['type'] ?? 'day'; // day|month|year
    $date = $_GET['date'] ?? date('Y-m-d');
    $year = $_GET['year'] ?? date('Y');
    $month = $_GET['month'] ?? date('m');

        $result = [];
        $single = null;
        try {
            switch ($type) {
                case 'day':
                    // Doanh thu ngày chọn; danh sách: toàn bộ ngày trong tháng của ngày đó
                    $single = $this->reportModel->getRevenueByDay($date);
                    $monthStart = date('Y-m-01', strtotime($date));
                    $monthEnd   = date('Y-m-t',  strtotime($date));
                    $result = $this->reportModel->getRevenueRangeGrouped('day', $monthStart, $monthEnd);
                    break;
                case 'month':
                    // Doanh thu tháng/năm chọn; danh sách: từng tháng trong cả năm chọn
                    $single = $this->reportModel->getRevenueByMonth($year, $month);
                    $yearStart = sprintf('%04d-01-01', (int)$year);
                    $yearEnd   = sprintf('%04d-12-31', (int)$year);
                    $result = $this->reportModel->getRevenueRangeGrouped('month', $yearStart, $yearEnd);
                    break;
                case 'year':
                    // Doanh thu cả năm; danh sách: từng tháng của năm đó
                    $singleList = $this->reportModel->getRevenueByYear($year);
                    $single = [
                        'period' => $year,
                        'revenue' => array_sum(array_column($singleList,'revenue')),
                        'profit' => array_sum(array_column($singleList,'profit')),
                    ];
                    $result = $singleList;
                    break;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $totalAllTime = $this->reportModel->getTotalRevenueAllTime(); // now returns ['revenue'=>..., 'profit'=>...]
        $data = [
            'type' => $type,
            'date' => $date,
            'year' => $year,
            'month' => $month,
            'single' => $single,
            'list' => $result,
            'totalAllTime' => $totalAllTime,
            'error' => $error ?? null,
        ];
        // Hiển thị bằng layout admin giống các chức năng khác
        $this->view('adminPage', ['page' => 'RevenueReportView'] + $data);
    }
}
