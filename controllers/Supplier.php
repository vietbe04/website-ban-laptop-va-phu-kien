<?php
/**
 * Controller quản lý nhà cung cấp
 */
class Supplier extends Controller {
    
    // ==================== QUẢN LÝ NHÀ CUNG CẤP ====================
    
    /**
     * Trang danh sách nhà cung cấp
     */
    public function index() {
        $this->requireRole(['admin', 'staff'], 'supplier-management');
        
        $search = $_GET['search'] ?? '';
        $status = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $model = $this->model('SupplierModel');
        $list = $model->getSuppliers($search, $status, $perPage, $offset);
        $total = $model->countSuppliers($search, $status);
        $totalPages = ceil($total / $perPage);
        
        $this->view('adminPage', [
            'page' => 'SupplierListView',
            'suppliers' => $list,
            'search' => $search,
            'status' => $status,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total
        ]);
    }
    
    /**
     * Hiển thị form tạo nhà cung cấp mới
     */
    public function create() {
        $this->requireRole(['admin', 'staff'], 'supplier-management');
        $this->view('adminPage', ['page' => 'SupplierFormView', 'action' => 'create']);
    }
    
    /**
     * Xử lý tạo nhà cung cấp mới
     */
    public function store() {
        $this->requireRole(['admin', 'staff'], 'supplier-management');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/Supplier/index');
            exit();
        }
        
        $model = $this->model('SupplierModel');
        
