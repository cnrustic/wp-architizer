document.addEventListener('DOMContentLoaded', function() {
    const mainItems = document.querySelectorAll('.gallery-main-item');
    const thumbs = document.querySelectorAll('.gallery-thumb');
    
    // 初始化第一张图片为激活状态
    if (mainItems.length > 0) {
        mainItems[0].classList.add('active');
    }
    if (thumbs.length > 0) {
        thumbs[0].classList.add('active');
    }
    
    // 为每个缩略图添加点击事件
    thumbs.forEach((thumb, index) => {
        thumb.addEventListener('click', () => {
            // 移除所有激活状态
            mainItems.forEach(item => item.classList.remove('active'));
            thumbs.forEach(t => t.classList.remove('active'));
            
            // 激活当前选中的图片
            mainItems[index].classList.add('active');
            thumb.classList.add('active');
        });
    });
}); 