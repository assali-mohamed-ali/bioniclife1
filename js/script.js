/**
 * BionicLife - Dynamic Interactions & Animations
 * Modern, smooth, and interactive experience
 */

// ============================================
// 1. SMOOTH SCROLL & NAVBAR EFFECTS
// ============================================

const navbar = document.querySelector('.navbar');
let lastScroll = 0;

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    // Add 'scrolled' class when scrolling
    if (currentScroll > 100) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
    
    // Show/hide scroll-to-top button
    const scrollTop = document.querySelector('.scroll-top');
    if (scrollTop) {
        if (currentScroll > 500) {
            scrollTop.classList.add('visible');
        } else {
            scrollTop.classList.remove('visible');
        }
    }
    
    lastScroll = currentScroll;
});

// ============================================
// 2. SCROLL TO TOP BUTTON
// ============================================

// Create scroll-to-top button if it doesn't exist
if (!document.querySelector('.scroll-top')) {
    const scrollTopBtn = document.createElement('div');
    scrollTopBtn.className = 'scroll-top';
    scrollTopBtn.innerHTML = '↑';
    scrollTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    document.body.appendChild(scrollTopBtn);
}

// ============================================
// 3. INTERSECTION OBSERVER - FADE IN ANIMATIONS
// ============================================

const observerOptions = {
    threshold: 0.15,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, observerOptions);

// Observe all elements that should fade in
document.addEventListener('DOMContentLoaded', () => {
    // Add fade-in class to elements
    const fadeElements = document.querySelectorAll('.product-card, .review-card, .about, .featured-products h2');
    fadeElements.forEach(el => {
        el.classList.add('fade-in');
        observer.observe(el);
    });
});

// ============================================
// 4. PRODUCT CARDS - TILT EFFECT (3D)
// ============================================

document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        const rotateX = (y - centerY) / 20;
        const rotateY = (centerX - x) / 20;
        
        card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-15px) scale(1.02)`;
    });
    
    card.addEventListener('mouseleave', () => {
        card.style.transform = '';
    });
});

// ============================================
// 5. PARALLAX EFFECT ON HERO
// ============================================

const hero = document.querySelector('.hero');
if (hero) {
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallaxSpeed = 0.5;
        hero.style.transform = `translateY(${scrolled * parallaxSpeed}px)`;
    });
}

// ============================================
// 6. SMOOTH NUMBER COUNTER (for stats)
// ============================================

function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    
    const timer = setInterval(() => {
        start += increment;
        if (start >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(start);
        }
    }, 16);
}

// Activate counters when visible
const counterElements = document.querySelectorAll('[data-counter]');
const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
            const target = parseInt(entry.target.dataset.counter);
            animateCounter(entry.target, target);
            entry.target.classList.add('counted');
        }
    });
}, { threshold: 0.5 });

counterElements.forEach(el => counterObserver.observe(el));

// ============================================
// 7. FORM VALIDATION ENHANCED
// ============================================

const forms = document.querySelectorAll('form');
forms.forEach(form => {
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    
    inputs.forEach(input => {
        // Real-time validation
        input.addEventListener('blur', () => {
            validateInput(input);
        });
        
        input.addEventListener('input', () => {
            if (input.classList.contains('error')) {
                validateInput(input);
            }
        });
    });
    
    form.addEventListener('submit', (e) => {
        let isValid = true;
        
        inputs.forEach(input => {
            if (!validateInput(input)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            // Smooth scroll to first error
            const firstError = form.querySelector('.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
});

function validateInput(input) {
    const value = input.value.trim();
    let isValid = true;
    
    // Remove previous error
    const existingError = input.parentElement.querySelector('.error-message');
    if (existingError) existingError.remove();
    input.classList.remove('error');
    
    if (input.hasAttribute('required') && !value) {
        showError(input, 'Ce champ est requis');
        isValid = false;
    } else if (input.type === 'email' && value && !isValidEmail(value)) {
        showError(input, 'Email invalide');
        isValid = false;
    } else if (input.type === 'tel' && value && !isValidPhone(value)) {
        showError(input, 'Numéro de téléphone invalide');
        isValid = false;
    }
    
    return isValid;
}

function showError(input, message) {
    input.classList.add('error');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.color = '#e63946';
    errorDiv.style.fontSize = '0.85rem';
    errorDiv.style.marginTop = '0.3rem';
    errorDiv.textContent = message;
    input.parentElement.appendChild(errorDiv);
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function isValidPhone(phone) {
    return /^[\d\s\+\-\(\)]{8,}$/.test(phone);
}

// ============================================
// 8. RIPPLE EFFECT ON BUTTONS
// ============================================

document.querySelectorAll('.btn, .btn-secondary, .btn-login').forEach(button => {
    button.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple-effect');
        
        // Add ripple styles if not exist
        if (!document.querySelector('#ripple-styles')) {
            const style = document.createElement('style');
            style.id = 'ripple-styles';
            style.textContent = `
                .ripple-effect {
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255,255,255,0.6);
                    transform: scale(0);
                    animation: ripple 0.6s ease-out;
                    pointer-events: none;
                }
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
                .btn, .btn-secondary, .btn-login {
                    position: relative;
                    overflow: hidden;
                }
            `;
            document.head.appendChild(style);
        }
        
        this.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
    });
});

// ============================================
// 9. CURSOR TRAILER (OPTIONAL - PREMIUM EFFECT)
// ============================================

const trailer = document.createElement('div');
trailer.className = 'cursor-trailer';
trailer.style.cssText = `
    position: fixed;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(0,180,216,0.6), transparent);
    pointer-events: none;
    z-index: 9999;
    transition: transform 0.15s ease-out;
    display: none;
`;
document.body.appendChild(trailer);

// Only show on desktop
if (window.innerWidth > 1024) {
    trailer.style.display = 'block';
    
    let mouseX = 0, mouseY = 0;
    let trailerX = 0, trailerY = 0;
    
    document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
    });
    
    function animateTrailer() {
        const dx = mouseX - trailerX;
        const dy = mouseY - trailerY;
        
        trailerX += dx * 0.1;
        trailerY += dy * 0.1;
        
        trailer.style.left = trailerX + 'px';
        trailer.style.top = trailerY + 'px';
        
        requestAnimationFrame(animateTrailer);
    }
    
    animateTrailer();
}

// ============================================
// 10. PRODUCT IMAGE ZOOM ON HOVER
// ============================================

document.querySelectorAll('.product-card img').forEach(img => {
    img.addEventListener('mouseenter', function() {
        this.style.transition = 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
    });
});

// ============================================
// 11. LAZY LOADING IMAGES
// ============================================

const imageObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            if (img.dataset.src) {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
            }
            imageObserver.unobserve(img);
        }
    });
});

document.querySelectorAll('img[data-src]').forEach(img => {
    imageObserver.observe(img);
});

// ============================================
// 12. CONSOLE EASTER EGG
// ============================================

console.log('%c🦾 BionicLife - Powered by Innovation', 'font-size: 20px; color: #00b4d8; font-weight: bold;');
console.log('%cRedonnez vie à vos mouvements 💙', 'font-size: 14px; color: #666;');

// ============================================
// 13. PERFORMANCE LOGGING (DEV ONLY)
// ============================================

if (window.location.hostname === 'localhost') {
    window.addEventListener('load', () => {
        setTimeout(() => {
            const perfData = performance.getEntriesByType('navigation')[0];
            console.log(`⚡ Page loaded in ${(perfData.loadEventEnd - perfData.fetchStart).toFixed(2)}ms`);
        }, 0);
    });
}
