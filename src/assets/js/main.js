// ===== MAIN JAVASCRIPT FOR RENTAL SYSTEM =====

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
    });

    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Form validation enhancement - improved to not interfere with valid submissions
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            // Only prevent submission if form is actually invalid
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                form.classList.add('was-validated');
            } else {
                // Form is valid, allow submission
                form.classList.add('was-validated');
                // Don't prevent default - let the form submit normally
            }
        });
    });

    // Password confirmation validation
    const passwordFields = document.querySelectorAll('input[name="new_password"]');
    const confirmFields = document.querySelectorAll('input[name="confirm_password"]');
    
    if (passwordFields.length > 0 && confirmFields.length > 0) {
        confirmFields[0].addEventListener('input', function() {
            const password = passwordFields[0].value;
            const confirm = this.value;
            
            if (password !== confirm) {
                this.setCustomValidity('Password tidak cocok');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // Loading button states - improved to not interfere with form submission
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(function(button) {
        // Store original button text
        button.setAttribute('data-original-text', button.innerHTML);
        
        button.addEventListener('click', function(e) {
            const form = this.closest('form');
            
            // Only add loading state if form is valid and will actually submit
            if (form && form.checkValidity()) {
                const self = this;
                
                // Set loading state after a very small delay to ensure form submission happens first
                setTimeout(() => {
                    if (!self.disabled) { // Only if not already disabled
                        self.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
                        self.disabled = true;
                    }
                }, 50);
                
                // Re-enable after 10 seconds as fallback
                setTimeout(() => {
                    self.disabled = false;
                    self.innerHTML = self.getAttribute('data-original-text') || 'Submit';
                }, 10000);
            }
        });
    });

    // Image lazy loading
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach(function(img) {
        imageObserver.observe(img);
    });

    // Search functionality
    const searchInputs = document.querySelectorAll('input[type="search"], .search-input');
    searchInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const searchTargets = document.querySelectorAll('.searchable');
            
            searchTargets.forEach(function(target) {
                const text = target.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    target.style.display = '';
                } else {
                    target.style.display = 'none';
                }
            });
        });
    });

    // Counter animation
    const counters = document.querySelectorAll('.counter');
    const counterObserver = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.getAttribute('data-target'));
                const duration = 2000; // 2 seconds
                const increment = target / (duration / 16); // 60fps
                let current = 0;
                
                const timer = setInterval(function() {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.floor(current);
                }, 16);
                
                counterObserver.unobserve(counter);
            }
        });
    });

    counters.forEach(function(counter) {
        counterObserver.observe(counter);
    });

    // Dark mode toggle (if implemented)
    const darkModeToggle = document.querySelector('#darkModeToggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
        });

        // Load dark mode preference
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }
    }

    // Back to top button
    const backToTopButton = document.querySelector('#backToTop');
    if (backToTopButton) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.style.display = 'block';
            } else {
                backToTopButton.style.display = 'none';
            }
        });

        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Auto-refresh for dashboard (every 5 minutes)
    if (window.location.pathname.includes('dashboard')) {
        setInterval(function() {
            // Only refresh if user is active (not idle)
            if (document.hasFocus()) {
                const statsElements = document.querySelectorAll('.stat-card [data-stat]');
                // You can implement AJAX refresh here
            }
        }, 300000); // 5 minutes
    }

    // Confirmation dialogs
    const deleteButtons = document.querySelectorAll('.btn-delete, [data-action="delete"]');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            const confirmMessage = this.getAttribute('data-confirm') || 'Apakah Anda yakin ingin menghapus item ini?';
            if (!confirm(confirmMessage)) {
                e.preventDefault();
            }
        });
    });

    // Format currency inputs
    const currencyInputs = document.querySelectorAll('.currency-input');
    currencyInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            let value = this.value.replace(/[^0-9]/g, '');
            if (value) {
                value = parseInt(value).toLocaleString('id-ID');
                this.value = 'Rp ' + value;
            }
        });
    });

    // Phone number formatting
    const phoneInputs = document.querySelectorAll('input[type="tel"], .phone-input');
    phoneInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            let value = this.value;
            
            // Only format if value has sufficient length to avoid auto-prefix on single digits
            if (value.length >= 2) {
                // Remove all non-digit characters except + at the beginning
                value = value.replace(/[^0-9+]/g, '');
                
                // If starts with 0, replace with +62
                if (value.startsWith('0')) {
                    value = '+62' + value.substring(1);
                }
                // If starts with 62 but not +62, add +
                else if (value.startsWith('62') && !value.startsWith('+62')) {
                    value = '+' + value;
                }
                // If it's just numbers and doesn't start with +62, and length > 3, add +62
                else if (/^[0-9]+$/.test(value) && !value.startsWith('62') && value.length > 3) {
                    value = '+62' + value;
                }
            }
            
            this.value = value;
        });
    });
});

// Utility functions
function showToast(message, type = 'info') {
    const toastContainer = document.querySelector('.toast-container') || createToastContainer();
    const toast = createToast(message, type);
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

function createToast(message, type) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.setAttribute('role', 'alert');
    
    const typeColors = {
        'success': 'text-bg-success',
        'error': 'text-bg-danger',
        'warning': 'text-bg-warning',
        'info': 'text-bg-info'
    };
    
    toast.innerHTML = `
        <div class="toast-header ${typeColors[type] || 'text-bg-info'}">
            <strong class="me-auto">Notifikasi</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            ${message}
        </div>
    `;
    
    return toast;
}

// Format number to Indonesian currency
function formatCurrency(number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
}

// Format date to Indonesian format
function formatDate(date) {
    return new Intl.DateTimeFormat('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }).format(new Date(date));
}

// Debounce function for search
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

// Export functions for global use
window.showToast = showToast;
window.formatCurrency = formatCurrency;
window.formatDate = formatDate;
window.debounce = debounce;