<?php
require_once 'BaseModel.php';
/**
 * Model báo cáo doanh thu: tổng hợp theo ngày/tháng/năm và theo khoảng
 */
class ReportModel extends BaseModel {
    protected $table = 'orders';

    /**
     * Doanh thu trong một ngày cụ thể (YYYY-MM-DD)
     */
    public function getRevenueByDay($date) {
        // Use separate aggregates to avoid double-counting order totals when joining order_details.
        $paidCond = "(LOWER(o.transaction_info) REGEXP 'da.*than.*toan' OR LOWER(o.transaction_info) LIKE '%completed%' OR LOWER(o.transaction_info) LIKE '%paid%')";

        // Revenue comes directly from orders
        $revSql = "SELECT DATE(o.created_at) as period, COALESCE(SUM(o.total_amount),0) as revenue
                   FROM {$this->table} o
                   WHERE DATE(o.created_at) = :d AND $paidCond
                   GROUP BY DATE(o.created_at)";
        $revStmt = $this->db->prepare($revSql);
        $revStmt->execute([':d' => $date]);
        $revRow = $revStmt->fetch(PDO::FETCH_ASSOC);

        // Profit computed from order_details and product cost
        $profitSql = "SELECT DATE(o.created_at) as period, COALESCE(SUM(d.total) - SUM(COALESCE(p.giaNhap,0) * d.quantity),0) as profit
                      FROM {$this->table} o
                      JOIN order_details d ON d.order_id = o.id
                      LEFT JOIN tblsanpham p ON p.masp = d.product_id
                      WHERE DATE(o.created_at) = :d AND $paidCond
                      GROUP BY DATE(o.created_at)";
        $profitStmt = $this->db->prepare($profitSql);
        $profitStmt->execute([':d' => $date]);
        $profitRow = $profitStmt->fetch(PDO::FETCH_ASSOC);

        $period = $date;
        $revenue = $revRow['revenue'] ?? 0;
        $profit = $profitRow['profit'] ?? 0;
        return ['period' => $period, 'revenue' => (float)$revenue, 'profit' => (float)$profit];
    }

    /**
     * Doanh thu theo tháng (tham số year, month)
     */
    public function getRevenueByMonth($year, $month) {
        $paidCond = "(LOWER(o.transaction_info) REGEXP 'da.*than.*toan' OR LOWER(o.transaction_info) LIKE '%completed%' OR LOWER(o.transaction_info) LIKE '%paid%')";

        $periodLabel = sprintf('%04d-%02d',$year,$month);

        $revSql = "SELECT DATE_FORMAT(o.created_at, '%Y-%m') as period, COALESCE(SUM(o.total_amount),0) as revenue
                   FROM {$this->table} o
                   WHERE YEAR(o.created_at) = :y AND MONTH(o.created_at) = :m AND $paidCond
                   GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')";
        $revStmt = $this->db->prepare($revSql);
        $revStmt->execute([':y' => $year, ':m' => $month]);
        $revRow = $revStmt->fetch(PDO::FETCH_ASSOC);

        $profitSql = "SELECT DATE_FORMAT(o.created_at, '%Y-%m') as period, COALESCE(SUM(d.total) - SUM(COALESCE(p.giaNhap,0) * d.quantity),0) as profit
                      FROM {$this->table} o
                      JOIN order_details d ON d.order_id = o.id
                      LEFT JOIN tblsanpham p ON p.masp = d.product_id
                      WHERE YEAR(o.created_at) = :y AND MONTH(o.created_at) = :m AND $paidCond
                      GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')";
        $profitStmt = $this->db->prepare($profitSql);
        $profitStmt->execute([':y' => $year, ':m' => $month]);
        $profitRow = $profitStmt->fetch(PDO::FETCH_ASSOC);

        return ['period' => $periodLabel, 'revenue' => (float)($revRow['revenue'] ?? 0), 'profit' => (float)($profitRow['profit'] ?? 0)];
    }

    /**
     * Doanh thu cả năm (trả về từng tháng trong năm)
     */
    public function getRevenueByYear($year) {
        $paidCond = "(LOWER(o.transaction_info) REGEXP 'da.*than.*toan' OR LOWER(o.transaction_info) LIKE '%completed%' OR LOWER(o.transaction_info) LIKE '%paid%')";

        $revSql = "SELECT DATE_FORMAT(o.created_at, '%Y-%m') as period, COALESCE(SUM(o.total_amount),0) as revenue
                   FROM {$this->table} o
                   WHERE YEAR(o.created_at) = :y AND $paidCond
                   GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
                   ORDER BY period";
        $revStmt = $this->db->prepare($revSql);
        $revStmt->execute([':y' => $year]);
        $revRows = $revStmt->fetchAll(PDO::FETCH_ASSOC);

        $profitSql = "SELECT DATE_FORMAT(o.created_at, '%Y-%m') as period, COALESCE(SUM(d.total) - SUM(COALESCE(p.giaNhap,0) * d.quantity),0) as profit
                      FROM {$this->table} o
                      JOIN order_details d ON d.order_id = o.id
                      LEFT JOIN tblsanpham p ON p.masp = d.product_id
                      WHERE YEAR(o.created_at) = :y AND $paidCond
                      GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
                      ORDER BY period";
        $profitStmt = $this->db->prepare($profitSql);
        $profitStmt->execute([':y' => $year]);
        $profitRows = $profitStmt->fetchAll(PDO::FETCH_ASSOC);

        // Merge profit into revenue rows by period
        $profitMap = [];
        foreach ($profitRows as $pr) {
            $profitMap[$pr['period']] = (float)$pr['profit'];
        }
        $out = [];
        foreach ($revRows as $r) {
            $p = $r['period'];
            $out[] = ['period' => $p, 'revenue' => (float)$r['revenue'], 'profit' => $profitMap[$p] ?? 0.0];
        }
        return $out;
    }

