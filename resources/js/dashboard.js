import {
    Chart, BarController, BarElement, CategoryScale, LinearScale,
    LineController, LineElement, PointElement,
    DoughnutController, ArcElement, Tooltip, Legend,
} from 'chart.js';

Chart.register(BarController, BarElement, CategoryScale, LinearScale,
    LineController, LineElement, PointElement,
    DoughnutController, ArcElement, Tooltip, Legend);

const data = window.dashboardData;
const monthlyCanvas = document.getElementById('monthly-chart');
const expenseCanvas = document.getElementById('expense-chart');
const rupiah = new Intl.NumberFormat('id-ID', {
    style: 'currency', currency: 'IDR', maximumFractionDigits: 0,
});
const compactRupiah = new Intl.NumberFormat('id-ID', {
    notation: 'compact', maximumFractionDigits: 1,
});
const categoryColors = ['#20b8b8', '#3b6776', '#e9a45e', '#8187a8', '#e1848b', '#87aea0', '#b59878', '#a3b3bf'];
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const font = { family: '"Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif' };
const options = {
    responsive: true,
    maintainAspectRatio: false,
    animation: reducedMotion ? false : { duration: 350 },
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                boxWidth: 8,
                boxHeight: 8,
                usePointStyle: true,
                padding: 20,
                color: '#64748b',
                font: { ...font, size: 11 },
            },
        },
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
            datasets: [
                {
                    label: 'Income', data: data.monthly.income,
                    backgroundColor: '#20b8b8', borderColor: '#20b8b8',
                    borderRadius: 5, borderSkipped: false, maxBarThickness: 24,
                    pointRadius: 3, pointHoverRadius: 5, borderWidth: 0,
                },
                {
                    label: 'Expenses', data: data.monthly.expense,
                    backgroundColor: '#b8c8d0', borderColor: '#7896a5',
                    borderRadius: 5, borderSkipped: false, maxBarThickness: 24,
                    pointRadius: 3, pointHoverRadius: 5, borderWidth: 0,
                },
            ],
        },
        options: {
            ...options,
            interaction: { mode: 'index', intersect: false },
            layout: { padding: { top: 8 } },
            scales: {
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { color: '#f1f5f9', drawTicks: false },
                    ticks: {
                        maxTicksLimit: 5, padding: 12,
                        color: '#94a3b8', font: { ...font, size: 10 },
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

            // Preserve legend visibility when changing the chart display.
            const visibility = monthlyChart.data.datasets.map((_, index) => monthlyChart.isDatasetVisible(index));
            monthlyChart.config.type = type;
            monthlyChart.data.datasets.forEach((dataset, index) => {
                dataset.borderWidth = type === 'line' ? 2 : 0;
                dataset.tension = 0.25;
                dataset.hidden = !visibility[index];
            });
            monthlyChart.update();

            document.querySelectorAll('[data-chart-type]').forEach((control) => {
                control.setAttribute('aria-pressed', String(control.dataset.chartType === type));
            });
        });
    });
}

if (data && expenseCanvas && data.expenses.values.length) {
    document.querySelectorAll('[data-category-swatch]').forEach((swatch) => {
        swatch.style.backgroundColor = categoryColors[Number(swatch.dataset.categorySwatch) % categoryColors.length];
    });

    new Chart(expenseCanvas, {
        type: 'doughnut',
        data: {
            labels: data.expenses.labels,
            datasets: [{
                data: data.expenses.values,
                backgroundColor: data.expenses.values.map((_, index) => categoryColors[index % categoryColors.length]),
                borderColor: '#ffffff',
                borderWidth: 3,
                borderRadius: 3,
                hoverOffset: reducedMotion ? 0 : 5,
            }],
        },
        options: {
            ...options,
            cutout: '78%',
            layout: { padding: 8 },
            plugins: { ...options.plugins, legend: { display: false } },
        },
    });
}
