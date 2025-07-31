/**
 * Animations JavaScript
 * SMP Bina Informatika - Efek Visual dan Animasi
 */

class Animations {
    constructor() {
        this.init();
    }

    init() {
        this.setupScrollAnimations();
        this.setupHoverEffects();
        this.setupParallaxEffects();
        this.setupTypingEffect();
        this.setupCounterAnimation();
        this.setupLoadingAnimations();
    }

    setupScrollAnimations() {
        // Intersection Observer untuk animasi scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateElement(entry.target);
                }
            });
        }, observerOptions);

        // Observe elements untuk animasi
        const animatedElements = document.querySelectorAll(
            '.procedure-card, .gallery-item, .contact-info, .section-title, .hero-content'
        );

        animatedElements.forEach(el => {
            observer.observe(el);
        });
    }

    animateElement(element) {
        // Tambahkan class animasi berdasarkan tipe elemen
        if (element.classList.contains('procedure-card')) {
            element.classList.add('animate-slide-in-left');
        } else if (element.classList.contains('gallery-item')) {
            element.classList.add('animate-fade-in-up');
        } else if (element.classList.contains('section-title')) {
            element.classList.add('animate-fade-in-up');
        } else if (element.classList.contains('hero-content')) {
            element.classList.add('fade-in');
        } else {
            element.classList.add('animate-fade-in-up');
        }

        // Hapus observer setelah animasi
        setTimeout(() => {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, 100);
    }

    setupHoverEffects() {
        // Hover effect untuk procedure cards
        const procedureCards = document.querySelectorAll('.procedure-card');
        procedureCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                this.addHoverEffect(card);
            });

            card.addEventListener('mouseleave', () => {
                this.removeHoverEffect(card);
            });
        });

        // Hover effect untuk gallery items
        const galleryItems = document.querySelectorAll('.gallery-item');
        galleryItems.forEach(item => {
            item.addEventListener('mouseenter', () => {
                this.addGalleryHoverEffect(item);
            });

            item.addEventListener('mouseleave', () => {
                this.removeGalleryHoverEffect(item);
            });
        });

        // Hover effect untuk buttons
        const buttons = document.querySelectorAll('.btn-primary-custom, .btn-secondary-custom, .btn-submit');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', () => {
                this.addButtonHoverEffect(button);
            });

            button.addEventListener('mouseleave', () => {
                this.removeButtonHoverEffect(button);
            });
        });
    }

    addHoverEffect(element) {
        element.style.transform = 'translateY(-10px) scale(1.02)';
        element.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
    }

    removeHoverEffect(element) {
        element.style.transform = 'translateY(0) scale(1)';
        element.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.1)';
    }

    addGalleryHoverEffect(element) {
        const img = element.querySelector('img');
        const overlay = element.querySelector('.gallery-overlay');
        
        if (img) img.style.transform = 'scale(1.1)';
        if (overlay) overlay.style.transform = 'translateY(0)';
    }

    removeGalleryHoverEffect(element) {
        const img = element.querySelector('img');
        const overlay = element.querySelector('.gallery-overlay');
        
        if (img) img.style.transform = 'scale(1)';
        if (overlay) overlay.style.transform = 'translateY(100%)';
    }

    addButtonHoverEffect(button) {
        button.style.transform = 'translateY(-3px)';
        button.style.boxShadow = '0 8px 25px rgba(76, 175, 80, 0.4)';
    }

    removeButtonHoverEffect(button) {
        button.style.transform = 'translateY(0)';
        button.style.boxShadow = '0 5px 15px rgba(76, 175, 80, 0.3)';
    }

    setupParallaxEffects() {
        // Parallax effect untuk hero section
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const heroSection = document.querySelector('.hero-section');
            
            if (heroSection) {
                const rate = scrolled * -0.5;
                heroSection.style.transform = `translateY(${rate}px)`;
            }

            // Parallax untuk background elements
            const parallaxElements = document.querySelectorAll('.parallax-bg');
            parallaxElements.forEach(element => {
                const speed = element.dataset.speed || 0.5;
                const yPos = -(scrolled * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
        });
    }

    setupTypingEffect() {
        // Typing effect untuk hero title
        const heroTitle = document.querySelector('.hero-title');
        if (heroTitle && !heroTitle.dataset.typed) {
            this.typeWriter(heroTitle, heroTitle.textContent, 100);
            heroTitle.dataset.typed = 'true';
        }
    }

    typeWriter(element, text, speed) {
        element.textContent = '';
        let i = 0;
        
        function type() {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                i++;
                setTimeout(type, speed);
            }
        }
        
        type();
    }

    setupCounterAnimation() {
        // Counter animation untuk statistik
        const counters = document.querySelectorAll('.counter');
        const observerOptions = {
            threshold: 0.5
        };

        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateCounter(entry.target);
                }
            });
        }, observerOptions);

        counters.forEach(counter => {
            counterObserver.observe(counter);
        });
    }

    animateCounter(element) {
        const target = parseInt(element.dataset.target);
        const duration = 2000; // 2 seconds
        const step = target / (duration / 16); // 60fps
        let current = 0;

        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current);
        }, 16);
    }

    setupLoadingAnimations() {
        // Loading animation untuk form submission
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', () => {
                this.showFormLoading(form);
            });
        });

        // Page loading animation
        window.addEventListener('load', () => {
            this.hidePageLoader();
        });
    }

    showFormLoading(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Memproses...';
            submitBtn.disabled = true;
            
            // Simpan original text untuk restore nanti
            submitBtn.dataset.originalText = originalText;
        }
    }

    hideFormLoading(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn && submitBtn.dataset.originalText) {
            submitBtn.textContent = submitBtn.dataset.originalText;
            submitBtn.disabled = false;
        }
    }

    hidePageLoader() {
        const loader = document.getElementById('pageLoader');
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        }
    }

    // Utility functions
    addRippleEffect(element) {
        element.addEventListener('click', (e) => {
            const ripple = document.createElement('span');
            const rect = element.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            element.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    }

    addFloatingEffect(element) {
        let direction = 1;
        let position = 0;
        
        setInterval(() => {
            position += direction * 0.5;
            if (position > 10 || position < -10) {
                direction *= -1;
            }
            element.style.transform = `translateY(${position}px)`;
        }, 50);
    }

    addPulseEffect(element) {
        element.classList.add('pulse');
        setTimeout(() => {
            element.classList.remove('pulse');
        }, 1000);
    }

    // Smooth reveal animation
    revealOnScroll() {
        const reveals = document.querySelectorAll('.reveal');
        
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                }
            });
        }, { threshold: 0.1 });

        reveals.forEach(reveal => {
            revealObserver.observe(reveal);
        });
    }

    // Stagger animation untuk multiple elements
    staggerAnimation(elements, delay = 100) {
        elements.forEach((element, index) => {
            setTimeout(() => {
                element.classList.add('animate-fade-in-up');
            }, index * delay);
        });
    }

    // Bounce animation
    addBounceEffect(element) {
        element.addEventListener('click', () => {
            element.classList.add('bounce');
            setTimeout(() => {
                element.classList.remove('bounce');
            }, 600);
        });
    }

    // Shake animation untuk error states
    addShakeEffect(element) {
        element.classList.add('shake');
        setTimeout(() => {
            element.classList.remove('shake');
        }, 500);
    }

    // Fade in animation
    fadeIn(element, duration = 500) {
        element.style.opacity = '0';
        element.style.display = 'block';
        
        let start = null;
        const animate = (timestamp) => {
            if (!start) start = timestamp;
            const progress = timestamp - start;
            const opacity = Math.min(progress / duration, 1);
            
            element.style.opacity = opacity;
            
            if (progress < duration) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    }

    // Slide in animation
    slideIn(element, direction = 'left', duration = 500) {
        const startPosition = direction === 'left' ? '-100%' : '100%';
        element.style.transform = `translateX(${startPosition})`;
        element.style.display = 'block';
        
        let start = null;
        const animate = (timestamp) => {
            if (!start) start = timestamp;
            const progress = timestamp - start;
            const percentage = Math.min(progress / duration, 1);
            
            element.style.transform = `translateX(${percentage * 100 - 100}%)`;
            
            if (progress < duration) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    }
}

// Initialize animations when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new Animations();
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Animations;
} 