// File input label update
document.addEventListener('DOMContentLoaded', function() {
    // Update custom file input labels
    const fileInputs = document.querySelectorAll('.custom-file-input');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            let fileName = this.files[0]?.name || 'Choose file';
            this.nextElementSibling.textContent = fileName;
        });
    });
    
    // Camera functionality for mobile devices
    const studentPhotoInput = document.getElementById('student_photo');
    if (studentPhotoInput) {
        const cameraContainer = document.querySelector('.camera-container');
        const openCameraBtn = document.getElementById('openCamera');
        const cameraView = document.getElementById('cameraView');
        const capturePhotoBtn = document.getElementById('capturePhoto');
        const photoCanvas = document.getElementById('photoCanvas');
        
        // Show camera option only on mobile devices
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            if (cameraContainer) cameraContainer.classList.remove('d-none');
            
            // Hide file input on mobile
            if (studentPhotoInput.parentElement) {
                studentPhotoInput.parentElement.classList.add('d-none');
            }
        }
        
        let stream = null;
        
        // Open camera
        if (openCameraBtn) {
            openCameraBtn.addEventListener('click', function() {
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    navigator.mediaDevices.getUserMedia({ video: true })
                        .then(function(mediaStream) {
                            stream = mediaStream;
                            if (cameraView) {
                                cameraView.srcObject = mediaStream;
                                cameraView.classList.remove('d-none');
                            }
                            if (capturePhotoBtn) capturePhotoBtn.classList.remove('d-none');
                            if (openCameraBtn) openCameraBtn.classList.add('d-none');
                        })
                        .catch(function(error) {
                            console.error('Camera error:', error);
                            alert('Unable to access camera: ' + error.message);
                        });
                } else {
                    alert('Your browser does not support camera access');
                }
            });
        }
        
        // Capture photo
        if (capturePhotoBtn) {
            capturePhotoBtn.addEventListener('click', function() {
                if (cameraView && photoCanvas) {
                    const context = photoCanvas.getContext('2d');
                    photoCanvas.width = cameraView.videoWidth;
                    photoCanvas.height = cameraView.videoHeight;
                    context.drawImage(cameraView, 0, 0, photoCanvas.width, photoCanvas.height);
                    
                    // Convert canvas to blob and create file
                    photoCanvas.toBlob(function(blob) {
                        const file = new File([blob], 'student_photo.jpg', { type: 'image/jpeg' });
                        
                        // Create a new FileList-like object
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        
                        // Assign the file to the file input
                        studentPhotoInput.files = dataTransfer.files;
                        
                        // Update the label
                        if (studentPhotoInput.nextElementSibling) {
                            studentPhotoInput.nextElementSibling.textContent = 'student_photo.jpg';
                        }
                        
                        // Stop camera
                        if (stream) {
                            stream.getTracks().forEach(track => track.stop());
                        }
                        
                        // Hide camera elements
                        if (cameraView) cameraView.classList.add('d-none');
                        if (capturePhotoBtn) capturePhotoBtn.classList.add('d-none');
                        if (openCameraBtn) openCameraBtn.classList.remove('d-none');
                    }, 'image/jpeg', 0.8);
                }
            });
        }
    }
    
    // Initialize DataTables if available
    if (typeof $.fn.DataTable !== 'undefined' && $('#applicationsTable').length) {
        $('#applicationsTable').DataTable({
            responsive: true,
            ordering: true,
            searching: true,
            language: {
                paginate: {
                    previous: '<i class="fas fa-chevron-left"></i>',
                    next: '<i class="fas fa-chevron-right"></i>'
                }
            }
        });
    }
    
    // Sidebar toggle functionality
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebarToggle && sidebar && mainContent) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            mainContent.classList.toggle('show');
            
            if (sidebar.classList.contains('show')) {
                sidebar.style.width = '250px';
                mainContent.style.marginLeft = '250px';
                sidebar.querySelectorAll('.nav-link span').forEach(span => {
                    span.style.display = 'inline';
                });
            } else {
                sidebar.style.width = '80px';
                mainContent.style.marginLeft = '80px';
                sidebar.querySelectorAll('.nav-link span').forEach(span => {
                    span.style.display = 'none';
                });
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth < 576) {
                sidebar.style.width = '0';
                mainContent.style.marginLeft = '0';
                sidebarToggle.style.display = 'block';
            } else if (window.innerWidth < 768) {
                sidebar.style.width = '80px';
                mainContent.style.marginLeft = '80px';
                sidebar.querySelectorAll('.nav-link span').forEach(span => {
                    span.style.display = 'none';
                });
                sidebarToggle.style.display = 'none';
            } else {
                sidebar.style.width = '250px';
                mainContent.style.marginLeft = '250px';
                sidebar.querySelectorAll('.nav-link span').forEach(span => {
                    span.style.display = 'inline';
                });
                sidebarToggle.style.display = 'none';
            }
        });
        
        // Trigger resize on load to set correct initial state
        window.dispatchEvent(new Event('resize'));
    }
    
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
    
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const passwordStrength = document.getElementById('passwordStrength');
    
    if (passwordInput && passwordStrength) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let message = '';
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[A-Z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            if (password.match(/[!@#$%^&*(),.?":{}|<>]+/)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    message = 'Very Weak';
                    passwordStrength.className = 'password-strength very-weak';
                    break;
                case 2:
                    message = 'Weak';
                    passwordStrength.className = 'password-strength weak';
                    break;
                case 3:
                    message = 'Medium';
                    passwordStrength.className = 'password-strength medium';
                    break;
                case 4:
                    message = 'Strong';
                    passwordStrength.className = 'password-strength strong';
                    break;
                case 5:
                    message = 'Very Strong';
                    passwordStrength.className = 'password-strength very-strong';
                    break;
            }
            
            passwordStrength.textContent = message;
            passwordStrength.style.display = 'block';
        });
    }
    
    // Confirm password match
    if (passwordInput && confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            if (passwordInput.value !== this.value) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    // Image preview for file inputs
    const imageInputs = document.querySelectorAll('input[type="file"][accept^="image"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file && file.type.match('image.*')) {
                const reader = new FileReader();
                const previewId = this.id + 'Preview';
                const preview = document.getElementById(previewId);
                
                if (preview) {
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
    });
});

