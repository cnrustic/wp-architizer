document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.querySelector('.search-form');
    const searchType = searchForm.querySelector('.search-type');
    const advancedToggle = searchForm.querySelector('.toggle-button');
    const advancedFields = searchForm.querySelector('.advanced-search-fields');
    const filterGroups = searchForm.querySelectorAll('.filter-group');
    
    // 切换高级搜索显示
    advancedToggle.addEventListener('click', function() {
        advancedFields.style.display = advancedFields.style.display === 'none' ? 'block' : 'none';
    });
    
    // 根据选择的内容类型显示相应的筛选选项
    searchType.addEventListener('change', function() {
        const selectedType = this.value;
        
        // 隐藏所有筛选组
        filterGroups.forEach(group => group.style.display = 'none');
        
        // 显示选中类型的筛选组
        if (selectedType !== 'all') {
            const targetGroup = searchForm.querySelector(`.${selectedType}-filters`);
            if (targetGroup) {
                targetGroup.style.display = 'grid';
            }
        }
    });
}); 