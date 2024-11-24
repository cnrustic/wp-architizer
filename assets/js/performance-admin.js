class PerformanceManager {
    constructor() {
        this.initializeElements();
        this.bindEvents();
        this.loadStats();
    }
    
    initializeElements() {
        this.optimizeImagesBtn = document.getElementById('optimize-images');
        this.clearCacheBtn = document.getElementById('clear-cache');
        this.progressBar = document.querySelector('.progress-bar .progress');
        this.logContainer = document.querySelector('.optimization-log');
    }
    
    bindEvents() {
        if (this.optimizeImagesBtn) {
            this.optimizeImagesBtn.addEventListener('click', () => this.handleImageOptimization());
        }
        
        if (this.clearCacheBtn) {
            this.clearCacheBtn.addEventListener('click', () => this.handleCacheClear());
        }
        
        // 监听设置变更
        document.querySelectorAll('.performance-settings input').forEach(input => {
            input.addEventListener('change', () => this.handleSettingChange(input));
        });
    }
    
    async loadStats() {
        try {
            const response = await fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: 'get_performance_stats',
                    nonce: performanceSettings.nonce
                })
            });
            
            const data = await response.json();
            if (data.success) {
                this.updateStats(data.data);
            }
        } catch (error) {
            console.error('加载性能统计失败:', error);
        }
    }
    
    updateStats(stats) {
        Object.entries(stats).forEach(([key, value]) => {
            const element = document.querySelector(`[data-stat="${key}"]`);
            if (element) {
                element.textContent = value;
            }
        });
    }
    
    async handleImageOptimization() {
        this.optimizeImagesBtn.disabled = true;
        this.progressBar.style.width = '0%';
        
        try {
            const images = await this.getUnoptimizedImages();
            let processed = 0;
            
            for (const image of images) {
                await this.optimizeImage(image);
                processed++;
                this.updateProgress((processed / images.length) * 100);
                this.log(`优化图片: ${image.filename}`, 'success');
            }
            
            this.log('所有图片优化完成!', 'success');
        } catch (error) {
            this.log(`优化失败: ${error.message}`, 'error');
        } finally {
            this.optimizeImagesBtn.disabled = false;
        }
    }
    
    async handleCacheClear() {
        this.clearCacheBtn.disabled = true;
        
        try {
            const response = await fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: 'clear_all_cache',
                    nonce: performanceSettings.nonce
                })
            });
            
            const data = await response.json();
            if (data.success) {
                this.log('缓存清理成功!', 'success');
            } else {
                throw new Error(data.data);
            }
        } catch (error) {
            this.log(`清理缓存失败: ${error.message}`, 'error');
        } finally {
            this.clearCacheBtn.disabled = false;
        }
    }
    
    async handleSettingChange(input) {
        const setting = input.name;
        const value = input.type === 'checkbox' ? input.checked : input.value;
        
        try {
            const response = await fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: 'update_performance_setting',
                    nonce: performanceSettings.nonce,
                    setting,
                    value
                })
            });
            
            const data = await response.json();
            if (data.success) {
                this.log(`设置已更新: ${setting}`, 'success');
            } else {
                throw new Error(data.data);
            }
        } catch (error) {
            this.log(`更新设置失败: ${error.message}`, 'error');
            // 恢复原值
            input.checked = !input.checked;
        }
    }
    
    updateProgress(percentage) {
        this.progressBar.style.width = `${percentage}%`;
    }
    
    log(message, type = 'info') {
        const entry = document.createElement('div');
        entry.className = `log-entry ${type}`;
        entry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
        
        this.logContainer.insertBefore(entry, this.logContainer.firstChild);
        
        // 限制日志条目数量
        if (this.logContainer.children.length > 100) {
            this.logContainer.removeChild(this.logContainer.lastChild);
        }
    }
    
    async getUnoptimizedImages() {
        const response = await fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: 'get_unoptimized_images',
                nonce: performanceSettings.nonce
            })
        });
        
        const data = await response.json();
        if (!data.success) {
            throw new Error(data.data);
        }
        
        return data.data;
    }
    
    async optimizeImage(image) {
        const response = await fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: 'optimize_single_image',
                nonce: performanceSettings.nonce,
                image_id: image.id
            })
        });
        
        const data = await response.json();
        if (!data.success) {
            throw new Error(data.data);
        }
        
        return data.data;
    }
}

// 初始化性能管理器
document.addEventListener('DOMContentLoaded', () => {
    new PerformanceManager();
}); 