 <?php
/**
 * Model người dùng: CRUD, xác thực, thống kê đơn hàng liên quan
 */
class UserModel extends DB {
    private $table = "tbluser";
    public $email;
    public $password;
    public $fullname;
    public $token;
    // Tránh dynamic property (PHP 8.2+) và cảnh báo khi truy cập
    public $role = 'user';

    /**
     * Tạo user mới với fullname, email, password hash, token và role
     */
    public function create() {
        // Hỗ trợ khóa role (mặc định 'user') để phân quyền
        $role = ($this->role !== null && $this->role !== '') ? $this->role : 'user';
        $query = "INSERT INTO {$this->table} (fullname, email, password, verification_token, role) VALUES (:fullname, :email, :password, :token, :role)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":fullname", $this->fullname);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":token", $this->token);
        $stmt->bindParam(":role", $role);
        return $stmt->execute();
    }

    /**
     * Admin: danh sách người dùng + thống kê đơn hàng, có tìm kiếm và phân trang
     * Chỉ tính đơn hàng đã thanh toán (transaction_info LIKE '%dathanhtoan%')
     */
    public function listWithStats($q = '', $limit = 15, $offset = 0) {
        $like = "%" . $q . "%";
        $sql = "SELECT u.user_id, u.fullname, u.email, u.created_at, u.role, u.is_locked,
                   MAX(o.phone) as phone, MAX(o.address) as address,
                   COUNT(CASE WHEN o.transaction_info LIKE '%dathanhtoan%' THEN 1 END) as orders_count, 
                   COALESCE(SUM(CASE WHEN o.transaction_info LIKE '%dathanhtoan%' THEN o.total_amount ELSE 0 END), 0) as total_spent
            FROM {$this->table} u
            LEFT JOIN orders o ON (o.user_email = u.email OR (o.user_id IS NOT NULL AND o.user_id = u.user_id))
            " . ($q !== '' ? "WHERE u.email LIKE :like_email OR u.fullname LIKE :like_fullname OR o.phone LIKE :like_phone" : "") . "
            GROUP BY u.user_id, u.fullname, u.email, u.created_at, u.role, u.is_locked
            ORDER BY u.created_at DESC
            LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        if ($q !== '') {
            // bind all placeholders separately to avoid driver issues with repeating named params
            $stmt->bindParam(':like_email', $like);
            $stmt->bindParam(':like_fullname', $like);
            $stmt->bindParam(':like_phone', $like);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm tổng số người dùng (có điều kiện tìm kiếm) để phân trang
     */
    public function countWithStats($q = '') {
        $like = "%" . $q . "%";
        $sql = "SELECT COUNT(DISTINCT u.user_id) as total
            FROM {$this->table} u
            LEFT JOIN orders o ON (o.user_email = u.email OR (o.user_id IS NOT NULL AND o.user_id = u.user_id))
            " . ($q !== '' ? "WHERE u.email LIKE :like_email OR u.fullname LIKE :like_fullname OR o.phone LIKE :like_phone" : "");
        $stmt = $this->db->prepare($sql);
        if ($q !== '') {
            $stmt->bindParam(':like_email', $like);
            $stmt->bindParam(':like_fullname', $like);
            $stmt->bindParam(':like_phone', $like);
        }
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Lấy người dùng theo id kèm thống kê đơn hàng
     * Chỉ tính đơn hàng đã thanh toán (transaction_info LIKE '%dathanhtoan%')
     * @param int $id
     */
    public function getByIdWithStats($id) {
        $sql = "SELECT u.user_id, u.fullname, u.email, u.created_at, u.role, u.is_locked,
                       MAX(o.phone) as phone, MAX(o.address) as address,
                       COUNT(CASE WHEN o.transaction_info LIKE '%dathanhtoan%' THEN 1 END) as orders_count, 
                       COALESCE(SUM(CASE WHEN o.transaction_info LIKE '%dathanhtoan%' THEN o.total_amount ELSE 0 END), 0) as total_spent
                FROM {$this->table} u
                LEFT JOIN orders o ON (o.user_email = u.email OR (o.user_id IS NOT NULL AND o.user_id = u.user_id))
                WHERE u.user_id = :id
                GROUP BY u.user_id, u.fullname, u.email, u.created_at, u.role, u.is_locked";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật thông tin người dùng (tùy chọn cập nhật role)
     */
    public function update($id, $fullname, $email, $role = null) {
        if ($role === null) { // giữ nguyên nếu không truyền role
            $sql = "UPDATE {$this->table} SET fullname = :fullname, email = :email WHERE user_id = :id";
        } else {
            $sql = "UPDATE {$this->table} SET fullname = :fullname, email = :email, role = :role WHERE user_id = :id";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($role !== null) { $stmt->bindParam(':role', $role); }
        return $stmt->execute();
    }

    /**
     * Xóa người dùng theo id
     */
    public function deleteById($id) {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Tìm user theo token xác minh chưa kích hoạt
     */
    public function verify($token) {
        $query = "SELECT * FROM {$this->table} WHERE verification_token = :token AND is_verified = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Đánh dấu đã xác minh và xóa token
     */
    public function setVerified($token) {
        $query = "UPDATE {$this->table} SET is_verified = 1, verification_token = NULL WHERE verification_token = :token";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":token", $token);
        return $stmt->execute();
    }

    /**
     * Kiểm tra email đã tồn tại chưa
     */
    public function emailExists($email) {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Lấy user theo email (mảng)
     */
    public function getByEmail($email) {
        $query = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy PDOStatement user theo email (dùng linh hoạt)
     */
    public function findByEmail($email) {
        $query = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Cập nhật mật khẩu mới (hash) theo email
     */
    public function updatePassword($email, $newPasswordHash) {
        $query = "UPDATE {$this->table} SET password = :password WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":password", $newPasswordHash);
        $stmt->bindParam(":email", $email);
        return $stmt->execute();
    }

    /**
     * Set a password reset token for the given email.
     * Tries to use `reset_token`/`reset_expires` columns if present,
     * otherwise falls back to using `verification_token` for compatibility.
     * @param string $email
     * @param string $token
     * @param int $expiresSeconds (optional) expiry in seconds from now, default 3600 (1 hour)
     * @return bool
     */
    public function setResetToken($email, $token, $expiresSeconds = 3600) {
        try {
            $sql = "UPDATE {$this->table} SET reset_token = :token, reset_expires = DATE_ADD(NOW(), INTERVAL :secs SECOND) WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':secs', $expiresSeconds, PDO::PARAM_INT);
            $stmt->bindParam(':email', $email);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Fallback if columns do not exist: store token in verification_token
            try {
                $sql2 = "UPDATE {$this->table} SET verification_token = :token WHERE email = :email";
                $stmt2 = $this->db->prepare($sql2);
                $stmt2->bindParam(':token', $token);
                $stmt2->bindParam(':email', $email);
                return $stmt2->execute();
            } catch (PDOException $e2) {
                error_log('[UserModel::setResetToken] fallback failed: ' . $e2->getMessage());
                return false;
            }
        }
    }

    /**
     * Find user by reset token. Returns PDOStatement or false.
     * Checks expiry when possible.
     */
    public function findByResetToken($token) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE reset_token = :token AND (reset_expires IS NULL OR reset_expires > NOW()) LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            // Fallback to verification_token if reset columns not available
            try {
                $sql2 = "SELECT * FROM {$this->table} WHERE verification_token = :token LIMIT 1";
                $stmt2 = $this->db->prepare($sql2);
                $stmt2->bindParam(':token', $token);
                $stmt2->execute();
                return $stmt2;
            } catch (PDOException $e2) {
                error_log('[UserModel::findByResetToken] error: ' . $e2->getMessage());
                return false;
            }
        }
    }

    /**
     * Clear reset token for an email (or fallback clear verification_token).
     */
    public function clearResetToken($email) {
        try {
            $sql = "UPDATE {$this->table} SET reset_token = NULL, reset_expires = NULL WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email);
            return $stmt->execute();
        } catch (PDOException $e) {
            try {
                $sql2 = "UPDATE {$this->table} SET verification_token = NULL WHERE email = :email";
                $stmt2 = $this->db->prepare($sql2);
                $stmt2->bindParam(':email', $email);
                return $stmt2->execute();
            } catch (PDOException $e2) {
                error_log('[UserModel::clearResetToken] fallback failed: ' . $e2->getMessage());
                return false;
            }
        }
    }

    /**
     * Update password by user id (safer than updating by email when email formatting might differ)
     * @param int $userId
     * @param string $newPasswordHash
     * @return bool
     */
    public function updatePasswordById($userId, $newPasswordHash) {
        try {
            $sql = "UPDATE {$this->table} SET password = :password WHERE user_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':password', $newPasswordHash);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('[UserModel::updatePasswordById] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear reset token by user id
     * @param int $userId
     * @return bool
     */
    public function clearResetTokenById($userId) {
        try {
            $sql = "UPDATE {$this->table} SET reset_token = NULL, reset_expires = NULL WHERE user_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            try {
                $sql2 = "UPDATE {$this->table} SET verification_token = NULL WHERE user_id = :id";
                $stmt2 = $this->db->prepare($sql2);
                $stmt2->bindParam(':id', $userId, PDO::PARAM_INT);
                return $stmt2->execute();
            } catch (PDOException $e2) {
                error_log('[UserModel::clearResetTokenById] fallback failed: ' . $e2->getMessage());
                return false;
            }
        }
    }

    /**
     * Nội bộ: PDOStatement người dùng theo id
     */
    public function findByIdInternal($id) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Khóa tài khoản người dùng
     * @param int $id
     * @return bool
     */
    public function lockAccount($id) {
        try {
            $sql = "UPDATE {$this->table} SET is_locked = 1 WHERE user_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('[UserModel::lockAccount] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mở khóa tài khoản người dùng
     * @param int $id
     * @return bool
     */
    public function unlockAccount($id) {
        try {
            $sql = "UPDATE {$this->table} SET is_locked = 0 WHERE user_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('[UserModel::unlockAccount] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra xem tài khoản có bị khóa không
     * @param int $id
     * @return bool
     */
    public function isAccountLocked($id) {
        try {
            $sql = "SELECT is_locked FROM {$this->table} WHERE user_id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result && (int)$result['is_locked'] === 1;
        } catch (PDOException $e) {
            error_log('[UserModel::isAccountLocked] ' . $e->getMessage());
            return false;
        }
    }
}