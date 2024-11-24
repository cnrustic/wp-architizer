class PerformanceMonitor {
    constructor() {
        this.metrics = {};
        this.init();
    }

    init() {
        this.observePageLoad();
        this.observeUserInteractions();
        this.observeNetworkRequests();
    }

    observePageLoad() {
        // 性能时间监控
        window.addEventListener('load', () => {
            const timing = performance.timing;
            const navigationStart = timing.navigationStart;

            this.metrics.pageLoad = {
                total: timing.loadEventEnd - navigationStart,
                ttfb: timing.responseStart - navigationStart,
                domReady: timing.domContentLoadedEventEnd - navigationStart,
                resources: timing.loadEventEnd - timing.domContentLoadedEventEnd
            };

            this.sendMetrics('pageLoad');
        });

        // 首次内容绘制
        const paint = performance.getEntriesByType('paint');
        paint.forEach(entry => {
            this.metrics[entry.name] = entry.startTime;
        });
    }

    observeUserInteractions() {
        let lastInteraction = performance.now();

        document.addEventListener('click', () => {
            const now = performance.now();
            const timeSinceLastInteraction = now - lastInteraction;
            
            this.metrics.interactionDelay = timeSinceLastInteraction;
            lastInteraction = now;
            
            this.sendMetrics('interaction');
        }, true);
    }

    observeNetworkRequests() {
        const observer = new PerformanceObserver((list) => {
            list.getEntries().forEach(entry => {
                if (entry.initiatorType === 'xmlhttprequest' || entry.initiatorType === 'fetch') {
                    this.metrics.apiCalls = this.metrics.apiCalls || [];
                    this.metrics.apiCalls.push({
                        name: entry.name,
                        duration: entry.duration,
                        size: entry.transferSize
                    });
                }
            });
        });

        observer.observe({ entryTypes: ['resource'] });
    }

    sendMetrics(type) {
        // 发送性能数据到后端
        if (type && this.metrics[type]) {
            fetch('/wp-admin/admin-ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'log_performance',
                    metrics: JSON.stringify(this.metrics[type]),
                    type: type
                })
            });
        }
    }
}

// 初始化性能监控
if (!window.isBot) {
    new PerformanceMonitor();
} 