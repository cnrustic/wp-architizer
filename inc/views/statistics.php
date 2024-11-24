<div class="wrap">
    <h1>网站统计</h1>
    
    <div class="statistics-dashboard">
        <!-- 时间范围选择器 -->
        <div class="date-range-picker">
            <form method="get">
                <input type="hidden" name="page" value="wp-architizer-statistics">
                <select name="range">
                    <option value="7d">最近7天</option>
                    <option value="30d">最近30天</option>
                    <option value="90d">最近90天</option>
                    <option value="custom">自定义范围</option>
                </select>
                <input type="date" name="start_date" class="custom-date">
                <input type="date" name="end_date" class="custom-date">
                <button type="submit" class="button">应用</button>
            </form>
        </div>
        
        <!-- 概览卡片 -->
        <div class="statistics-cards">
            <div class="stat-card">
                <h3>总访问量</h3>
                <div class="stat-value"><?php echo $this->get_total_visits(); ?></div>
                <div class="stat-trend">
                    <?php echo $this->get_trend('visits'); ?>
                </div>
            </div>
            
            <div class="stat-card">
                <h3>独立访客</h3>
                <div class="stat-value"><?php echo $this->get_unique_visitors(); ?></div>
                <div class="stat-trend">
                    <?php echo $this->get_trend('visitors'); ?>
                </div>
            </div>
            
            <div class="stat-card">
                <h3>平均停留时间</h3>
                <div class="stat-value"><?php echo $this->get_avg_time(); ?></div>
                <div class="stat-trend">
                    <?php echo $this->get_trend('time'); ?>
                </div>
            </div>
            
            <div class="stat-card">
                <h3>跳出率</h3>
                <div class="stat-value"><?php echo $this->get_bounce_rate(); ?>%</div>
                <div class="stat-trend">
                    <?php echo $this->get_trend('bounce'); ?>
                </div>
            </div>
        </div>
        
        <!-- 图表区域 -->
        <div class="statistics-charts">
            <div class="chart-container">
                <h3>访问趋势</h3>
                <canvas id="visitsChart"></canvas>
            </div>
            
            <div class="chart-container">
                <h3>访问来源</h3>
                <canvas id="sourcesChart"></canvas>
            </div>
            
            <div class="chart-container">
                <h3>设备分布</h3>
                <canvas id="devicesChart"></canvas>
            </div>
            
            <div class="chart-container">
                <h3>地理分布</h3>
                <div id="geoMap"></div>
            </div>
        </div>
        
        <!-- 热门内容 -->
        <div class="popular-content">
            <h3>热门内容</h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>页面标题</th>
                        <th>访问量</th>
                        <th>平均停留时间</th>
                        <th>跳出率</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $this->get_popular_content(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div> 