// Product Management JavaScript

let categoriesData = [];
let brandsData = [];

// Load data when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    loadBrands();
    loadProducts();
    
    // Image preview functionality
    document.getElementById('product_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });
});

// Load all categories
function loadCategories() {
    fetch('../actions/fetch_categories.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                categoriesData = data.data;
                populateCategoryDropdowns();
            } else {
                console.error('Error loading categories:', data.message);
                // Fallback: populate with default categories
                populateCategoryDropdowns();
            }
        })
        .catch(error => {
            console.error('Error loading categories:', error);
            // Fallback: populate with default categories
            populateCategoryDropdowns();
        });
}

// Load all brands
function loadBrands() {
    fetch('../actions/fetch_brand_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                brandsData = data.data;
                populateBrandDropdowns();
            }
        })
        .catch(error => {
            console.error('Error loading brands:', error);
        });
}

// Populate category dropdowns
function populateCategoryDropdowns() {
    const addSelect = document.getElementById('add_product_cat');
    const editSelect = document.getElementById('edit_product_cat');
    
    // Clear existing options except first
    addSelect.innerHTML = '<option value="">Select Category</option>';
    editSelect.innerHTML = '<option value="">Select Category</option>';
    
    // If we have categories data, use it
    if (categoriesData && categoriesData.length > 0) {
        categoriesData.forEach(cat => {
            const option1 = document.createElement('option');
            option1.value = cat.cat_id;
            option1.textContent = cat.cat_name;
            addSelect.appendChild(option1);
            
            const option2 = document.createElement('option');
            option2.value = cat.cat_id;
            option2.textContent = cat.cat_name;
            editSelect.appendChild(option2);
        });
    } else {
        // Fallback: Add default categories from schema
        const defaultCategories = [
            {id: 1, name: 'West African'},
            {id: 2, name: 'East African'},
            {id: 3, name: 'North African'},
            {id: 4, name: 'Southern African'},
            {id: 5, name: 'Central African'},
            {id: 6, name: 'Fine Dining'},
            {id: 7, name: 'Casual Dining'},
            {id: 8, name: 'Street Food/Quick Bite'},
            {id: 9, name: 'Catering/Takeaway'}
        ];
        
        defaultCategories.forEach(cat => {
            const option1 = document.createElement('option');
            option1.value = cat.id;
            option1.textContent = cat.name;
            addSelect.appendChild(option1);
            
            const option2 = document.createElement('option');
            option2.value = cat.id;
            option2.textContent = cat.name;
            editSelect.appendChild(option2);
        });
    }
}

// Populate brand dropdowns
function populateBrandDropdowns() {
    const addSelect = document.getElementById('add_product_brand');
    const editSelect = document.getElementById('edit_product_brand');
    
    // Clear existing options except first
    addSelect.innerHTML = '<option value="">Select Brand</option>';
    editSelect.innerHTML = '<option value="">Select Brand</option>';
    
    if (brandsData && brandsData.length > 0) {
        brandsData.forEach(brand => {
            const option1 = document.createElement('option');
            option1.value = brand.brand_id;
            option1.textContent = brand.brand_name;
            addSelect.appendChild(option1);
            
            const option2 = document.createElement('option');
            option2.value = brand.brand_id;
            option2.textContent = brand.brand_name;
            editSelect.appendChild(option2);
        });
    }
}

// Load all products
function loadProducts() {
    const loadingMessage = document.getElementById('loadingMessage');
    const productTable = document.getElementById('productTable');
    const noData = document.getElementById('noData');
    
    loadingMessage.style.display = 'block';
    productTable.style.display = 'none';
    noData.style.display = 'none';
    
    // Create a simple fetch action for products
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '../actions/fetch_product_action.php', true);
    xhr.onload = function() {
        loadingMessage.style.display = 'none';
        
        if (xhr.status === 200) {
            try {
                const data = JSON.parse(xhr.responseText);
                if (data.success && data.data && data.data.length > 0) {
                    displayProducts(data.data);
                    productTable.style.display = 'table';
                } else {
                    noData.style.display = 'block';
                }
            } catch (e) {
                showAlert('Error parsing product data', 'error');
                noData.style.display = 'block';
            }
        } else {
            showAlert('Error loading products', 'error');
            noData.style.display = 'block';
        }
    };
    xhr.onerror = function() {
        loadingMessage.style.display = 'none';
        showAlert('Network error loading products', 'error');
        noData.style.display = 'block';
    };
    xhr.send();
}

