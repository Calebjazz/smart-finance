(function () {
    'use strict';

    const STORAGE_KEY = 'smartfinance_theme';
    const DARK_CLASS = 'dark-mode';

    function getChartColors(isDark) {
        return {
            text: isDark ? '#94a3b8' : '#475569',
            grid: isDark ? '#334155' : '#e2e8f0',
        };
    }

    class ThemeManager {
        constructor() {
            this.body = document.body;
            this.charts = [];
            this.init();
        }

        init() {
            const saved = localStorage.getItem(STORAGE_KEY);
            this.setDarkMode(saved === 'dark');
            window.addEventListener('storage', (e) => {
                if (e.key === STORAGE_KEY) {
                    this.setDarkMode(e.newValue === 'dark');
                }
            });
        }

        setDarkMode(isDark) {
            this.body.classList.toggle(DARK_CLASS, isDark);
            localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light');
            this.updateThemeIcon(isDark);
            this.applyChartDefaults(isDark);
            this.refreshRegisteredCharts(isDark);
            document.dispatchEvent(new CustomEvent('themechange', { detail: { isDark } }));
        }

        toggle() {
            this.setDarkMode(!this.body.classList.contains(DARK_CLASS));
        }

        isDark() {
            return this.body.classList.contains(DARK_CLASS);
        }

        updateThemeIcon(isDark) {
            const icon = document.getElementById('theme-icon');
            if (!icon) return;
            icon.classList.toggle('fa-sun', isDark);
            icon.classList.toggle('fa-moon', !isDark);
        }

        applyChartDefaults(isDark) {
            if (typeof Chart === 'undefined') return;
            const colors = getChartColors(isDark);
            Chart.defaults.color = colors.text;
            Chart.defaults.borderColor = colors.grid;
        }

        registerChart(chart) {
            if (chart) this.charts.push(chart);
        }

        refreshRegisteredCharts(isDark) {
            if (typeof Chart === 'undefined') return;
            const colors = getChartColors(isDark);
            this.charts.forEach((chart) => {
                if (!chart || !chart.options) return;
                if (chart.options.plugins?.legend?.labels) {
                    chart.options.plugins.legend.labels.color = colors.text;
                }
                if (chart.options.scales) {
                    Object.values(chart.options.scales).forEach((scale) => {
                        if (scale.ticks) scale.ticks.color = colors.text;
                        if (scale.grid) scale.grid.color = colors.grid;
                    });
                }
                chart.update();
            });
        }

        buildChartOptions(extra = {}) {
            const colors = getChartColors(this.isDark());
            const base = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: colors.text } },
                },
                scales: {
                    y: { ticks: { color: colors.text }, grid: { color: colors.grid } },
                    x: { ticks: { color: colors.text }, grid: { color: colors.grid } },
                },
            };
            return Object.assign(base, extra);
        }
    }

    // Apply theme immediately to prevent flash
    (function earlyTheme() {
        try {
            if (localStorage.getItem(STORAGE_KEY) === 'dark') {
                document.documentElement.classList.add(DARK_CLASS);
                document.addEventListener('DOMContentLoaded', () => {
                    document.body.classList.add(DARK_CLASS);
                });
            }
        } catch (e) { /* ignore */ }
    })();

    // Expose chart helpers immediately so inline scripts can run safely before DOMContentLoaded
    window.sfChartOptions = (extra = {}) => {
        if (window.themeManager) {
            return window.themeManager.buildChartOptions(extra);
        }
        // Fallback options build if themeManager is not yet booted
        const isDark = document.documentElement.classList.contains('dark-mode');
        const colors = getChartColors(isDark);
        const base = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: colors.text } },
            },
            scales: {
                y: { ticks: { color: colors.text }, grid: { color: colors.grid } },
                x: { ticks: { color: colors.text }, grid: { color: colors.grid } },
            },
        };
        return Object.assign(base, extra);
    };

    window._tempCharts = [];
    window.sfRegisterChart = (chart) => {
        if (window.themeManager) {
            window.themeManager.registerChart(chart);
        } else {
            window._tempCharts.push(chart);
        }
    };

    function boot() {
        window.themeManager = new ThemeManager();
        window.toggleTheme = () => window.themeManager.toggle();

        // Register any charts that were created before themeManager booted
        if (window._tempCharts && window._tempCharts.length) {
            window._tempCharts.forEach((c) => window.themeManager.registerChart(c));
            window._tempCharts = [];
        }

        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => window.toggleTheme());
        }

        // Sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content') || document.querySelector('main');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        if (sidebar && mainContent && sidebarToggle) {
            let open = true;
            sidebarToggle.addEventListener('click', () => {
                open = !open;
                sidebar.style.transform = open ? 'translateX(0)' : 'translateX(-100%)';
                mainContent.style.marginLeft = open ? '16rem' : '0';
            });
        }

        // Notifications dropdown
        const notifBtn = document.getElementById('notif-toggle');
        const notifPanel = document.getElementById('notif-panel');
        if (notifBtn && notifPanel) {
            notifBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                notifPanel.classList.toggle('hidden');
            });
            document.addEventListener('click', () => notifPanel.classList.add('hidden'));
            notifPanel.addEventListener('click', (e) => e.stopPropagation());
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
