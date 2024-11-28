class PerformanceManager {
    constructor() {
        try {
            this.initElements();
            this.initEvents();
            this.initStats();
        } catch (error) {
            console.error('性能管理器初始化失败:', error);
            this.handleInitError(error);
        }
    }
    
    initElements() {
        this.optimizeImagesBtn = document.querySelector('#optimize-images');
        this.clearCacheBtn = document.querySelector('#clear-cache');
        this.progressBar = document.querySelector('.progress-bar .progress');
        this.logContainer = document.querySelector('.optimization-log');
    }
    
    initEvents() {
        this.optimizeImagesBtn?.addEventListener('click', () => this.handleImageOptimization());
        this.clearCacheBtn?.addEventListener('click', () => this.handleCacheClear());
    }
    
    async initStats() {
        try {
            const response = await fetch(ajaxurl + '?action=get_performance_stats');
            const stats = await response.json();
            this.updateStats(stats);
        } catch (error) {
            this.log('获取性能统计失败', 'error');
        }
    }
    
    log(message, type = 'info') {
        const entry = document.createElement('div');
        entry.className = `log-entry ${type}`;
        entry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
        this.logContainer?.insertBefore(entry, this.logContainer.firstChild);
    }
    
    updateProgress(percent) {
        if (this.progressBar) {
            this.progressBar.style.width = `${percent}%`;
        }
    }
    
    handleInitError(error) {
        const errorMessage = document.createElement('div');
        errorMessage.className = 'notice notice-error';
        errorMessage.innerHTML = `
            <p>性能管理器初始化失败：${error.message}</p>
            <p>请检查控制台获取详细信息。</p>
        `;
        document.querySelector('.wrap')?.prepend(errorMessage);
    }
} 