// Confirm logout
function confirmLogout() {
    return confirm('Are you sure you want to logout?');
}

// Confirm delete action
function confirmDelete() {
    return confirm('Are you sure you want to delete this item? This action cannot be undone.');
}

// Confirm approval/rejection
function confirmAction(action) {
    return confirm(`Are you sure you want to ${action} this application?`);
}

// Form validation
function validateForm() {
    const form = document.getElementById('admissionForm');
    if (!form) return true;
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return false;
    }
    
    // File size validation
    const fileInputs = form.querySelectorAll('input[type="file"]');
    for (let input of fileInputs) {
        if (input.files.length > 0) {
            const file = input.files[0];
            let maxSize = 200 * 1024; // Default 200KB
            
            if (input.accept.includes('.jpg,.jpeg')) {
                maxSize = (input.id === 'student_photo') ? 50 * 1024 : 200 * 1024;
            }
            
            if (file.size > maxSize) {
                alert(`File ${file.name} exceeds maximum allowed size of ${maxSize/1024}KB`);
                return false;
            }
        }
    }
    
    return true;
}

// Toggle password visibility
function togglePasswordVisibility(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (passwordInput && icon) {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}

// Show loading spinner
function showLoading() {
    const loadingOverlay = document.createElement('div');
    loadingOverlay.id = 'loadingOverlay';
    loadingOverlay.style.position = 'fixed';
    loadingOverlay.style.top = '0';
    loadingOverlay.style.left = '0';
    loadingOverlay.style.width = '100%';
    loadingOverlay.style.height = '100%';
    loadingOverlay.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
    loadingOverlay.style.display = 'flex';
    loadingOverlay.style.justifyContent = 'center';
    loadingOverlay.style.alignItems = 'center';
    loadingOverlay.style.zIndex = '9999';
    
    const spinner = document.createElement('div');
    spinner.className = 'spinner-border text-primary';
    spinner.style.width = '3rem';
    spinner.style.height = '3rem';
    spinner.setAttribute('role', 'status');
    
    const srOnly = document.createElement('span');
    srOnly.className = 'visually-hidden';
    srOnly.textContent = 'Loading...';
    
    spinner.appendChild(srOnly);
    loadingOverlay.appendChild(spinner);
    document.body.appendChild(loadingOverlay);
}

// Hide loading spinner
function hideLoading() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.remove();
    }
}

// Show notification toast
function showToast(message, type = 'info') {
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.style.position = 'fixed';
        toastContainer.style.top = '20px';
        toastContainer.style.right = '20px';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Initialize and show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Validate mobile number
function validateMobileNumber(input) {
    const value = input.value.replace(/\D/g, '');
    input.value = value;
    return value.length === 10;
}

// Validate email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Debounce function for search inputs
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Export functions for global access
window.confirmLogout = confirmLogout;
window.confirmDelete = confirmDelete;
window.confirmAction = confirmAction;
window.validateForm = validateForm;
window.togglePasswordVisibility = togglePasswordVisibility;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.showToast = showToast;
window.formatFileSize = formatFileSize;
window.validateMobileNumber = validateMobileNumber;
window.validateEmail = validateEmail;