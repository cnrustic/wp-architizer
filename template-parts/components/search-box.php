<div class="search-box">
    <form role="search" method="get" action="<?php echo home_url('/'); ?>" class="search-form">
        <input type="text" 
               class="search-input" 
               placeholder="搜索项目、产品或建筑师..." 
               value="<?php echo get_search_query(); ?>" 
               name="s" 
               autocomplete="off">
        <div class="search-type">
            <select name="post_type">
                <option value="project">项目</option>
                <option value="product">产品</option>
                <option value="firm">建筑事务所</option>
            </select>
        </div>
        <button type="submit" class="search-submit">
            <i class="fas fa-search"></i>
        </button>
    </form>
    <div class="search-suggestions"></div>
</div> 