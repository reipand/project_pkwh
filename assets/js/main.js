/**
 * Main JavaScript File
 * SMP Bina Informatika - Penerimaan Siswa Baru
 */

class SMPBinaInformatika {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeAnimations();
        this.setupFormValidation();
        this.setupCarousel();
        this.setupScrollEffects();
    }

    setupEventListeners() {
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            this.handleNavbarScroll();
        });

        // Form submission
        const registrationForm = document.getElementById('registrationForm');
        if (registrationForm) {
            registrationForm.addEventListener('submit', (e) => {
                this.handleRegistrationSubmit(e);
            });
        }

        // Contact form submission
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', (e) => {
                this.handleContactSubmit(e);
            });
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                this.handleSmoothScroll(e);
            });
        });

        // Back to top button
        const backToTopBtn = document.getElementById('backToTop');
        if (backToTopBtn) {
            backToTopBtn.addEventListener('click', () => {
                this.scrollToTop();
            });
        }
    }

    handleNavbarScroll() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 100) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }

    async handleRegistrationSubmit(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        
        // Show loading
        this.showLoading();
        
        try {
            const response = await fetch('api/register.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showSuccessMessage('Registrasi berhasil!', result.message);
                form.reset();
                
                // Show registration details
                if (result.data) {
                    this.showRegistrationDetails(result.data);
                }
            } else {
                this.showErrorMessage('Registrasi gagal!', result.message);
            }
        } catch (error) {
            this.showErrorMessage('Terjadi kesalahan!', 'Silakan coba lagi nanti.');
            console.error('Registration error:', error);
        } finally {
            this.hideLoading();
        }
    }

    async handleContactSubmit(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        
        this.showLoading();
        
        try {
            const response = await fetch('api/contact.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showSuccessMessage('Pesan terkirim!', 'Terima kasih telah menghubungi kami.');
                form.reset();
            } else {
                this.showErrorMessage('Gagal mengirim pesan!', result.message);
            }
        } catch (error) {
            this.showErrorMessage('Terjadi kesalahan!', 'Silakan coba lagi nanti.');
            console.error('Contact error:', error);
        } finally {
            this.hideLoading();
        }
    }

    handleSmoothScroll(e) {
        e.preventDefault();
        const targetId = e.currentTarget.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
            const offsetTop = targetElement.offsetTop - 80;
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    }

    scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    initializeAnimations() {
        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.querySelectorAll('.procedure-card, .gallery-item, .contact-info').forEach(el => {
            observer.observe(el);
        });
    }

    setupFormValidation() {
        // Real-time validation
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('blur', () => {
                this.validateField(input);
            });
            
            input.addEventListener('input', () => {
                this.clearFieldError(input);
            });
        });
    }

    validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;
        
        // Clear previous errors
        this.clearFieldError(field);
        
        // Validation rules
        const rules = {
            name: {
                required: true,
                minLength: 2,
                pattern: /^[a-zA-Z\s]+$/
            },
            parent_email: {
                required: true,
                pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/
            },
            nik: {
                required: true,
                pattern: /^\d{16}$/
            },
            parent_phone: {
                required: true,
                pattern: /^(\+62|62|0)8[1-9][0-9]{6,9}$/
            },
            password: {
                required: true,
                minLength: 6
            },
            birth_date: {
                required: true,
                pattern: /^\d{4}-\d{2}-\d{2}$/
            }
        };
        
        if (rules[fieldName]) {
            const rule = rules[fieldName];
            
            if (rule.required && !value) {
                this.showFieldError(field, 'Field ini wajib diisi');
                return false;
            }
            
            if (rule.minLength && value.length < rule.minLength) {
                this.showFieldError(field, `Minimal ${rule.minLength} karakter`);
                return false;
            }
            
            if (rule.pattern && !rule.pattern.test(value)) {
                if (fieldName === 'parent_email') {
                    this.showFieldError(field, 'Format email tidak valid');
                } else {
                    this.showFieldError(field, 'Format tidak valid');
                }
                return false;
            }
        }
        
        return true;
    }

    showFieldError(field, message) {
        field.classList.add('error');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }

    clearFieldError(field) {
        field.classList.remove('error');
        const errorDiv = field.parentNode.querySelector('.error-message');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    setupCarousel() {
        const carousel = document.querySelector('.hero-carousel');
        if (!carousel) return;

        let currentSlide = 0;
        const slides = carousel.querySelectorAll('.carousel-item');
        const totalSlides = slides.length;

        // Auto slide
        setInterval(() => {
            currentSlide = (currentSlide + 1) % totalSlides;
            this.showSlide(currentSlide);
        }, 5000);

        // Manual navigation
        const prevBtn = carousel.querySelector('.carousel-prev');
        const nextBtn = carousel.querySelector('.carousel-next');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                this.showSlide(currentSlide);
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                currentSlide = (currentSlide + 1) % totalSlides;
                this.showSlide(currentSlide);
            });
        }
    }

    showSlide(index) {
        const carousel = document.querySelector('.hero-carousel');
        if (!carousel) return;

        const slides = carousel.querySelectorAll('.carousel-item');
        const dots = carousel.querySelectorAll('.carousel-dot');
        
        slides.forEach((slide, i) => {
            slide.style.transform = `translateX(${100 * (i - index)}%)`;
            slide.classList.toggle('active', i === index);
        });
        
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
    }

    setupScrollEffects() {
        // Parallax effect for hero section
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const heroSection = document.querySelector('.hero-section');
            
            if (heroSection) {
                heroSection.style.transform = `translateY(${scrolled * 0.5}px)`;
            }
        });
    }

    showLoading() {
        const spinner = document.getElementById('loadingSpinner');
        if (spinner) {
            spinner.classList.remove('d-none');
        }
    }

    hideLoading() {
        const spinner = document.getElementById('loadingSpinner');
        if (spinner) {
            spinner.classList.add('d-none');
        }
    }

    showSuccessMessage(title, message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: title,
                text: message,
                confirmButtonColor: '#4caf50'
            });
        } else {
            alert(`${title}\n${message}`);
        }
    }

    showErrorMessage(title, message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: title,
                text: message,
                confirmButtonColor: '#dc3545'
            });
        } else {
            alert(`${title}\n${message}`);
        }
    }

    showRegistrationDetails(data) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Registrasi Berhasil!',
                html: `
                    <div class="text-start">
                        <p><strong>Nama:</strong> ${data.name}</p>
                        <p><strong>Username:</strong> ${data.username}</p>
                        <p><strong>Tahun Ajaran:</strong> ${data.academic_year}</p>
                        <p><strong>Kelas:</strong> ${data.class}</p>
                        <hr>
                        <p class="text-warning"><strong>Harap simpan username dan password Anda!</strong></p>
                    </div>
                `,
                confirmButtonColor: '#4caf50',
                confirmButtonText: 'OK'
            });
        }
    }

    // Utility functions
    formatDate(date) {
        return new Date(date).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(amount);
    }

    debounce(func, wait) {
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
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new SMPBinaInformatika();
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SMPBinaInformatika;
} 