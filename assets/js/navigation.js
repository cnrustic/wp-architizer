class Navigation {
    constructor() {
        this.header = document.querySelector('.site-header');
        this.mobileMenu = document.querySelector('.mobile-menu');
        this.menuTrigger = document.querySelector('.mobile-menu-trigger');
        this.closeMenu = document.querySelector('.close-menu');
        this.lastScrollTop = 0;
        
        this.init();
    }

    init() {
        // 移动菜单交互
        this.menuTrigger?.addEventListener('click', () => this.toggleMobileMenu());
        this.closeMenu?.addEventListener('click', () => this.toggleMobileMenu());

        // 滚动隐藏/显示头部
        window.addEventListener('scroll', () => this.handleScroll());

        // 子菜单展开/收起
        document.querySelectorAll('.menu-item-has-children').forEach(item => {
            const submenuToggle = document.createElement('button');
            submenuToggle.className = 'submenu-toggle';
            submenuToggle.innerHTML = '<i class="fas fa-chevron-down"></i>';
            item.appendChild(submenuToggle);

            submenuToggle.addEventListener('click', (e) => {
                e.preventDefault();
                item.classList.toggle('submenu-open');
            });
        });
    }

    toggleMobileMenu() {
        this.mobileMenu.classList.toggle('active');
        document.body.classList.toggle('menu-open');
    }

    handleScroll() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // 向上滚动显示，向下滚动隐藏
        if (scrollTop > this.lastScrollTop && scrollTop > 100) {
            this.header.classList.add('header-hidden');
        } else {
            this.header.classList.remove('header-hidden');
        }
        
        this.lastScrollTop = scrollTop;
    }
}

// 初始化
document.addEventListener('DOMContentLoaded', () => {
    new Navigation();
}); 