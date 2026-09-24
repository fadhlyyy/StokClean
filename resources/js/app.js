import { createIcons, icons } from 'lucide';
import Chart from 'chart.js/auto';

window.createIcons = createIcons;
window.lucideIcons = icons;
window.Chart = Chart;

document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});
