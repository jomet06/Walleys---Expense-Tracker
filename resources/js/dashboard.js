import {
    Chart, BarController, BarElement, CategoryScale, LinearScale,
    LineController, LineElement, PointElement,
    DoughnutController, ArcElement, Tooltip,
} from 'chart.js';

Chart.register(BarController, BarElement, CategoryScale, LinearScale,
    LineController, LineElement, PointElement,
    DoughnutController, ArcElement, Tooltip);

const data = window.dashboardData;
const monthlyCanvas = document.getElementById('monthly-chart');
const breakdownCanvas = document.getElementById('expense-chart');
const rupiah = new Intl.NumberFormat('id-ID', {
    style: 'currency', currency: 'IDR', maximumFractionDigits: 0,
});
const compactRupiah = new Intl.NumberFormat('id-ID', {
    notation: 'compact', maximumFractionDigits: 1,
});
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const font = { family: '"Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif' };
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    animation: reducedMotion ? false : { duration: 300 },
    plugins: {
        tooltip: {
            backgroundColor: '#172b36',
            titleFont: { ...font, size: 11 },
            bodyFont: { ...font, size: 12 },
            padding: 12,
            cornerRadius: 10,
            boxPadding: 5,
            callbacks: {
                label: (context) => `${context.dataset.label || context.label}: ${rupiah.format(context.parsed.y ?? context.parsed)}`,
            },
        },
    },
};

if (data && monthlyCanvas) {
    const monthlyChart = new Chart(monthlyCanvas, {
        type: 'bar',
        data: {
            labels: data.monthly.labels,
            datasets: [{
                label: 'Expenses',
                data: data.monthly.expense,
                backgroundColor: '#f43f5e',
                borderColor: '#e11d48',
                borderRadius: 5,
                borderSkipped: false,
                maxBarThickness: 36,
                pointRadius: 3,
                pointHoverRadius: 5,
                borderWidth: 0,
            }],
        },
        options: {
            ...chartOptions,
            interaction: { mode: 'index', intersect: false },
            layout: { padding: { top: 8 } },
            scales: {
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { color: '#f1f5f9', drawTicks: false },
                    ticks: {
                        maxTicksLimit: 5, padding: 12,
                        color: '#64748b', font: { ...font, size: 10 },
                        callback: (value) => `Rp ${compactRupiah.format(value)}`,
                    },
                },
                x: {
                    border: { display: false },
                    grid: { display: false },
                    ticks: {
                        color: '#64748b', font: { ...font, size: 10 },
                        maxRotation: 0, padding: 10,
                        callback(value) {
                            return this.getLabelForValue(value).split(' ')[0];
                        },
                    },
                },
            },
        },
    });

    document.querySelectorAll('[data-chart-type]').forEach((button) => {
        button.addEventListener('click', () => {
            const type = button.dataset.chartType;
            if (!['bar', 'line'].includes(type) || monthlyChart.config.type === type) return;

            monthlyChart.config.type = type;
            monthlyChart.data.datasets[0].borderWidth = type === 'line' ? 2 : 0;
            monthlyChart.data.datasets[0].tension = 0.25;
            monthlyChart.update();

            document.querySelectorAll('[data-chart-type]').forEach((control) => {
                control.setAttribute('aria-pressed', String(control.dataset.chartType === type));
            });
        });
    });
}

if (data) {
    const modeSettings = {
        cashflow: {
            title: 'Total movement',
            label: 'Current month cashflow: income and expense movement',
            colors: ['#059669', '#e11d48'],
        },
        income: {
            title: 'Total income',
            label: 'Current month income by category',
            colors: ['#059669', '#34d399', '#065f46', '#6ee7b7', '#0d9488', '#84cc16'],
        },
        expenses: {
            title: 'Total expenses',
            label: 'Current month expenses by category',
            colors: ['#e11d48', '#fb7185', '#9f1239', '#fda4af', '#be123c', '#f43f5e'],
        },
    };
    const controls = Array.from(document.querySelectorAll('[data-breakdown-mode]'));
    const panels = document.querySelectorAll('[data-breakdown-panel]');
    const chartRegion = document.querySelector('[data-breakdown-chart-region]');
    const totalLabel = document.querySelector('[data-breakdown-total-label]');
    const totalValue = document.querySelector('[data-breakdown-total]');
    let breakdownChart;

    const showBreakdown = (mode) => {
        if (!Object.hasOwn(modeSettings, mode)) return;

        const source = data[mode];
        const settings = modeSettings[mode];
        const total = source.values.reduce((sum, value) => sum + value, 0);
        const colors = source.values.map((_, index) => settings.colors[index % settings.colors.length]);

        controls.forEach((control) => {
            const selected = control.dataset.breakdownMode === mode;
            control.setAttribute('aria-selected', String(selected));
            control.tabIndex = selected ? 0 : -1;
        });
        panels.forEach((panel) => {
            panel.hidden = panel.dataset.breakdownPanel !== mode;
        });

        // Update text safely; category names stay in escaped server-rendered lists.
        if (totalLabel) totalLabel.textContent = settings.title;
        if (totalValue) totalValue.textContent = rupiah.format(total);
        if (breakdownCanvas) breakdownCanvas.setAttribute('aria-label', settings.label);
        if (chartRegion) chartRegion.hidden = total === 0;
        if (!breakdownCanvas || total === 0) return;

        if (!breakdownChart) {
            breakdownChart = new Chart(breakdownCanvas, {
                type: 'doughnut',
                data: {
                    labels: source.labels,
                    datasets: [{
                        data: source.values,
                        backgroundColor: colors,
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        borderRadius: 2,
                        hoverOffset: reducedMotion ? 0 : 4,
                    }],
                },
                options: {
                    ...chartOptions,
                    cutout: '78%',
                    layout: { padding: 8 },
                },
            });
            return;
        }

        breakdownChart.data.labels = source.labels;
        breakdownChart.data.datasets[0].data = source.values;
        breakdownChart.data.datasets[0].backgroundColor = colors;
        breakdownChart.resize();
        breakdownChart.update();
    };

    controls.forEach((control, index) => {
        control.addEventListener('click', () => showBreakdown(control.dataset.breakdownMode));
        control.addEventListener('keydown', (event) => {
            let nextIndex;
            if (event.key === 'ArrowRight') nextIndex = (index + 1) % controls.length;
            if (event.key === 'ArrowLeft') nextIndex = (index + controls.length - 1) % controls.length;
            if (event.key === 'Home') nextIndex = 0;
            if (event.key === 'End') nextIndex = controls.length - 1;
            if (nextIndex === undefined) return;

            event.preventDefault();
            controls[nextIndex].focus();
            showBreakdown(controls[nextIndex].dataset.breakdownMode);
        });
    });

    showBreakdown('cashflow');
}