    /**
     * Tổng hợp doanh thu trong khoảng ngày theo `granularity`: day|month|year
     */
    public function getRevenueRangeGrouped($granularity, $startDate, $endDate) {
        $allowed = ['day','month','year'];
        if (!in_array($granularity, $allowed)) {
            throw new InvalidArgumentException('Granularity không hợp lệ');
        }
        switch ($granularity) {
            case 'day':
                $groupExpr = 'DATE(created_at)';
                $selectExpr = 'DATE(created_at) as period';
                break;
            case 'month':
                $groupExpr = "DATE_FORMAT(created_at,'%Y-%m')";
                $selectExpr = "DATE_FORMAT(created_at,'%Y-%m') as period";
                break;
            case 'year':
                $groupExpr = 'YEAR(created_at)';
                $selectExpr = 'YEAR(created_at) as period';
                break;
        }
        $paidCond = "(LOWER(o.transaction_info) REGEXP 'da.*than.*toan' OR LOWER(o.transaction_info) LIKE '%completed%' OR LOWER(o.transaction_info) LIKE '%paid%')";

        // Revenue grouped
        $revSql = "SELECT $selectExpr, COALESCE(SUM(o.total_amount),0) as revenue
                   FROM {$this->table} o
                   WHERE DATE(o.created_at) BETWEEN :start AND :end AND $paidCond
                   GROUP BY $groupExpr
                   ORDER BY $groupExpr";
        $revStmt = $this->db->prepare($revSql);
        $revStmt->execute([':start' => $startDate, ':end' => $endDate]);
        $revRows = $revStmt->fetchAll(PDO::FETCH_ASSOC);

        // Profit grouped
        $profitSql = "SELECT $selectExpr, COALESCE(SUM(d.total) - SUM(COALESCE(p.giaNhap,0) * d.quantity),0) as profit
                      FROM {$this->table} o
                      JOIN order_details d ON d.order_id = o.id
                      LEFT JOIN tblsanpham p ON p.masp = d.product_id
                      WHERE DATE(o.created_at) BETWEEN :start AND :end AND $paidCond
                      GROUP BY $groupExpr
                      ORDER BY $groupExpr";
        $profitStmt = $this->db->prepare($profitSql);
        $profitStmt->execute([':start' => $startDate, ':end' => $endDate]);
        $profitRows = $profitStmt->fetchAll(PDO::FETCH_ASSOC);

        $profitMap = [];
        foreach ($profitRows as $pr) {
            $profitMap[$pr['period']] = (float)$pr['profit'];
        }
        $out = [];
        foreach ($revRows as $r) {
            $p = $r['period'];
            $out[] = ['period' => $p, 'revenue' => (float)$r['revenue'], 'profit' => $profitMap[$p] ?? 0.0];
        }
        return $out;
    }

    /**
     * Tổng doanh thu toàn thời gian (SUM total_amount)
     */
    public function getTotalRevenueAllTime() {
        $paidCond = "(LOWER(o.transaction_info) REGEXP 'da.*than.*toan' OR LOWER(o.transaction_info) LIKE '%completed%' OR LOWER(o.transaction_info) LIKE '%paid%')";

        $revSql = "SELECT COALESCE(SUM(o.total_amount),0) as revenue FROM {$this->table} o WHERE $paidCond";
        $revRow = $this->db->query($revSql)->fetch(PDO::FETCH_ASSOC);
        $revenue = (float)($revRow['revenue'] ?? 0);

        $profitSql = "SELECT COALESCE(SUM(d.total) - SUM(COALESCE(p.giaNhap,0) * d.quantity),0) as profit
                      FROM {$this->table} o
                      JOIN order_details d ON d.order_id = o.id
                      LEFT JOIN tblsanpham p ON p.masp = d.product_id
                      WHERE $paidCond";
        $profitRow = $this->db->query($profitSql)->fetch(PDO::FETCH_ASSOC);
        $profit = (float)($profitRow['profit'] ?? 0);

        return ['revenue' => $revenue, 'profit' => $profit];
    }
}