        $data = [
            'code' => trim($_POST['code'] ?? ''),
            'name' => trim($_POST['name'] ?? ''),
            'contact_person' => trim($_POST['contact_person'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'tax_code' => trim($_POST['tax_code'] ?? ''),
            'bank_account' => trim($_POST['bank_account'] ?? ''),
            'bank_name' => trim($_POST['bank_name'] ?? ''),
            'status' => isset($_POST['status']) ? (int)$_POST['status'] : 1,
            'notes' => trim($_POST['notes'] ?? '')
        ];
        
        // Validation
        if (empty($data['code']) || empty($data['name'])) {
            $_SESSION['flash_message'] = 'Mã và tên nhà cung cấp không được để trống';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/Supplier/create');
            exit();
        }
        
        if ($model->isCodeExists($data['code'])) {
            $_SESSION['flash_message'] = 'Mã nhà cung cấp đã tồn tại';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/Supplier/create');
            exit();
        }
        
        if ($model->createSupplier($data)) {
            $_SESSION['flash_message'] = 'Tạo nhà cung cấp thành công';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Có lỗi xảy ra';
            $_SESSION['flash_type'] = 'danger';
        }
        
        header('Location: ' . APP_URL . '/Supplier/index');
        exit();
    }
    
    /**
     * Hiển thị form sửa nhà cung cấp
     */
    public function edit($id) {
        $this->requireRole(['admin', 'staff'], 'supplier-management');
        
        $model = $this->model('SupplierModel');
        $supplier = $model->getSupplierById($id);
        
        if (!$supplier) {
            $_SESSION['flash_message'] = 'Không tìm thấy nhà cung cấp';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/Supplier/index');
            exit();
        }
        
        $this->view('adminPage', [
            'page' => 'SupplierFormView',
            'action' => 'edit',
            'supplier' => $supplier
        ]);
    }
    
    /**
     * Xử lý cập nhật nhà cung cấp
     */
    public function update($id) {
        $this->requireRole(['admin', 'staff'], 'supplier-management');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/Supplier/index');
            exit();
        }
        
        $model = $this->model('SupplierModel');
        
        $data = [
            'code' => trim($_POST['code'] ?? ''),
            'name' => trim($_POST['name'] ?? ''),
            'contact_person' => trim($_POST['contact_person'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'tax_code' => trim($_POST['tax_code'] ?? ''),
            'bank_account' => trim($_POST['bank_account'] ?? ''),
            'bank_name' => trim($_POST['bank_name'] ?? ''),
            'status' => isset($_POST['status']) ? (int)$_POST['status'] : 1,
            'notes' => trim($_POST['notes'] ?? '')
        ];
        
        // Validation
        if (empty($data['code']) || empty($data['name'])) {
            $_SESSION['flash_message'] = 'Mã và tên nhà cung cấp không được để trống';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/Supplier/edit/' . $id);
            exit();
        }
        
        if ($model->isCodeExists($data['code'], $id)) {
            $_SESSION['flash_message'] = 'Mã nhà cung cấp đã tồn tại';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/Supplier/edit/' . $id);
            exit();
        }
        
        if ($model->updateSupplier($id, $data)) {
            $_SESSION['flash_message'] = 'Cập nhật nhà cung cấp thành công';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Có lỗi xảy ra';
            $_SESSION['flash_type'] = 'danger';
        }
        
        header('Location: ' . APP_URL . '/Supplier/index');
        exit();
    }
    
    /**
     * Xóa nhà cung cấp
     */
    public function delete($id) {
        $this->requireRole(['admin'], 'supplier-management');
        
        $model = $this->model('SupplierModel');
        
        if ($model->deleteSupplier($id)) {
            $_SESSION['flash_message'] = 'Xóa nhà cung cấp thành công';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Có lỗi xảy ra';
            $_SESSION['flash_type'] = 'danger';
        }
        
        header('Location: ' . APP_URL . '/Supplier/index');
        exit();
    }
    
    // ==================== QUẢN LÝ HỢP ĐỒNG ====================
    
    /**
     * Danh sách hợp đồng
     */
    public function contracts() {
        $this->requireRole(['admin', 'staff'], 'supplier-management');
        
        $supplierId = isset($_GET['supplier_id']) && $_GET['supplier_id'] !== '' ? (int)$_GET['supplier_id'] : null;
        $status = $_GET['status'] ?? null;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $model = $this->model('SupplierModel');
        $list = $model->getContracts($supplierId, $status, $perPage, $offset);
        $total = $model->countContracts($supplierId, $status);
        $totalPages = ceil($total / $perPage);
        $suppliers = $model->getAllSuppliersForDropdown();
        
        $this->view('adminPage', [
            'page' => 'ContractListView',
            'contracts' => $list,
            'suppliers' => $suppliers,
            'supplierId' => $supplierId,
            'status' => $status,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total
        ]);
    }
    
    /**
     * Form tạo hợp đồng
     */
    public function createContract() {
        $this->requireRole(['admin', 'staff'], 'supplier-management');
        
        $model = $this->model('SupplierModel');
        $suppliers = $model->getAllSuppliersForDropdown();
        
        $this->view('adminPage', [
            'page' => 'ContractFormView',
            'action' => 'create',
            'suppliers' => $suppliers
        ]);
    }
    
    /**
     * Lưu hợp đồng mới
     */
    public function storeContract() {
        $this->requireRole(['admin', 'staff'], 'supplier-management');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/Supplier/contracts');
            exit();
        }
        
        $model = $this->model('SupplierModel');
        
        $data = [
            'supplier_id' => (int)$_POST['supplier_id'],
            'contract_number' => trim($_POST['contract_number'] ?? ''),
            'contract_name' => trim($_POST['contract_name'] ?? ''),
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['end_date'] ?? null,
            'contract_value' => (float)($_POST['contract_value'] ?? 0),
            'payment_terms' => trim($_POST['payment_terms'] ?? ''),
            'delivery_terms' => trim($_POST['delivery_terms'] ?? ''),
            'status' => $_POST['status'] ?? 'active',
            'file_path' => null,
            'notes' => trim($_POST['notes'] ?? '')
        ];
        
        if ($model->createContract($data)) {
            $_SESSION['flash_message'] = 'Tạo hợp đồng thành công';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Có lỗi xảy ra';
            $_SESSION['flash_type'] = 'danger';
        }
        
        header('Location: ' . APP_URL . '/Supplier/contracts');
        exit();
    }
    
    /**
     * Form sửa hợp đồng
     */
    public function editContract($id) {
        $this->requireRole(['admin', 'staff'], 'supplier-management');
        
        $model = $this->model('SupplierModel');
        $contract = $model->getContractById($id);
        $suppliers = $model->getAllSuppliersForDropdown();
        
        if (!$contract) {
            $_SESSION['flash_message'] = 'Không tìm thấy hợp đồng';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/Supplier/contracts');
            exit();
        }
        
        $this->view('adminPage', [
            'page' => 'ContractFormView',
            'action' => 'edit',
            'contract' => $contract,
            'suppliers' => $suppliers
        ]);
    }
    
    /**
     * Cập nhật hợp đồng
     */
    public function updateContract($id) {
        $this->requireRole(['admin', 'staff'], 'supplier-management');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/Supplier/contracts');
            exit();
        }
        
        $model = $this->model('SupplierModel');
        
        $data = [
            'supplier_id' => (int)$_POST['supplier_id'],
            'contract_number' => trim($_POST['contract_number'] ?? ''),
            'contract_name' => trim($_POST['contract_name'] ?? ''),
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['end_date'] ?? null,
            'contract_value' => (float)($_POST['contract_value'] ?? 0),
            'payment_terms' => trim($_POST['payment_terms'] ?? ''),
            'delivery_terms' => trim($_POST['delivery_terms'] ?? ''),
            'status' => $_POST['status'] ?? 'active',
            'file_path' => null,
            'notes' => trim($_POST['notes'] ?? '')
        ];
        
        if ($model->updateContract($id, $data)) {
            $_SESSION['flash_message'] = 'Cập nhật hợp đồng thành công';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Có lỗi xảy ra';
            $_SESSION['flash_type'] = 'danger';
        }
        
        header('Location: ' . APP_URL . '/Supplier/contracts');
        exit();
    }
    
