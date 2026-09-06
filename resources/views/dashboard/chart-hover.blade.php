{{-- 
    Komponen Visualisasi ECharts Interaktif (Hover Linked Pie & Line Chart)
    Mematuhi Standar UI: Anti-Cropping, Mobile Friendly, Spacing Kelipatan 4px & 8px, Bebas Emoji Grafis Unicode
--}}
<section class="grid grid-cols-1 gap-4 sm:gap-6" aria-label="Analisis tren inventaris">
    <article class="animate-enter delay-300 rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white shadow-sm overflow-hidden flex flex-col justify-between transition-all duration-300 hover:shadow-md">
        {{-- Header Panel --}}
        <div class="bg-slate-900 p-4 sm:p-5 border-b border-slate-800 flex items-center justify-between gap-3">
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h2 class="text-sm sm:text-base font-bold text-white tracking-wide truncate">
                        Distribusi & Tren Kategori Inventaris
                    </h2>
                    <span class="hidden sm:inline-flex items-center rounded-md bg-indigo-500/20 px-2 py-0.5 text-[11px] font-semibold text-indigo-300 border border-indigo-500/30">
                        Interaktif
                    </span>
                </div>
                <p class="mt-0.5 text-xs sm:text-sm text-slate-300">
                    Arahkan kursor atau sentuh sumbu tahun untuk melihat sinkronisasi diagram lingkaran dan garis pertumbuhan.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="flex h-2.5 w-2.5 rounded-full bg-emerald-400 shadow-sm animate-pulse" title="Sistem Aktif"></span>
            </div>
        </div>

        {{-- Canvas Chart Container --}}
        <div class="p-3 sm:p-6 w-full">
            <div id="echarts-hover-main" class="w-full min-h-[420px] sm:min-h-[480px] h-[440px] sm:h-[500px]"></div>
        </div>

        {{-- Footer Insight Note --}}
        <div class="px-4 py-3 sm:px-6 sm:py-3.5 bg-slate-50/80 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-500">
            <div class="flex items-center gap-1.5 truncate">
                <svg class="h-4 w-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="truncate">Kategori: {{ implode(', ', $hoverChartData['categories'] ?? ['Jaringan', 'Komputer', 'Perangkat Keras', 'Alat Praktik']) }}</span>
            </div>
            <span class="text-[11px] text-slate-400">Pembaruan Otomatis Terintegrasi</span>
        </div>
    </article>
</section>

