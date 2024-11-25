document.addEventListener('DOMContentLoaded', function() {
    const searchToggle = document.querySelector('.search-toggle');
    const headerSearch = document.querySelector('.header-search');
    
    console.log('searchToggle:', searchToggle);
    console.log('headerSearch:', headerSearch);
    
    if (searchToggle && headerSearch) {
        searchToggle.addEventListener('click', function() {
            headerSearch.classList.toggle('active');
            if (headerSearch.classList.contains('active')) {
                headerSearch.querySelector('.search-field').focus();
            }
        });
        
        // 点击外部关闭搜索
        document.addEventListener('click', function(event) {
            if (!headerSearch.contains(event.target) && !searchToggle.contains(event.target)) {
                headerSearch.classList.remove('active');
            }
        });
    } else {
        console.warn('元素未找到');
    }
}); 