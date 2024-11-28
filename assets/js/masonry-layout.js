const initMasonry = () => {
    const grid = document.querySelector('.gallery-masonry');
    if (!grid) return;

    const masonry = new Masonry(grid, {
        itemSelector: '.gallery-item',
        columnWidth: '.gallery-sizer',
        percentPosition: true,
        gutter: 20
    });

    imagesLoaded(grid).on('progress', () => {
        masonry.layout();
    });
};

// 初始化瀑布流
initMasonry();

// AJAX加载更多后重新初始化
document.addEventListener('ajaxLoadComplete', initMasonry); 