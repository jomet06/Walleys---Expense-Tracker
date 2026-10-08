import {
    Chart, BarController, BarElement, CategoryScale, LinearScale,
    DoughnutController, ArcElement, Tooltip, Legend,
} from 'chart.js';

Chart.register(BarController, BarElement, CategoryScale, LinearScale,
    DoughnutController, ArcElement, Tooltip, Legend);

const data = window.dashboardData;
const monthlyCanvas = document.getElementById('monthly-chart');
const expenseCanvas = document.getElementById('expense-chart');
const rupiah = new Intl.NumberFormat('id-ID', {
    style: 'currency', currency: 'IDR', maximumFractionDigits: 0,
});
const options = {
    responsive: true,
    maintainAspectRatio: false,
    animation: !window.matchMedia('(prefers-reduced-motion: reduce)').matches,
    plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 16 } },
        tooltip: {
            callbacks: {
                label: (context) => `${context.dataset.label || context.label}: ${rupiah.format(context.parsed.y ?? context.parsed)}`,
            },
        },
    },
};

if (data && monthlyCanvas) {
    new Chart(monthlyCanvas, {
        type: 'bar',
        data: {
            labels: data.monthly.labels,
            datasets: [
                { label: 'Income', data: data.monthly.income, backgroundColor: '#059669', borderRadius: 4 },
                { label: 'Expenses', data: data.monthly.expense, backgroundColor: '#e11d48', borderRadius: 4 },
            ],
        },
        options: {
            ...options,
            scales: {
                y: { beginAtZero: true, ticks: { callback: (value) => rupiah.format(value) } },
                x: { grid: { display: false } },
            },
        },
    });
}

if (data && expenseCanvas && data.expenses.values.length) {
    new Chart(expenseCanvas, {
        type: 'doughnut',
        data: {
            labels: data.expenses.labels,
            datasets: [{
                data: data.expenses.values,
                backgroundColor: ['#4f46e5', '#059669', '#e11d48', '#d97706', '#0284c7', '#9333ea', '#0d9488', '#475569'],
                borderWidth: 2,
            }],
        },
        options: { ...options, cutout: '68%', plugins: { ...options.plugins, legend: { display: false } } },
    });
}
