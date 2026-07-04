<?php
// Mbatha's iPhone Plug - Admin Product CRUD Manager

require_once '../config/db.php';
require_once '../includes/functions.php';

// Gate Access
requireAdminLogin();

// -----------------------------------------------------
// Handle Actions (Add, Edit, Delete, Mark Sold)
// -----------------------------------------------------

// 1. Delete Product
if (isset($_GET['delete'])) {
    $prod_id = (int)$_GET['delete'];
    
    // Fetch image path to remove file from server
    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt->execute([$prod_id]);
    $img = $stmt->fetchColumn();
    if ($img && file_exists('../' . $img) && strpos($img, 'placeholder') === false) {
        unlink('../' . $img);
    }
    
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$prod_id]);
    
    setFlashMessage("Product deleted successfully.", "success");
    header("Location: products.php");
    exit;
}

// 2. Mark Sold Out (Quick Stock Adjustment)
if (isset($_GET['sold'])) {
    $prod_id = (int)$_GET['sold'];
    $stmt = $pdo->prepare("UPDATE products SET stock = 0 WHERE id = ?");
    $stmt->execute([$prod_id]);
    
    setFlashMessage("Product marked as SOLD OUT.", "info");
    header("Location: products.php");
    exit;
}

// 3. Add / Edit Product POST Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
    $prod_id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $name = sanitize($_POST['name']);
    $type = sanitize($_POST['type']); // 'iphone', 'accessory'
    $category = sanitize($_POST['category']); // 'Phone', 'Case', etc.
    $model = !empty($_POST['model']) ? sanitize($_POST['model']) : null;
    $price = (float)$_POST['price'];
    $original_price = !empty($_POST['original_price']) ? (float)$_POST['original_price'] : null;
    $storage = !empty($_POST['storage']) ? sanitize($_POST['storage']) : null;
    $color = !empty($_POST['color']) ? sanitize($_POST['color']) : null;
    $battery_health = !empty($_POST['battery_health']) ? (int)$_POST['battery_health'] : null;
    $grade = sanitize($_POST['grade']);
    $warranty = sanitize($_POST['warranty']);
    $stock = (int)$_POST['stock'];
    $description = sanitize($_POST['description']);
    
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_flash_sale = isset($_POST['is_flash_sale']) ? 1 : 0;
    
    // File upload logic
    $image_url = !empty($_POST['existing_image']) ? $_POST['existing_image'] : '';
    
    ensureImageDirectory();
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['product_image']['tmp_name'];
        $file_name = $_FILES['product_image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($file_ext, $allowed_exts)) {
            $unique_name = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $file_ext;
            $upload_dest = __DIR__ . '/../assets/images/products/' . $unique_name;
            
            if (move_uploaded_file($file_tmp, $upload_dest)) {
                // Delete previous file if replacing
                if ($image_url && file_exists('../' . $image_url) && strpos($image_url, 'placeholder') === false) {
                    unlink('../' . $image_url);
                }
                $image_url = 'assets/images/products/' . $unique_name;
            }
        }
    }
    
    if (empty($image_url)) {
        // Assign standard template fallback if no file supplied
        $image_url = 'assets/images/products/placeholder.jpg';
    }

    if ($prod_id) {
        // Edit Mode SQL
        $sql = "UPDATE products SET name=?, type=?, category=?, model=?, price=?, original_price=?, storage=?, color=?, battery_health=?, grade=?, warranty=?, stock=?, description=?, is_featured=?, is_flash_sale=?, image_url=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $type, $category, $model, $price, $original_price, $storage, $color, $battery_health, $grade, $warranty, $stock, $description, $is_featured, $is_flash_sale, $image_url, $prod_id]);
        setFlashMessage("Product details updated successfully.", "success");
    } else {
        // Add Mode SQL
        $sql = "INSERT INTO products (name, type, category, model, price, original_price, storage, color, battery_health, grade, warranty, stock, description, is_featured, is_flash_sale, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $type, $category, $model, $price, $original_price, $storage, $color, $battery_health, $grade, $warranty, $stock, $description, $is_featured, $is_flash_sale, $image_url]);
        setFlashMessage("New product cataloged successfully.", "success");
    }
    
    header("Location: products.php");
    exit;
}

