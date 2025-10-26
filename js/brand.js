// Brand Management JavaScript

// Load brands when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadBrands();
});

// Load all brands
function loadBrands() {
    const loadingMessage = document.getElementById('loadingMessage');
    const brandTable = document.getElementById('brandTable');
    const noData = document.getElementById('noData');
    
    loadingMessage.style.display = 'block';
    brandTable.style.display = 'none';
    noData.style.display = 'none';
    
    fetch('../actions/fetch_brand_action.php')
        .then(response => response.json())
        .then(data => {
            loadingMessage.style.display = 'none';
            
            if (data.success) {
                if (data.data && data.data.length > 0) {
                    displayBrands(data.data);
                    brandTable.style.display = 'table';
                } else {
                    noData.style.display = 'block';
                }
            } else {
                showAlert(data.message, 'error');
                noData.style.display = 'block';
            }
        })
        .catch(error => {
            loadingMessage.style.display = 'none';
            showAlert('Error loading brands: ' + error.message, 'error');
            console.error('Error:', error);
        });
}

// Display brands in table
function displayBrands(brands) {
    const tbody = document.getElementById('brandTableBody');
    tbody.innerHTML = '';
    
    brands.forEach(brand => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${brand.brand_id}</td>
            <td>${escapeHtml(brand.brand_name)}</td>
            <td class="action-buttons">
                <button class="btn btn-primary btn-sm" onclick="openEditModal(${brand.brand_id}, '${escapeHtml(brand.brand_name)}')">Edit</button>
                <button class="btn btn-danger btn-sm" onclick="deleteBrand(${brand.brand_id}, '${escapeHtml(brand.brand_name)}')">Delete</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Open Add Modal
function openAddModal() {
    document.getElementById('addModal').style.display = 'block';
    document.getElementById('addBrandForm').reset();
}

// Close Add Modal
function closeAddModal() {
    document.getElementById('addModal').style.display = 'none';
    document.getElementById('addBrandForm').reset();
}

// Open Edit Modal
function openEditModal(brandId, brandName) {
    document.getElementById('edit_brand_id').value = brandId;
    document.getElementById('edit_brand_name').value = brandName;
    document.getElementById('editModal').style.display = 'block';
}

// Close Edit Modal
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    document.getElementById('editBrandForm').reset();
}

// Handle Add Brand Form Submission
document.getElementById('addBrandForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const brandName = document.getElementById('add_brand_name').value.trim();
    
    // Client-side validation
    if (!brandName) {
        showAlert('Brand name is required', 'error');
        return;
    }
    
    if (brandName.length < 2) {
        showAlert('Brand name must be at least 2 characters long', 'error');
        return;
    }
    
    if (brandName.length > 100) {
        showAlert('Brand name must not exceed 100 characters', 'error');
        return;
    }
    
    // Prepare form data
    const formData = new FormData();
    formData.append('brand_name', brandName);
    
    // Submit via AJAX
    fetch('../actions/add_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            closeAddModal();
            loadBrands(); // Reload the brands table
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Error adding brand: ' + error.message, 'error');
        console.error('Error:', error);
    });
});

// Handle Edit Brand Form Submission
document.getElementById('editBrandForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const brandId = document.getElementById('edit_brand_id').value;
    const brandName = document.getElementById('edit_brand_name').value.trim();
    
    // Client-side validation
    if (!brandName) {
        showAlert('Brand name is required', 'error');
        return;
    }
    
    if (brandName.length < 2) {
        showAlert('Brand name must be at least 2 characters long', 'error');
        return;
    }
    
    if (brandName.length > 100) {
        showAlert('Brand name must not exceed 100 characters', 'error');
        return;
    }
    
    // Prepare form data
    const formData = new FormData();
    formData.append('brand_id', brandId);
    formData.append('brand_name', brandName);
    
    // Submit via AJAX
    fetch('../actions/update_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            closeEditModal();
            loadBrands(); // Reload the brands table
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Error updating brand: ' + error.message, 'error');
        console.error('Error:', error);
    });
});

// Delete Brand
function deleteBrand(brandId, brandName) {
    if (!confirm(`Are you sure you want to delete the brand "${brandName}"?\n\nThis action cannot be undone.`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('brand_id', brandId);
    
    fetch('../actions/delete_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            loadBrands(); // Reload the brands table
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Error deleting brand: ' + error.message, 'error');
        console.error('Error:', error);
    });
}

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
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close modals when clicking outside
window.onclick = function(event) {
    const addModal = document.getElementById('addModal');
    const editModal = document.getElementById('editModal');
    
    if (event.target === addModal) {
        closeAddModal();
    }
    if (event.target === editModal) {
        closeEditModal();
    }
}