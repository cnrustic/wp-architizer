class InteractionEffects {
    constructor() {
        this.init();
    }

    init() {
        this.initScrollAnimations();
        this.initHoverEffects();
        this.initParallaxEffects();
        this.initImageLazyLoad();
        this.initSmoothScroll();
    }

    initScrollAnimations() {
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // 添加需要动画的元素
        document.querySelectorAll('.project-card, .product-card, .section-title, .filter-group').forEach(el => {
            el.classList.add('animate-item');
            observer.observe(el);
        });
    }

    initHoverEffects() {
        // 卡片悬停效果
        document.querySelectorAll('.project-card, .product-card').forEach(card => {
            card.addEventListener('mouseenter', this.handleCardHover);
            card.addEventListener('mouseleave', this.handleCardLeave);
        });

        // 按钮悬停效果
        document.querySelectorAll('.filter-submit, .load-more-btn').forEach(button => {
            button.addEventListener('mouseenter', this.handleButtonHover);
            button.addEventListener('mouseleave', this.handleButtonLeave);
        });
    }

    handleCardHover(e) {
        const card = e.currentTarget;
        const thumbnail = card.querySelector('.project-thumbnail img, .product-thumbnail img');
        
        if (thumbnail) {
            thumbnail.style.transform = 'scale(1.1)';
            thumbnail.style.transition = 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        }

        // 添加卡片阴影动画
        card.style.transform = 'translateY(-8px)';
        card.style.boxShadow = '0 12px 24px rgba(0,0,0,0.15)';
    }

    handleCardLeave(e) {
        const card = e.currentTarget;
        const thumbnail = card.querySelector('.project-thumbnail img, .product-thumbnail img');
        
        if (thumbnail) {
            thumbnail.style.transform = 'scale(1)';
        }

        card.style.transform = 'translateY(0)';
        card.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
    }

    handleButtonHover(e) {
        const button = e.currentTarget;
        button.style.transform = 'translateY(-2px)';
        this.createButtonRipple(e);
    }

    handleButtonLeave(e) {
        const button = e.currentTarget;
        button.style.transform = 'translateY(0)';
    }

    createButtonRipple(e) {
        const button = e.currentTarget;
        const ripple = document.createElement('span');
        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = `${size}px`;
        ripple.style.left = `${x}px`;
        ripple.style.top = `${y}px`;
        ripple.classList.add('ripple');
        
        button.appendChild(ripple);
        
        ripple.addEventListener('animationend', () => {
            ripple.remove();
        });
    }

    initParallaxEffects() {
        window.addEventListener('scroll', () => {
            requestAnimationFrame(() => {
                document.querySelectorAll('.parallax-bg').forEach(el => {
                    const scrolled = window.pageYOffset;
                    const rate = scrolled * -0.3;
                    el.style.transform = `translate3d(0, ${rate}px, 0)`;
                });
            });
        });
    }

    initImageLazyLoad() {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.dataset.src;
                    if (src) {
                        img.src = src;
                        img.classList.add('fade-in');
                        observer.unobserve(img);
                    }
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = anchor.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
}

// 添加必要的 CSS
const interactionStyles = document.createElement('style');
interactionStyles.textContent = `
    .animate-item {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }

    .animate-in {
        opacity: 1;
        transform: translateY(0);
    }

    .fade-in {
        animation: fadeIn 0.5s ease forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,0.4);
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    }

    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    .project-card,
    .product-card {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                    box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .project-thumbnail img,
    .product-thumbnail img {
        transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .filter-submit,
    .load-more-btn {
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .parallax-bg {
        will-change: transform;
    }
`;
document.head.appendChild(interactionStyles);

// 初始化
document.addEventListener('DOMContentLoaded', () => {
    new InteractionEffects();
}); 