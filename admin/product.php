<?php
require_once(__DIR__ . '/../settings/core.php');

// Check if user is logged in and is admin
if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background-color: #e0a800;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow-y: auto;
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }
        
        .modal-header h2 {
            color: #333;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        input[type="file"] {
            padding: 5px;
        }
        
        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #007bff;
        }
        
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: none;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .product-image {
            max-width: 80px;
            max-height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 4px;
            display: none;
        }
        
        .required {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Product Management</h1>
        
        <div id="alertBox" class="alert"></div>
        
        <button class="btn btn-primary" onclick="openAddModal()">Add New Product</button>
        
        <div id="loadingMessage" class="loading">Loading products...</div>
        
        <table id="productTable" style="display: none;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productTableBody">
                <!-- Products will be loaded here dynamically -->
            </tbody>
        </table>
        
        <div id="noData" class="no-data" style="display: none;">
            No products found. Click "Add New Product" to create one.
        </div>
    </div>
    
    <!-- Add Product Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeAddModal()">&times;</span>
                <h2>Add New Product</h2>
            </div>
            <form id="addProductForm">
                <div class="form-group">
                    <label for="add_product_title">Product Title <span class="required">*</span></label>
                    <input type="text" id="add_product_title" name="product_title" required>
                </div>
                
                <div class="form-group">
                    <label for="add_product_cat">Category <span class="required">*</span></label>
                    <select id="add_product_cat" name="product_cat" required>
                        <option value="">Select Category</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="add_product_brand">Brand <span class="required">*</span></label>
                    <select id="add_product_brand" name="product_brand" required>
                        <option value="">Select Brand</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="add_product_price">Price <span class="required">*</span></label>
                    <input type="number" id="add_product_price" name="product_price" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="add_product_desc">Description</label>
                    <textarea id="add_product_desc" name="product_desc" maxlength="500"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="add_product_keywords">Keywords</label>
                    <input type="text" id="add_product_keywords" name="product_keywords" maxlength="100">
                </div>
                
                <button type="submit" class="btn btn-success">Add Product</button>
                <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
            </form>
        </div>
    </div>
    
    <!-- Edit Product Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeEditModal()">&times;</span>
                <h2>Edit Product</h2>
            </div>
            <form id="editProductForm">
                <input type="hidden" id="edit_product_id" name="product_id">
                
                <div class="form-group">
                    <label for="edit_product_title">Product Title <span class="required">*</span></label>
                    <input type="text" id="edit_product_title" name="product_title" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_product_cat">Category <span class="required">*</span></label>
                    <select id="edit_product_cat" name="product_cat" required>
                        <option value="">Select Category</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_product_brand">Brand <span class="required">*</span></label>
                    <select id="edit_product_brand" name="product_brand" required>
                        <option value="">Select Brand</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_product_price">Price <span class="required">*</span></label>
                    <input type="number" id="edit_product_price" name="product_price" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_product_desc">Description</label>
                    <textarea id="edit_product_desc" name="product_desc" maxlength="500"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_product_keywords">Keywords</label>
                    <input type="text" id="edit_product_keywords" name="product_keywords" maxlength="100">
                </div>
                
                <button type="submit" class="btn btn-success">Update Product</button>
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
            </form>
        </div>
    </div>
    
    <!-- Upload Image Modal -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeUploadModal()">&times;</span>
                <h2>Upload Product Image</h2>
            </div>
            <form id="uploadImageForm">
                <input type="hidden" id="upload_product_id" name="product_id">
                
                <div class="form-group">
                    <label for="product_image">Select Image <span class="required">*</span></label>
                    <input type="file" id="product_image" name="product_image" accept="image/*" required>
                    <small>Allowed: JPEG, PNG, GIF, WebP (Max 5MB)</small>
                </div>
                
                <img id="imagePreview" class="image-preview" alt="Image preview">
                
                <button type="submit" class="btn btn-success">Upload Image</button>
                <button type="button" class="btn btn-secondary" onclick="closeUploadModal()">Cancel</button>
            </form>
        </div>
    </div>
    
    <script src="../js/product.js"></script>
</body>
</html>