// Display products in table
function displayProducts(products) {
    const tbody = document.getElementById('productTableBody');
    tbody.innerHTML = '';
    
    products.forEach(product => {
        const row = document.createElement('tr');
        
        // Image cell
        let imageHtml = '<img src="../assets/no-image.png" class="product-image" alt="No image">';
        if (product.product_image && product.product_image !== '') {
            imageHtml = `<img src="../${escapeHtml(product.product_image)}" class="product-image" alt="${escapeHtml(product.product_title)}">`;
        }
        
        row.innerHTML = `
            <td>${product.product_id}</td>
            <td>${imageHtml}</td>
            <td>${escapeHtml(product.product_title)}</td>
            <td>${escapeHtml(product.cat_name || 'N/A')}</td>
            <td>${escapeHtml(product.brand_name || 'N/A')}</td>
            <td>$${parseFloat(product.product_price).toFixed(2)}</td>
            <td class="action-buttons">
                <button class="btn btn-primary btn-sm" onclick='openEditModal(${JSON.stringify(product)})'>Edit</button>
                <button class="btn btn-warning btn-sm" onclick="openUploadModal(${product.product_id})">Image</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Open Add Modal
function openAddModal() {
    document.getElementById('addModal').style.display = 'block';
    document.getElementById('addProductForm').reset();
}

// Close Add Modal
function closeAddModal() {
    document.getElementById('addModal').style.display = 'none';
    document.getElementById('addProductForm').reset();
}

// Open Edit Modal
function openEditModal(product) {
    document.getElementById('edit_product_id').value = product.product_id;
    document.getElementById('edit_product_title').value = product.product_title;
    document.getElementById('edit_product_cat').value = product.product_cat;
    document.getElementById('edit_product_brand').value = product.product_brand;
    document.getElementById('edit_product_price').value = product.product_price;
    document.getElementById('edit_product_desc').value = product.product_desc || '';
    document.getElementById('edit_product_keywords').value = product.product_keywords || '';
    document.getElementById('editModal').style.display = 'block';
}

// Close Edit Modal
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    document.getElementById('editProductForm').reset();
}

// Open Upload Modal
function openUploadModal(productId) {
    document.getElementById('upload_product_id').value = productId;
    document.getElementById('uploadImageForm').reset();
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('uploadModal').style.display = 'block';
}

// Close Upload Modal
function closeUploadModal() {
    document.getElementById('uploadModal').style.display = 'none';
    document.getElementById('uploadImageForm').reset();
    document.getElementById('imagePreview').style.display = 'none';
}

// Handle Add Product Form Submission
document.getElementById('addProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Client-side validation
    const title = formData.get('product_title').trim();
    const price = parseFloat(formData.get('product_price'));
    
    if (!title || title.length < 3) {
        showAlert('Product title must be at least 3 characters long', 'error');
        return;
    }
    
    if (price < 0) {
        showAlert('Price must be a positive number', 'error');
        return;
    }
    
    // Submit via AJAX
    fetch('../actions/add_product_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            closeAddModal();
            loadProducts();
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Error adding product: ' + error.message, 'error');
        console.error('Error:', error);
    });
});

// Handle Edit Product Form Submission
document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Client-side validation
    const title = formData.get('product_title').trim();
    const price = parseFloat(formData.get('product_price'));
    
    if (!title || title.length < 3) {
        showAlert('Product title must be at least 3 characters long', 'error');
        return;
    }
    
    if (price < 0) {
        showAlert('Price must be a positive number', 'error');
        return;
    }
    
    // Submit via AJAX
    fetch('../actions/update_product_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            closeEditModal();
            loadProducts();
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Error updating product: ' + error.message, 'error');
        console.error('Error:', error);
    });
});

// Handle Upload Image Form Submission
document.getElementById('uploadImageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const fileInput = document.getElementById('product_image');
    const file = fileInput.files[0];
    
    // Client-side validation
    if (!file) {
        showAlert('Please select an image file', 'error');
        return;
    }
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        showAlert('File size must not exceed 5MB', 'error');
        return;
    }
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showAlert('Only JPEG, PNG, GIF, and WebP images are allowed', 'error');
        return;
    }
    
    // Submit via AJAX
    fetch('../actions/upload_product_image_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            closeUploadModal();
            loadProducts();
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Error uploading image: ' + error.message, 'error');
        console.error('Error:', error);
    });
});

// Show Alert Message
function showAlert(message, type) {
    const alertBox = document.getElementById('alertBox');
    alertBox.textContent = message;
    alertBox.className = 'alert';
    
    if (type === 'success') {
        alertBox.classList.add('alert-success');
    } else {
        alertBox.classList.add('alert-error');
    }
    
    alertBox.style.display = 'block';
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        alertBox.style.display = 'none';
    }, 5000);
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close modals when clicking outside
window.onclick = function(event) {
    const addModal = document.getElementById('addModal');
    const editModal = document.getElementById('editModal');
    const uploadModal = document.getElementById('uploadModal');
    
    if (event.target === addModal) {
        closeAddModal();
    }
    if (event.target === editModal) {
        closeEditModal();
    }
    if (event.target === uploadModal) {
        closeUploadModal();
    }
}