    /**
     * Xóa hợp đồng
     */
    public function deleteContract($id) {
        $this->requireRole(['admin'], 'supplier-management');
        
        $model = $this->model('SupplierModel');
        
        if ($model->deleteContract($id)) {
            $_SESSION['flash_message'] = 'Xóa hợp đồng thành công';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Có lỗi xảy ra';
            $_SESSION['flash_type'] = 'danger';
        }
        
        header('Location: ' . APP_URL . '/Supplier/contracts');
        exit();
    }
    
    // ==================== QUẢN LÝ HÀNG HÓA ====================
    
    /**
     * Danh sách hàng hóa
     */
    public function products() {
        $this->requireRole(['admin', 'staff'], 'supplier-management');
        
        $supplierId = isset($_GET['supplier_id']) && $_GET['supplier_id'] !== '' ? (int)$_GET['supplier_id'] : null;
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? null;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $model = $this->model('SupplierModel');
        $list = $model->getSupplierProducts($supplierId, $search, $category, $perPage, $offset);
        $total = $model->countSupplierProducts($supplierId, $search, $category);
        $totalPages = ceil($total / $perPage);
        $suppliers = $model->getAllSuppliersForDropdown();
        $categories = $model->getCategories();
        
        $this->view('adminPage', [
            'page' => 'SupplierProductListView',
            'products' => $list,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'supplierId' => $supplierId,
            'search' => $search,
            'category' => $category,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total
        ]);
    }
    
    /**
     * Form tạo hàng hóa
     */
    public function createProduct() {
        $this->requireRole(['admin', 'staff'], 'supplier-management');
        
        $model = $this->model('SupplierModel');
        $suppliers = $model->getAllSuppliersForDropdown();
        $categories = $model->getCategories();
        
        $this->view('adminPage', [
            'page' => 'SupplierProductFormView',
            'action' => 'create',
            'suppliers' => $suppliers,
            'categories' => $categories
        ]);
    }
    
    /**
     * Lưu hàng hóa mới
     */
    public function storeProduct() {
        $this->requireRole(['admin', 'staff'], 'supplier-management');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/Supplier/products');
            exit();
        }
        
