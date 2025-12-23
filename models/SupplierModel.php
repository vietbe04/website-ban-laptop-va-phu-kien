<?php
/**
 * Model quản lý nhà cung cấp
 * - CRUD nhà cung cấp
 * - Quản lý hợp đồng
 * - Quản lý danh mục hàng hóa cung cấp
 */
class SupplierModel extends DB {
    
    // ==================== QUẢN LÝ NHÀ CUNG CẤP ====================
    
    /**
     * Lấy danh sách nhà cung cấp với tìm kiếm và phân trang
     */
    public function getSuppliers($search = '', $status = null, $limit = 20, $offset = 0) {
        $conditions = [];
        $params = [];
        
        if ($search !== '') {
            $conditions[] = "(code LIKE :search OR name LIKE :search OR contact_person LIKE :search OR phone LIKE :search)";
            $params[':search'] = "%{$search}%";
        }
        
        if ($status !== null) {
            $conditions[] = "status = :status";
            $params[':status'] = $status;
        }
        
        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        
        $sql = "SELECT * FROM suppliers {$where} ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Đếm tổng số nhà cung cấp
     */
    public function countSuppliers($search = '', $status = null) {
        $conditions = [];
        $params = [];
        
        if ($search !== '') {
            $conditions[] = "(code LIKE :search OR name LIKE :search OR contact_person LIKE :search OR phone LIKE :search)";
            $params[':search'] = "%{$search}%";
        }
        
        if ($status !== null) {
            $conditions[] = "status = :status";
            $params[':status'] = $status;
        }
        
        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        
        $sql = "SELECT COUNT(*) FROM suppliers {$where}";
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
    
    /**
     * Lấy thông tin nhà cung cấp theo ID
     */
    public function getSupplierById($id) {
        $sql = "SELECT * FROM suppliers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Tạo nhà cung cấp mới
     */
    public function createSupplier($data) {
        $sql = "INSERT INTO suppliers (code, name, contact_person, phone, email, address, tax_code, bank_account, bank_name, status, notes) 
                VALUES (:code, :name, :contact_person, :phone, :email, :address, :tax_code, :bank_account, :bank_name, :status, :notes)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':code' => $data['code'],
            ':name' => $data['name'],
            ':contact_person' => $data['contact_person'] ?? null,
            ':phone' => $data['phone'] ?? null,
            ':email' => $data['email'] ?? null,
            ':address' => $data['address'] ?? null,
            ':tax_code' => $data['tax_code'] ?? null,
            ':bank_account' => $data['bank_account'] ?? null,
            ':bank_name' => $data['bank_name'] ?? null,
            ':status' => $data['status'] ?? 1,
            ':notes' => $data['notes'] ?? null
        ]);
    }
    
    /**
     * Cập nhật thông tin nhà cung cấp
     */
    public function updateSupplier($id, $data) {
        $sql = "UPDATE suppliers SET 
                code = :code, name = :name, contact_person = :contact_person, phone = :phone, 
                email = :email, address = :address, tax_code = :tax_code, bank_account = :bank_account, 
                bank_name = :bank_name, status = :status, notes = :notes 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':code' => $data['code'],
            ':name' => $data['name'],
            ':contact_person' => $data['contact_person'] ?? null,
            ':phone' => $data['phone'] ?? null,
            ':email' => $data['email'] ?? null,
            ':address' => $data['address'] ?? null,
            ':tax_code' => $data['tax_code'] ?? null,
            ':bank_account' => $data['bank_account'] ?? null,
            ':bank_name' => $data['bank_name'] ?? null,
            ':status' => $data['status'] ?? 1,
            ':notes' => $data['notes'] ?? null
        ]);
    }
    
    /**
     * Xóa nhà cung cấp
     */
    public function deleteSupplier($id) {
        $sql = "DELETE FROM suppliers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Kiểm tra mã nhà cung cấp đã tồn tại
     */
    public function isCodeExists($code, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM suppliers WHERE code = :code";
        if ($excludeId) {
            $sql .= " AND id != :id";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':code', $code);
        if ($excludeId) {
            $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    // ==================== QUẢN LÝ HỢP ĐỒNG ====================
    
    /**
     * Lấy danh sách hợp đồng theo nhà cung cấp
     */
    public function getContracts($supplierId = null, $status = null, $limit = 20, $offset = 0) {
        $conditions = [];
        $params = [];
        
        if ($supplierId) {
            $conditions[] = "sc.supplier_id = :supplier_id";
            $params[':supplier_id'] = $supplierId;
        }
        
        if ($status) {
            $conditions[] = "sc.status = :status";
            $params[':status'] = $status;
        }
        
        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        
        $sql = "SELECT sc.*, s.name as supplier_name, s.code as supplier_code 
                FROM supplier_contracts sc
                LEFT JOIN suppliers s ON sc.supplier_id = s.id
                {$where} 
                ORDER BY sc.start_date DESC 
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Đếm số hợp đồng
     */
    public function countContracts($supplierId = null, $status = null) {
        $conditions = [];
        $params = [];
        
        if ($supplierId) {
            $conditions[] = "supplier_id = :supplier_id";
            $params[':supplier_id'] = $supplierId;
        }
        
        if ($status) {
            $conditions[] = "status = :status";
            $params[':status'] = $status;
        }
        
        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        
        $sql = "SELECT COUNT(*) FROM supplier_contracts {$where}";
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
    
    /**
     * Lấy thông tin hợp đồng theo ID
     */
    public function getContractById($id) {
        $sql = "SELECT sc.*, s.name as supplier_name, s.code as supplier_code 
                FROM supplier_contracts sc
                LEFT JOIN suppliers s ON sc.supplier_id = s.id
                WHERE sc.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Tạo hợp đồng mới
     */
    public function createContract($data) {
        $sql = "INSERT INTO supplier_contracts (supplier_id, contract_number, contract_name, start_date, end_date, 
                contract_value, payment_terms, delivery_terms, status, file_path, notes) 
                VALUES (:supplier_id, :contract_number, :contract_name, :start_date, :end_date, 
                :contract_value, :payment_terms, :delivery_terms, :status, :file_path, :notes)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':supplier_id' => $data['supplier_id'],
            ':contract_number' => $data['contract_number'],
            ':contract_name' => $data['contract_name'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'] ?? null,
            ':contract_value' => $data['contract_value'] ?? 0,
            ':payment_terms' => $data['payment_terms'] ?? null,
            ':delivery_terms' => $data['delivery_terms'] ?? null,
            ':status' => $data['status'] ?? 'active',
            ':file_path' => $data['file_path'] ?? null,
            ':notes' => $data['notes'] ?? null
        ]);
    }
    
    /**
     * Cập nhật hợp đồng
     */
    public function updateContract($id, $data) {
        $sql = "UPDATE supplier_contracts SET 
                supplier_id = :supplier_id, contract_number = :contract_number, contract_name = :contract_name, 
                start_date = :start_date, end_date = :end_date, contract_value = :contract_value, 
                payment_terms = :payment_terms, delivery_terms = :delivery_terms, status = :status, 
                file_path = :file_path, notes = :notes 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':supplier_id' => $data['supplier_id'],
            ':contract_number' => $data['contract_number'],
            ':contract_name' => $data['contract_name'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'] ?? null,
            ':contract_value' => $data['contract_value'] ?? 0,
            ':payment_terms' => $data['payment_terms'] ?? null,
            ':delivery_terms' => $data['delivery_terms'] ?? null,
            ':status' => $data['status'] ?? 'active',
            ':file_path' => $data['file_path'] ?? null,
            ':notes' => $data['notes'] ?? null
        ]);
    }
    
    /**
     * Xóa hợp đồng
     */
    public function deleteContract($id) {
        $sql = "DELETE FROM supplier_contracts WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // ==================== QUẢN LÝ HÀNG HÓA CUNG CẤP ====================
    
    /**
     * Lấy danh sách hàng hóa cung cấp
     */
    public function getSupplierProducts($supplierId = null, $search = '', $category = null, $limit = 20, $offset = 0) {
        $conditions = [];
        $params = [];
        
        if ($supplierId) {
            $conditions[] = "sp.supplier_id = :supplier_id";
            $params[':supplier_id'] = $supplierId;
        }
        
        if ($search !== '') {
            $conditions[] = "(sp.product_code LIKE :search OR sp.product_name LIKE :search)";
            $params[':search'] = "%{$search}%";
        }
        
        if ($category) {
            $conditions[] = "sp.category = :category";
            $params[':category'] = $category;
        }
        
        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        
        $sql = "SELECT sp.*, s.name as supplier_name, s.code as supplier_code 
                FROM supplier_products sp
                LEFT JOIN suppliers s ON sp.supplier_id = s.id
                {$where} 
                ORDER BY sp.created_at DESC 
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Đếm số hàng hóa
     */
    public function countSupplierProducts($supplierId = null, $search = '', $category = null) {
        $conditions = [];
        $params = [];
        
        if ($supplierId) {
            $conditions[] = "supplier_id = :supplier_id";
            $params[':supplier_id'] = $supplierId;
        }
        
        if ($search !== '') {
            $conditions[] = "(product_code LIKE :search OR product_name LIKE :search)";
            $params[':search'] = "%{$search}%";
        }
        
        if ($category) {
            $conditions[] = "category = :category";
            $params[':category'] = $category;
        }
        
        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        
        $sql = "SELECT COUNT(*) FROM supplier_products {$where}";
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
    
    /**
     * Lấy thông tin hàng hóa theo ID
     */
    public function getSupplierProductById($id) {
        $sql = "SELECT sp.*, s.name as supplier_name, s.code as supplier_code 
                FROM supplier_products sp
                LEFT JOIN suppliers s ON sp.supplier_id = s.id
                WHERE sp.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Tạo hàng hóa mới
     */
    public function createSupplierProduct($data) {
        $sql = "INSERT INTO supplier_products (supplier_id, product_code, product_name, category, unit, 
                unit_price, currency, min_order_quantity, lead_time_days, warranty_period, status, notes) 
                VALUES (:supplier_id, :product_code, :product_name, :category, :unit, 
                :unit_price, :currency, :min_order_quantity, :lead_time_days, :warranty_period, :status, :notes)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':supplier_id' => $data['supplier_id'],
            ':product_code' => $data['product_code'] ?? null,
            ':product_name' => $data['product_name'],
            ':category' => $data['category'] ?? null,
            ':unit' => $data['unit'] ?? null,
            ':unit_price' => $data['unit_price'] ?? 0,
            ':currency' => $data['currency'] ?? 'VND',
            ':min_order_quantity' => $data['min_order_quantity'] ?? 1,
            ':lead_time_days' => $data['lead_time_days'] ?? 0,
            ':warranty_period' => $data['warranty_period'] ?? null,
            ':status' => $data['status'] ?? 1,
            ':notes' => $data['notes'] ?? null
        ]);
    }
    
    /**
     * Cập nhật hàng hóa
     */
    public function updateSupplierProduct($id, $data) {
        $sql = "UPDATE supplier_products SET 
                supplier_id = :supplier_id, product_code = :product_code, product_name = :product_name, 
                category = :category, unit = :unit, unit_price = :unit_price, currency = :currency, 
                min_order_quantity = :min_order_quantity, lead_time_days = :lead_time_days, 
                warranty_period = :warranty_period, status = :status, notes = :notes 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':supplier_id' => $data['supplier_id'],
            ':product_code' => $data['product_code'] ?? null,
            ':product_name' => $data['product_name'],
            ':category' => $data['category'] ?? null,
            ':unit' => $data['unit'] ?? null,
            ':unit_price' => $data['unit_price'] ?? 0,
            ':currency' => $data['currency'] ?? 'VND',
            ':min_order_quantity' => $data['min_order_quantity'] ?? 1,
            ':lead_time_days' => $data['lead_time_days'] ?? 0,
            ':warranty_period' => $data['warranty_period'] ?? null,
            ':status' => $data['status'] ?? 1,
            ':notes' => $data['notes'] ?? null
        ]);
    }
    
    /**
     * Xóa hàng hóa
     */
    public function deleteSupplierProduct($id) {
        $sql = "DELETE FROM supplier_products WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Lấy danh sách category duy nhất
     */
    public function getCategories() {
        $sql = "SELECT DISTINCT category FROM supplier_products WHERE category IS NOT NULL ORDER BY category";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Lấy tất cả suppliers cho dropdown
     */
    public function getAllSuppliersForDropdown() {
        $sql = "SELECT id, code, name FROM suppliers WHERE status = 1 ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