<script>
window.initDashboardHoverChart = function () {
    var chartDom = document.getElementById('echarts-hover-main');
    if (!chartDom) return;

    if (typeof echarts === 'undefined') {
        console.warn('ECharts library is still loading...');
        setTimeout(window.initDashboardHoverChart, 100);
        return;
    }

    // Bersihkan instance lama jika sudah ada
    var existingChart = echarts.getInstanceByDom(chartDom);
    if (existingChart) {
        existingChart.dispose();
    }

    var myChart = echarts.init(chartDom, null, { renderer: 'canvas' });
    
    // Dataset Source yang disuplai oleh DashboardController
    var datasetSource = @json($hoverChartData['source'] ?? []);
    var categoriesList = @json($hoverChartData['categories'] ?? []);
    var initialYear = @json($hoverChartData['initialYear'] ?? (string) date('Y'));

    // Fallback jika dataset kosong
    if (!datasetSource || datasetSource.length === 0) {
        datasetSource = [
            ['product', '2021', '2022', '2023', '2024', '2025', '2026'],
            ['Jaringan', 25, 30, 42, 58, 65, 80],
            ['Komputer', 18, 24, 30, 35, 48, 52],
            ['Perangkat Keras', 12, 16, 22, 28, 34, 40],
            ['Alat Praktik', 15, 20, 25, 30, 38, 45],
            ['Furniture', 10, 15, 18, 22, 28, 32]
        ];
        categoriesList = ['Jaringan', 'Komputer', 'Perangkat Keras', 'Alat Praktik', 'Furniture'];
        initialYear = '2026';
    }

    // Konstruksi series garis untuk setiap kategori
    var seriesList = [];
    for (var i = 0; i < categoriesList.length; i++) {
        seriesList.push({
            type: 'line',
            smooth: true,
            seriesLayoutBy: 'row',
            emphasis: { focus: 'series' }
        });
    }

    // Tambahkan series Pie interaktif di bagian atas chart
    seriesList.push({
        type: 'pie',
        id: 'pie',
        radius: window.innerWidth < 640 ? '26%' : '30%',
        center: ['50%', window.innerWidth < 640 ? '24%' : '25%'],
        emphasis: {
            focus: 'self'
        },
        label: {
            formatter: '{b}: {@' + initialYear + '} ({d}%)',
            fontSize: window.innerWidth < 640 ? 11 : 12,
            color: '#334155'
        },
        encode: {
            itemName: 'product',
            value: initialYear,
            tooltip: initialYear
        }
    });

    var option = {
        animation: true,
        animationThreshold: 2000,
        animationDuration: 1200,
        animationEasing: 'cubicOut',
        animationDelay: function (idx) {
            return idx * 100;
        },
        animationDurationUpdate: 600,
        animationEasingUpdate: 'cubicInOut',
        color: ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'],
        legend: {
            top: '0%',
            textStyle: {
                fontSize: window.innerWidth < 640 ? 11 : 12,
                color: '#64748b'
            },
            icon: 'circle'
        },
        tooltip: {
            trigger: 'axis',
            showContent: false
        },
        dataset: {
            source: datasetSource
        },
        xAxis: {
            type: 'category',
            axisLine: { lineStyle: { color: '#cbd5e1' } },
            axisLabel: { color: '#64748b', fontSize: window.innerWidth < 640 ? 11 : 12 }
        },
        yAxis: {
            gridIndex: 0,
            splitLine: { lineStyle: { color: '#f1f5f9', type: 'dashed' } },
            axisLabel: { color: '#64748b', fontSize: window.innerWidth < 640 ? 11 : 12 }
        },
        grid: {
            top: window.innerWidth < 640 ? '55%' : '52%',
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        series: seriesList
    };

    // Event interaksi Hover Axis Pointer untuk sinkronisasi Pie Chart
    myChart.on('updateAxisPointer', function (event) {
        var xAxisInfo = event.axesInfo && event.axesInfo[0];
        if (xAxisInfo) {
            var dimension = xAxisInfo.value + 1;
            myChart.setOption({
                series: {
                    id: 'pie',
                    label: {
                        formatter: '{b}: {@[' + dimension + ']} ({d}%)'
                    },
                    encode: {
                        value: dimension,
                        tooltip: dimension
                    }
                }
            });
        }
    });

    myChart.setOption(option);

    // Pastikan ukuran chart pas (handle rendering delay)
    setTimeout(function() {
        if (myChart && !myChart.isDisposed()) {
            myChart.resize();
        }
    }, 60);

    // Responsive Auto Resize Listener
    var resizeHandler = function () {
        if (myChart && !myChart.isDisposed()) {
            var isMobile = window.innerWidth < 640;
            myChart.setOption({
                legend: {
                    textStyle: { fontSize: isMobile ? 11 : 12 }
                },
                xAxis: {
                    axisLabel: { fontSize: isMobile ? 11 : 12 }
                },
                yAxis: {
                    axisLabel: { fontSize: isMobile ? 11 : 12 }
                },
                grid: {
                    top: isMobile ? '55%' : '52%'
                },
                series: [{
                    id: 'pie',
                    radius: isMobile ? '26%' : '30%',
                    center: ['50%', isMobile ? '24%' : '25%'],
                    label: { fontSize: isMobile ? 11 : 12 }
                }]
            });
            myChart.resize();
        }
    };

    if (window._hoverChartResizeListener) {
        window.removeEventListener('resize', window._hoverChartResizeListener);
    }
    window._hoverChartResizeListener = resizeHandler;
    window.addEventListener('resize', resizeHandler);
};

// Eksekusi segera jika DOM sudah siap, atau tunggu DOMContentLoaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.initDashboardHoverChart);
} else {
    requestAnimationFrame(function () {
        window.initDashboardHoverChart();
    });
}
</script>
