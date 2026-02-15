// Document ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // Sidebar toggle for mobile
    const sidebarToggle = document.querySelector('[data-bs-toggle="sidebar"]');
    if(sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
    }
    
    // Auto calculate date difference for reservation
    const checkInInputs = document.querySelectorAll('input[name="check_in"]');
    const checkOutInputs = document.querySelectorAll('input[name="check_out"]');
    
    checkInInputs.forEach(input => {
        input.addEventListener('change', function() {
            const checkOutInput = this.closest('form').querySelector('input[name="check_out"]');
            if(checkOutInput) {
                const minDate = new Date(this.value);
                minDate.setDate(minDate.getDate() + 1);
                checkOutInput.min = minDate.toISOString().split('T')[0];
                
                // If check-out is before new min date, reset it
                if(checkOutInput.value && new Date(checkOutInput.value) < minDate) {
                    checkOutInput.value = '';
                }
            }
        });
    });
    
    // Image preview for file inputs
    const fileInputs = document.querySelectorAll('input[type="file"][accept="image/*"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const preview = this.nextElementSibling;
            if(this.files && this.files[0] && preview && preview.classList.contains('image-preview')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if(targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if(targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Back to top button
    const backToTopButton = document.querySelector('.back-to-top');
    if(backToTopButton) {
        window.addEventListener('scroll', function() {
            if(window.pageYOffset > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });
        
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Countdown timer (example for booking confirmation)
    const countdownElements = document.querySelectorAll('.countdown');
    countdownElements.forEach(element => {
        const endTime = new Date(element.getAttribute('data-end-time')).getTime();
        
        const timer = setInterval(function() {
            const now = new Date().getTime();
            const distance = endTime - now;
            
            if(distance < 0) {
                clearInterval(timer);
                element.innerHTML = 'Waktu habis!';
                return;
            }
            
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            element.innerHTML = `${hours}h ${minutes}m ${seconds}s`;
        }, 1000);
    });
    
    // Password toggle visibility
    const passwordToggles = document.querySelectorAll('.password-toggle');
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if(input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
    
    // Toast notifications
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    const toastList = toastElList.map(function(toastEl) {
        return new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 5000
        });
    });
    
    toastList.forEach(toast => toast.show());
    
    // Dark mode toggle (example)
    const darkModeToggle = document.querySelector('.dark-mode-toggle');
    if(darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
        });
        
        // Check for saved user preference
        if(localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }
    }
    
    // Initialize datepickers
    const datepickers = document.querySelectorAll('.datepicker');
    datepickers.forEach(input => {
        // This would be replaced with actual datepicker initialization
        // For example, if using a library like flatpickr:
        // flatpickr(input, {});
        input.addEventListener('focus', function() {
            this.type = 'date';
        });
        
        input.addEventListener('blur', function() {
            if(!this.value) {
                this.type = 'text';
            }
        });
    });
    
    // Dynamic form fields (example for adding multiple guests)
    const addGuestButton = document.querySelector('.add-guest');
    if(addGuestButton) {
        addGuestButton.addEventListener('click', function() {
            const guestContainer = document.querySelector('.guest-container');
            const guestCount = document.querySelectorAll('.guest-form').length;
            const newGuestForm = document.querySelector('.guest-form').cloneNode(true);
            
            // Reset values
            newGuestForm.querySelectorAll('input').forEach(input => {
                input.value = '';
                input.name = input.name.replace('[0]', `[${guestCount}]`);
            });
            
            // Add remove button
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn btn-sm btn-danger remove-guest mt-2';
            removeButton.innerHTML = '<i class="fas fa-times me-1"></i> Hapus Tamu';
            removeButton.addEventListener('click', function() {
                this.closest('.guest-form').remove();
            });
            
            newGuestForm.appendChild(removeButton);
            guestContainer.appendChild(newGuestForm);
        });
    }
    
    // Handle remove guest buttons
    document.addEventListener('click', function(e) {
        if(e.target.classList.contains('remove-guest')) {
            e.target.closest('.guest-form').remove();
        }
    });
    
    // Custom file input styling
    const customFileInputs = document.querySelectorAll('.custom-file-input');
    customFileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'Pilih file';
            this.nextElementSibling.textContent = fileName;
        });
    });
    
    // Tab remember functionality
    const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"], [data-bs-toggle="pill"]');
    tabLinks.forEach(link => {
        link.addEventListener('click', function() {
            localStorage.setItem('activeTab', this.getAttribute('href'));
        });
    });
    
    const activeTab = localStorage.getItem('activeTab');
    if(activeTab) {
        const tab = document.querySelector(`[href="${activeTab}"]`);
        if(tab) {
            new bootstrap.Tab(tab).show();
        }
    }
    
    // Auto-resize textareas
    const autoResizeTextareas = document.querySelectorAll('.auto-resize');
    autoResizeTextareas.forEach(textarea => {
        function resize() {
            textarea.style.height = 'auto';
            textarea.style.height = (textarea.scrollHeight) + 'px';
        }
        
        textarea.addEventListener('input', resize);
        resize(); // Initial resize
    });
});

// Helper function for debouncing
function debounce(func, wait, immediate) {
    let timeout;
    return function() {
        const context = this, args = arguments;
        const later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

// Helper function for throttling
function throttle(func, limit) {
    let lastFunc;
    let lastRan;
    return function() {
        const context = this;
        const args = arguments;
        if (!lastRan) {
            func.apply(context, args);
            lastRan = Date.now();
        } else {
            clearTimeout(lastFunc);
            lastFunc = setTimeout(function() {
                if ((Date.now() - lastRan) >= limit) {
                    func.apply(context, args);
                    lastRan = Date.now();
                }
            }, limit - (Date.now() - lastRan));
        }
    }
}

// Initialize any lazy loading images
if('IntersectionObserver' in window) {
    const lazyImageObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(function(entry) {
            if(entry.isIntersecting) {
                const lazyImage = entry.target;
                lazyImage.src = lazyImage.dataset.src;
                lazyImage.classList.remove('lazy');
                lazyImageObserver.unobserve(lazyImage);
            }
        });
    });
    
    const lazyImages = document.querySelectorAll('img.lazy');
    lazyImages.forEach(function(lazyImage) {
        lazyImageObserver.observe(lazyImage);
    });
}

// Add animation class when element is in viewport
function animateOnScroll() {
    const elements = document.querySelectorAll('.animate-on-scroll');
    elements.forEach(element => {
        const elementPosition = element.getBoundingClientRect().top;
        const screenPosition = window.innerHeight / 1.2;
        
        if(elementPosition < screenPosition) {
            element.classList.add('animate__animated', 'animate__fadeInUp');
        }
    });
}

window.addEventListener('scroll', debounce(animateOnScroll, 10));