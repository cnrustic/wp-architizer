class StatisticsCollector {
    constructor() {
        this.sessionId = this.generateSessionId();
        this.pageLoadTime = performance.now();
        this.interactions = [];
        this.init();
    }

    init() {
        this.trackPageview();
        this.trackInteractions();
        this.trackEngagement();
    }

    generateSessionId() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    trackPageview() {
        const data = {
            page_id: window.pageId,
            url: window.location.href,
            referrer: document.referrer,
            screen_size: `${window.innerWidth}x${window.innerHeight}`,
            load_time: performance.now()
        };

        this.sendData('pageview', data);
    }

    trackInteractions() {
        // 点击追踪
        document.addEventListener('click', (e) => {
            const target = e.target.closest('a, button, .clickable');
            if (target) {
                this.logInteraction('click', {
                    element: target.tagName,
                    text: target.textContent,
                    href: target.href || null
                });
            }
        });

        // 表单提交追踪
        document.addEventListener('submit', (e) => {
            this.logInteraction('form_submit', {
                form_id: e.target.id || null,
                form_action: e.target.action
            });
        });

        // 滚动深度追踪
        let maxScroll = 0;
        window.addEventListener('scroll', this.throttle(() => {
            const scrollPercent = (window.scrollY + window.innerHeight) / document.documentElement.scrollHeight * 100;
            if (scrollPercent > maxScroll) {
                maxScroll = scrollPercent;
                if (maxScroll % 25 === 0) { // 每25%记录一次
                    this.logInteraction('scroll_depth', {
                        depth: maxScroll
                    });
                }
            }
        }, 500));
    }

    trackEngagement() {
        let startTime = Date.now();
        let isActive = true;

        // 检测用户是否活跃
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.logEngagementTime(startTime);
                isActive = false;
            } else {
                startTime = Date.now();
                isActive = true;
            }
        });

        // 页面卸载时记录
        window.addEventListener('beforeunload', () => {
            if (isActive) {
                this.logEngagementTime(startTime);
            }
        });
    }

    logEngagementTime(startTime) {
        const duration = Date.now() - startTime;
        this.logInteraction('engagement_time', {
            duration: duration
        });
    }

    logInteraction(type, data) {
        const interaction = {
            type: type,
            data: data,
            timestamp: Date.now()
        };

        this.interactions.push(interaction);
        this.sendData('interaction', interaction);
    }

    async sendData(type, data) {
        try {
            const response = await fetch(wpAjax.ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'log_statistics',
                    nonce: wpAjax.statisticsNonce,
                    type: type,
                    data: JSON.stringify(data)
                })
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
        } catch (error) {
            console.error('Statistics error:', error);
            // 可以将失败的数据存储到 localStorage 中稍后重试
            this.storeFailedData(type, data);
        }
    }

    storeFailedData(type, data) {
        const failedData = JSON.parse(localStorage.getItem('failed_statistics') || '[]');
        failedData.push({ type, data, timestamp: Date.now() });
        localStorage.setItem('failed_statistics', JSON.stringify(failedData));
    }

    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        }
    }
}

// 初始化统计收集器
document.addEventListener('DOMContentLoaded', () => {
    new StatisticsCollector();
}); 