// Fetch All Products list
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products CRUD | Mbatha's iPhone Plug</title>
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body class="admin-body">

    <!-- Admin Sidebar -->
    <div class="admin-sidebar">
        <div class="admin-logo">
            <svg width="30" height="30" viewBox="0 0 200 200" style="filter: drop-shadow(0px 2px 4px rgba(212,175,55,0.3));">
                <circle cx="100" cy="100" r="90" fill="none" stroke="#d4af37" stroke-width="6"/>
                <rect x="75" y="45" width="50" height="95" rx="10" fill="none" stroke="#ffffff" stroke-width="4"/>
                <path d="M 60 160 Q 80 120, 90 90 L 100 110" fill="none" stroke="#d4af37" stroke-width="5" stroke-linecap="round"/>
            </svg>
            <span>MBATHA'S PLUG</span>
        </div>
        
        <nav class="admin-nav">
            <a href="dashboard.php" class="admin-nav-link"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="products.php" class="admin-nav-link active"><i class="fas fa-mobile-alt"></i> Products CRUD</a>
            <a href="orders.php" class="admin-nav-link"><i class="fas fa-receipt"></i> Orders List</a>
            <a href="tradeins.php" class="admin-nav-link"><i class="fas fa-exchange-alt"></i> Trade-Ins Manager</a>
            <a href="customers.php" class="admin-nav-link"><i class="fas fa-users"></i> Customers Registry</a>
        </nav>
        
        <div class="admin-logout-btn">
            <a href="logout.php" class="admin-nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Log Out</a>
        </div>
    </div>

    <!-- Admin Wrapper Content -->
    <div class="admin-wrapper">
        <header class="admin-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div>
                <h1 class="text-white fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Product Catalog Inventory</h1>
                <p class="text-secondary small mb-0">Create, Read, Update, and Delete products or accessories.</p>
            </div>
            <!-- Trigger Add Modal -->
            <button class="btn btn-gold py-2 px-4" data-bs-toggle="modal" data-bs-target="#productModal" onclick="openAddModal()"><i class="fas fa-plus me-2"></i> Add New Product</button>
        </header>

        <!-- Display flash notices -->
        <?php displayFlashMessage(); ?>

        <!-- Products Table card -->
        <div class="admin-panel-card">
            <div class="table-responsive">
                <table class="table glass-table text-white mb-0 align-middle">
                    <thead>
                        <tr class="text-muted border-bottom border-secondary border-opacity-25">
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Promo Details</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td>
                                    <!-- Dynamic fallback display image -->
                                    <img src="../<?php echo getProductImage($p['image_url'], $p['name'], $p['category']); ?>" alt="product" style="width: 50px; height: 50px; object-fit: contain; background: rgba(255,255,255,0.03); border-radius: 8px; padding: 4px;">
                                </td>
                                <td>
                                    <h6 class="text-white mb-1 fw-bold"><?php echo sanitize($p['name']); ?></h6>
                                    <span class="text-muted small"><?php echo sanitize($p['storage'] . " | " . $p['color'] . " | Grade: " . $p['grade']); ?></span>
                                </td>
                                <td><span class="text-secondary small"><?php echo sanitize($p['category']); ?></span></td>
                                <td class="text-center text-warning fw-semibold"><?php echo formatPrice($p['price']); ?></td>
                                <td class="text-center">
                                    <span class="badge <?php echo $p['stock'] > 0 ? 'bg-success' : 'bg-danger'; ?> rounded-pill px-2 py-1 small">
                                        <?php echo $p['stock'] > 0 ? $p['stock'] : 'SOLD'; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($p['is_featured']): ?>
                                        <span class="badge bg-warning text-dark small me-1">Featured</span>
                                    <?php endif; ?>
                                    <?php if ($p['is_flash_sale']): ?>
                                        <span class="badge bg-danger small">Flash Sale</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <?php if ($p['stock'] > 0): ?>
                                            <a href="products.php?sold=<?php echo $p['id']; ?>" class="btn btn-outline-warning btn-sm py-1 px-2" title="Mark Sold Out" style="font-size: 0.8rem;"><i class="fas fa-ban"></i> Sold</a>
                                        <?php endif; ?>
                                        <button class="btn btn-outline-info btn-sm py-1 px-2" title="Edit details" style="font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#productModal" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($p)); ?>)"><i class="fas fa-edit"></i> Edit</button>
                                        <a href="products.php?delete=<?php echo $p['id']; ?>" class="btn btn-outline-danger btn-sm py-1 px-2" title="Delete product" style="font-size: 0.8rem;" onclick="return confirm('Are you sure you want to delete this product?');"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Product CRUD Modal -->
    <div class="modal fade admin-modal" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="products.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header border-bottom border-secondary border-opacity-25">
                        <h5 class="modal-title text-white fw-bold" id="modalTitle">Catalog Product</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-close="modal" data-bs-target="#productModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <!-- Hidden ID -->
                        <input type="hidden" name="id" id="prod_id">
                        <input type="hidden" name="existing_image" id="existing_image">

                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label-custom">Product Display Name</label>
                                <input type="text" name="name" id="prod_name" class="form-control form-control-custom" placeholder="e.g. iPhone 15 Pro Max" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">Catalog Type</label>
                                <select name="type" id="prod_type" class="form-select form-control-custom" required>
                                    <option value="iphone">iPhone</option>
                                    <option value="accessory">Accessory</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label-custom">Category</label>
                                <select name="category" id="prod_category" class="form-select form-control-custom" required>
                                    <option value="Phone">iPhone</option>
                                    <option value="AirPods">AirPods</option>
                                    <option value="Watch">Apple Watch</option>
                                    <option value="Case">Protective Case</option>
                                    <option value="Charger">Charging Adapter/Cable</option>
                                    <option value="Powerbank">Power Bank</option>
                                    <option value="Screen Protector">Screen Protector</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">Compatible Model</label>
                                <input type="text" name="model" id="prod_model" class="form-control form-control-custom" placeholder="e.g. iPhone 15">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">Condition Grade</label>
                                <select name="grade" id="prod_grade" class="form-select form-control-custom" required>
                                    <option value="New">New / Sealed</option>
                                    <option value="Like New">Like New (99%)</option>
                                    <option value="Excellent" selected>Excellent (Grade A)</option>
                                    <option value="Very Good">Very Good (Grade B)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label-custom">Price (ZAR) <span class="text-danger">*</span></label>
                                <input type="number" name="price" id="prod_price" class="form-control form-control-custom" min="0" step="10" placeholder="Price" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">Slash Original Price (Optional)</label>
                                <input type="number" name="original_price" id="prod_orig_price" class="form-control form-control-custom" min="0" step="10" placeholder="Slash Original Price">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">Stock Units Available</label>
                                <input type="number" name="stock" id="prod_stock" class="form-control form-control-custom" min="0" value="1" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label-custom">Storage size</label>
                                <select name="storage" id="prod_storage" class="form-select form-control-custom">
                                    <option value="">None (Accessories)</option>
                                    <option value="64GB">64 GB</option>
                                    <option value="128GB" selected>128 GB</option>
                                    <option value="256GB">256 GB</option>
                                    <option value="512GB">512 GB</option>
                                    <option value="1TB">1 TB</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">Color</label>
                                <input type="text" name="color" id="prod_color" class="form-control form-control-custom" placeholder="e.g. Gold">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">Battery Health (%)</label>
                                <input type="number" name="battery_health" id="prod_battery" class="form-control form-control-custom" min="0" max="100" placeholder="e.g. 90">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">Warranty terms</label>
                                <input type="text" name="warranty" id="prod_warranty" class="form-control form-control-custom" value="6-Month Warranty" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-custom">Description & Specific Details</label>
                            <textarea name="description" id="prod_desc" class="form-control form-control-custom" rows="3" placeholder="Specify device conditions or accessory specs..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label-custom">Product Photo Image Upload</label>
                            <input type="file" name="product_image" class="form-control form-control-custom" accept="image/*">
                        </div>

                        <div class="row g-3 mb-2">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="prod_featured" value="1">
                                    <label class="form-check-label text-white small" for="prod_featured">
                                        Feature on Homepage
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_flash_sale" id="prod_flash" value="1">
                                    <label class="form-check-label text-white small" for="prod_flash">
                                        Put on Flash Sale Promo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary border-opacity-25">
                        <button type="button" class="btn btn-outline-gold px-4 py-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="save_product" class="btn btn-gold px-4 py-2">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Modal input assignments
        function openAddModal() {
            document.getElementById('modalTitle').textContent = "Add New Product";
            document.getElementById('prod_id').value = "";
            document.getElementById('existing_image').value = "";
            document.getElementById('prod_name').value = "";
            document.getElementById('prod_type').value = "iphone";
            document.getElementById('prod_category').value = "Phone";
            document.getElementById('prod_model').value = "";
            document.getElementById('prod_grade').value = "Excellent";
            document.getElementById('prod_price').value = "";
            document.getElementById('prod_orig_price').value = "";
            document.getElementById('prod_stock').value = "1";
            document.getElementById('prod_storage').value = "128GB";
            document.getElementById('prod_color').value = "";
            document.getElementById('prod_battery').value = "90";
            document.getElementById('prod_warranty').value = "6-Month Warranty";
            document.getElementById('prod_desc').value = "";
            document.getElementById('prod_featured').checked = false;
            document.getElementById('prod_flash').checked = false;
        }

        function openEditModal(prod) {
            document.getElementById('modalTitle').textContent = "Edit Product Details";
            document.getElementById('prod_id').value = prod.id;
            document.getElementById('existing_image').value = prod.image_url;
            document.getElementById('prod_name').value = prod.name;
            document.getElementById('prod_type').value = prod.type;
            document.getElementById('prod_category').value = prod.category;
            document.getElementById('prod_model').value = prod.model || "";
            document.getElementById('prod_grade').value = prod.grade;
            document.getElementById('prod_price').value = prod.price;
            document.getElementById('prod_orig_price').value = prod.original_price || "";
            document.getElementById('prod_stock').value = prod.stock;
            document.getElementById('prod_storage').value = prod.storage || "";
            document.getElementById('prod_color').value = prod.color || "";
            document.getElementById('prod_battery').value = prod.battery_health || "";
            document.getElementById('prod_warranty').value = prod.warranty || "6-Month Warranty";
            document.getElementById('prod_desc').value = prod.description || "";
            document.getElementById('prod_featured').checked = (prod.is_featured == 1);
            document.getElementById('prod_flash').checked = (prod.is_flash_sale == 1);
        }
    </script>
</body>
</html>