        $model = $this->model('SupplierModel');
        
        $data = [
            'supplier_id' => (int)$_POST['supplier_id'],
            'product_code' => trim($_POST['product_code'] ?? ''),
            'product_name' => trim($_POST['product_name'] ?? ''),
            'category' => trim($_POST['category'] ?? ''),
            'unit' => trim($_POST['unit'] ?? ''),
            'unit_price' => (float)($_POST['unit_price'] ?? 0),
            'currency' => $_POST['currency'] ?? 'VND',
            'min_order_quantity' => (int)($_POST['min_order_quantity'] ?? 1),
            'lead_time_days' => (int)($_POST['lead_time_days'] ?? 0),
            'warranty_period' => trim($_POST['warranty_period'] ?? ''),
            'status' => isset($_POST['status']) ? (int)$_POST['status'] : 1,
            'notes' => trim($_POST['notes'] ?? '')
        ];
        
        if ($model->createSupplierProduct($data)) {
            $_SESSION['flash_message'] = 'Tạo hàng hóa thành công';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Có lỗi xảy ra';
            $_SESSION['flash_type'] = 'danger';
        }
        
        header('Location: ' . APP_URL . '/Supplier/products');
        exit();
    }
    
    /**
     * Form sửa hàng hóa
     */
    public function editProduct($id) {
        $this->requireRole(['admin', 'staff'], 'supplier-management');
        
        $model = $this->model('SupplierModel');
        $product = $model->getSupplierProductById($id);
        $suppliers = $model->getAllSuppliersForDropdown();
        $categories = $model->getCategories();
        
        if (!$product) {
            $_SESSION['flash_message'] = 'Không tìm thấy hàng hóa';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/Supplier/products');
            exit();
        }
        
        $this->view('adminPage', [
            'page' => 'SupplierProductFormView',
            'action' => 'edit',
            'product' => $product,
            'suppliers' => $suppliers,
            'categories' => $categories
        ]);
    }
    
    /**
     * Cập nhật hàng hóa
     */
    public function updateProduct($id) {
        $this->requireRole(['admin', 'staff'], 'supplier-management');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/Supplier/products');
            exit();
        }
        
        $model = $this->model('SupplierModel');
        
        $data = [
            'supplier_id' => (int)$_POST['supplier_id'],
            'product_code' => trim($_POST['product_code'] ?? ''),
            'product_name' => trim($_POST['product_name'] ?? ''),
            'category' => trim($_POST['category'] ?? ''),
            'unit' => trim($_POST['unit'] ?? ''),
            'unit_price' => (float)($_POST['unit_price'] ?? 0),
            'currency' => $_POST['currency'] ?? 'VND',
            'min_order_quantity' => (int)($_POST['min_order_quantity'] ?? 1),
            'lead_time_days' => (int)($_POST['lead_time_days'] ?? 0),
            'warranty_period' => trim($_POST['warranty_period'] ?? ''),
            'status' => isset($_POST['status']) ? (int)$_POST['status'] : 1,
            'notes' => trim($_POST['notes'] ?? '')
        ];
        
        if ($model->updateSupplierProduct($id, $data)) {
            $_SESSION['flash_message'] = 'Cập nhật hàng hóa thành công';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Có lỗi xảy ra';
            $_SESSION['flash_type'] = 'danger';
        }
        
        header('Location: ' . APP_URL . '/Supplier/products');
        exit();
    }
    
    /**
     * Xóa hàng hóa
     */
    public function deleteProduct($id) {
        $this->requireRole(['admin'], 'supplier-management');
        
        $model = $this->model('SupplierModel');
        
        if ($model->deleteSupplierProduct($id)) {
            $_SESSION['flash_message'] = 'Xóa hàng hóa thành công';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Có lỗi xảy ra';
            $_SESSION['flash_type'] = 'danger';
        }
        
        header('Location: ' . APP_URL . '/Supplier/products');
        exit();
    }
}
