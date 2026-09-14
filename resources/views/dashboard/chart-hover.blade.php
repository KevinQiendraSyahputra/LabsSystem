{{-- 
    Komponen Visualisasi ECharts Tren Aktivitas & Distribusi Inventaris (Gentelella v4 2026 Edition)
    Mematuhi Standar UI: Anti-Cropping, Mobile Friendly, Spacing Kelipatan 4px & 8px, Bebas Emoji Grafis Unicode
--}}
<section class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6" aria-label="Analisis tren inventaris">

    {{-- Kartu Utama: Tren Aktivitas Laboratorium (Line & Area Chart dengan Tab Filter) --}}
    <article class="lg:col-span-8 animate-enter delay-500 rounded-xl sm:rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden flex flex-col justify-between transition-all duration-300 hover:shadow-md">
        {{-- Header Panel & Tab Filter --}}
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-850">
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-slate-100 tracking-tight">
                        Tren Aktivitas Laboratorium
                    </h2>
                    <span class="inline-flex items-center rounded-full bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 text-[10px] sm:text-xs font-semibold text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                        Live Data
                    </span>
                </div>
                <div class="mt-1 flex items-baseline gap-2">
                    <span class="text-lg sm:text-2xl font-extrabold text-slate-900 dark:text-white tabular-nums">
                        {{ $chartData['totalMingguIni'] ?? 0 }}
                    </span>
                    <span class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium">total aktivitas 7 hari terakhir</span>
                </div>
            </div>

            {{-- Segmented Range Tabs ala Gentelella v4 --}}
            <div class="inline-flex rounded-lg bg-slate-100 dark:bg-slate-800 p-0.5 self-start sm:self-auto border border-slate-200 dark:border-slate-700" role="tablist">
                <button type="button" id="tab-7d" onclick="switchChartRange('7d')" class="chart-range-tab px-2.5 py-1 text-[11px] sm:text-xs font-semibold rounded-md transition-all duration-200 bg-indigo-600 text-white shadow-xs">
                    7 Hari
                </button>
                <button type="button" id="tab-30d" onclick="switchChartRange('30d')" class="chart-range-tab px-2.5 py-1 text-[11px] sm:text-xs font-semibold rounded-md transition-all duration-200 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                    30 Hari
                </button>
                <button type="button" id="tab-6m" onclick="switchChartRange('6m')" class="chart-range-tab px-2.5 py-1 text-[11px] sm:text-xs font-semibold rounded-md transition-all duration-200 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                    6 Bulan
                </button>
            </div>
        </div>

        {{-- Canvas Chart Container --}}
        <div class="p-3 sm:p-5 w-full">
            <div id="echarts-activity-trend" class="w-full h-[260px] sm:h-[300px] lg:h-[320px]"></div>
        </div>

        {{-- Legend Footer --}}
        <div class="px-4 py-3 sm:px-6 sm:py-3 bg-slate-50/70 dark:bg-slate-850 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 text-[11px] sm:text-xs text-slate-500 dark:text-slate-400">
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-1.5">
                    <span class="h-2 w-4 rounded-full bg-indigo-600 inline-block"></span>
                    <span class="font-medium text-slate-700 dark:text-slate-300">Peminjaman Barang</span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="h-2 w-4 rounded-full bg-sky-500 inline-block"></span>
                    <span class="font-medium text-slate-700 dark:text-slate-300">Perawatan / Maintenance</span>
                </span>
            </div>
            <span class="text-slate-400 dark:text-slate-500 font-medium">Sinkronisasi Waktu Nyata</span>
        </div>
    </article>

    {{-- Kartu Samping: Distribusi Kondisi & Kategori Inventaris (Donut Chart ala Gentelella v4) --}}
    <article class="lg:col-span-4 animate-enter delay-550 rounded-xl sm:rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden flex flex-col justify-between transition-all duration-300 hover:shadow-md">
        {{-- Header Panel --}}
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2 bg-slate-50/50 dark:bg-slate-850">
            <div class="min-w-0">
                <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-slate-100 tracking-tight">
                    Distribusi Kondisi
                </h2>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400 truncate">Rasio fisik perangkat laboratorium</p>
            </div>
            <span class="flex h-2 w-2 rounded-full bg-emerald-500" title="Aktif"></span>
        </div>

        {{-- Donut Canvas Container --}}
        <div class="p-3 sm:p-4 flex flex-col items-center justify-center relative">
            <div id="echarts-kondisi-donut" class="w-full h-[200px] sm:h-[220px]"></div>
            
            {{-- Center Label Overlay --}}
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pb-2">
                <span class="text-lg sm:text-2xl font-extrabold text-slate-900 dark:text-white tabular-nums">
                    {{ number_format($totalBarang ?? 0) }}
                </span>
                <span class="text-[10px] sm:text-[11px] font-semibold text-slate-400 dark:text-slate-400 uppercase tracking-wider">
                    Total Unit
                </span>
            </div>
        </div>

        {{-- Donut Legend List ala Gentelella v4 --}}
        <div class="p-4 sm:p-5 pt-0 space-y-2 border-t border-slate-100/80 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-850/50">
            @php
                $kondisiList = [
                    ['label' => 'Baik', 'count' => $barangBaik ?? 0, 'color' => '#10b981'],
                    ['label' => 'Perbaikan', 'count' => $barangPerbaikan ?? 0, 'color' => '#f59e0b'],
                    ['label' => 'Rusak Berat', 'count' => $barangRusak ?? 0, 'color' => '#ef4444'],
                    ['label' => 'Perawatan', 'count' => $barangPerawatan ?? 0, 'color' => '#6366f1'],
                    ['label' => 'Hilang', 'count' => $barangHilang ?? 0, 'color' => '#94a3b8'],
                ];
            @endphp
            <div class="grid grid-cols-2 gap-2 text-[11px]">
                @foreach($kondisiList as $kItem)
                    @php
                        $kPct = ($totalBarang ?? 0) > 0 ? round(($kItem['count'] / $totalBarang) * 100, 1) : 0;
                    @endphp
                    <div class="flex items-center justify-between p-1.5 rounded-lg bg-white dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700/60 shadow-2xs">
                        <div class="flex items-center gap-1.5 min-w-0 truncate">
                            <span class="h-2 w-2 rounded-full shrink-0" style="background-color: {{ $kItem['color'] }}"></span>
                            <span class="text-slate-600 dark:text-slate-300 truncate">{{ $kItem['label'] }}</span>
                        </div>
                        <span class="font-bold text-slate-800 dark:text-slate-100 tabular-nums ml-1">{{ $kPct }}%</span>
                    </div>
                @endforeach
            </div>
        </div>
    </article>

</section>

<script>
(function() {
    var activityChart = null;
    var donutChart = null;

    // Data dari Controller
    var chartDataPayload = @json($chartData ?? []);
    var kondisiStatsPayload = @json($kondisiStats ?? []);

    var isDarkMode = function() {
        return document.documentElement.classList.contains('dark');
    };

    window.initGentelellaDashboardCharts = function() {
        var actDom = document.getElementById('echarts-activity-trend');
        var donDom = document.getElementById('echarts-kondisi-donut');

        if (!actDom || !donDom) return;

        if (typeof echarts === 'undefined') {
            setTimeout(window.initGentelellaDashboardCharts, 100);
            return;
        }

        // Bersihkan instance lama jika ada
        if (activityChart) {
            activityChart.dispose();
        }
        if (donutChart) {
            donutChart.dispose();
        }

        var fontFam = "'Figtree', -apple-system, BlinkMacSystemFont, sans-serif";

        // 1. Inisialisasi Activity Trend Chart
        activityChart = echarts.init(actDom, null, { renderer: 'canvas' });
        window.currentActivityRange = window.currentActivityRange || '7d';

        var getOptionForRange = function(rangeKey) {
            var categories = chartDataPayload.days7 || ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
            var dataPinjam = chartDataPayload.peminjaman7d || [0, 0, 0, 0, 0, 0, 0];
            var dataMaint  = chartDataPayload.maintenance7d || [0, 0, 0, 0, 0, 0, 0];

            if (rangeKey === '30d') {
                categories = chartDataPayload.days30 || [];
                dataPinjam = chartDataPayload.peminjaman30d || [];
                dataMaint  = chartDataPayload.maintenance30d || [];
            } else if (rangeKey === '6m') {
                categories = chartDataPayload.months6 || [];
                dataPinjam = chartDataPayload.peminjaman6m || [];
                dataMaint  = chartDataPayload.maintenance6m || [];
            }

            var isMobile = window.innerWidth < 640;
            var dark = isDarkMode();

            var textColor = dark ? '#94a3b8' : '#64748b';
            var axisLineColor = dark ? '#1e293b' : '#e2e8f0';
            var splitLineColor = dark ? 'rgba(30, 41, 59, 0.7)' : '#f1f5f9';
            var tooltipBg = dark ? '#0b132b' : '#ffffff';
            var tooltipBorder = dark ? '#1e293b' : '#e2e8f0';
            var tooltipText = dark ? '#f8fafc' : '#0f172a';
            var pointBorder = dark ? '#111c44' : '#ffffff';

            return {
                animation: true,
                animationDuration: 900,
                animationEasing: 'cubicOut',
                textStyle: { fontFamily: fontFam, fontSize: isMobile ? 10 : 11, color: textColor },
                grid: {
                    left: '2%',
                    right: '3%',
                    top: '12%',
                    bottom: '8%',
                    containLabel: true
                },
                tooltip: {
                    trigger: 'axis',
                    backgroundColor: tooltipBg,
                    borderColor: tooltipBorder,
                    borderWidth: 1,
                    padding: [8, 12],
                    textStyle: { color: tooltipText, fontSize: 12, fontFamily: fontFam },
                    extraCssText: 'box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25); border-radius: 8px;'
                },
                xAxis: {
                    type: 'category',
                    data: categories,
                    boundaryGap: false,
                    axisLine: { lineStyle: { color: axisLineColor } },
                    axisTick: { show: false },
                    axisLabel: { color: textColor, fontSize: isMobile ? 10 : 11 }
                },
                yAxis: {
                    type: 'value',
                    minInterval: 1,
                    splitLine: { lineStyle: { color: splitLineColor, type: 'dashed' } },
                    axisLabel: { color: textColor, fontSize: isMobile ? 10 : 11 },
                    axisLine: { show: false },
                    axisTick: { show: false }
                },
                series: [
                    {
                        name: 'Peminjaman',
                        type: 'line',
                        smooth: 0.35,
                        symbol: 'circle',
                        symbolSize: 6,
                        showSymbol: false,
                        data: dataPinjam,
                        lineStyle: { color: '#6366f1', width: 2.5 },
                        itemStyle: { color: '#6366f1', borderColor: pointBorder, borderWidth: 2 },
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                { offset: 0, color: 'rgba(99, 102, 241, 0.32)' },
                                { offset: 1, color: 'rgba(99, 102, 241, 0.00)' }
                            ])
                        }
                    },
                    {
                        name: 'Maintenance',
                        type: 'line',
                        smooth: 0.35,
                        symbol: 'circle',
                        symbolSize: 6,
                        showSymbol: false,
                        data: dataMaint,
                        lineStyle: { color: '#0ea5e9', width: 2, type: 'dashed' },
                        itemStyle: { color: '#0ea5e9', borderColor: pointBorder, borderWidth: 2 },
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                { offset: 0, color: 'rgba(14, 165, 233, 0.22)' },
                                { offset: 1, color: 'rgba(14, 165, 233, 0.00)' }
                            ])
                        }
                    }
                ]
            };
        };

        activityChart.setOption(getOptionForRange(window.currentActivityRange || '7d'));

        // 2. Inisialisasi Donut Chart Distribusi Kondisi
        donutChart = echarts.init(donDom, null, { renderer: 'canvas' });

        var getDonutOption = function() {
            var dark = isDarkMode();
            var tooltipBg = dark ? '#0b132b' : '#ffffff';
            var tooltipBorder = dark ? '#1e293b' : '#e2e8f0';
            var tooltipText = dark ? '#f8fafc' : '#0f172a';
            var sliceBorder = dark ? '#111c44' : '#ffffff';

            var donutData = [
                { value: kondisiStatsPayload['Baik'] || 0, name: 'Baik', itemStyle: { color: '#10b981' } },
                { value: kondisiStatsPayload['Perbaikan'] || 0, name: 'Perbaikan', itemStyle: { color: '#f59e0b' } },
                { value: kondisiStatsPayload['Rusak Berat'] || 0, name: 'Rusak Berat', itemStyle: { color: '#ef4444' } },
                { value: kondisiStatsPayload['Perawatan'] || 0, name: 'Perawatan', itemStyle: { color: '#6366f1' } },
                { value: kondisiStatsPayload['Hilang'] || 0, name: 'Hilang', itemStyle: { color: '#94a3b8' } }
            ];

            return {
                animation: true,
                animationDuration: 1000,
                animationEasing: 'cubicOut',
                tooltip: {
                    trigger: 'item',
                    backgroundColor: tooltipBg,
                    borderColor: tooltipBorder,
                    borderWidth: 1,
                    padding: [6, 10],
                    textStyle: { color: tooltipText, fontSize: 11, fontFamily: fontFam },
                    extraCssText: 'box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25); border-radius: 6px;',
                    formatter: '{b}: {c} unit ({d}%)'
                },
                series: [{
                    type: 'pie',
                    radius: ['60%', '82%'],
                    center: ['50%', '50%'],
                    avoidLabelOverlap: false,
                    label: { show: false },
                    labelLine: { show: false },
                    itemStyle: {
                        borderColor: sliceBorder,
                        borderWidth: 2
                    },
                    data: donutData
                }]
            };
        };

        donutChart.setOption(getDonutOption());

        // Switch Range Tab Function
        window.switchChartRange = function(rangeKey) {
            window.currentActivityRange = rangeKey;
            
            var allTabs = document.querySelectorAll('.chart-range-tab');
            allTabs.forEach(function(btn) {
                btn.className = 'chart-range-tab px-2.5 py-1 text-[11px] sm:text-xs font-semibold rounded-md transition-all duration-200 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
            });

            var activeBtn = document.getElementById('tab-' + rangeKey);
            if (activeBtn) {
                activeBtn.className = 'chart-range-tab px-2.5 py-1 text-[11px] sm:text-xs font-semibold rounded-md transition-all duration-200 bg-indigo-600 text-white shadow-xs';
            }

            if (activityChart) {
                activityChart.setOption(getOptionForRange(rangeKey), true);
            }
        };

        // Resize handler
        var resizeCharts = function() {
            if (activityChart && !activityChart.isDisposed()) {
                activityChart.resize();
            }
            if (donutChart && !donutChart.isDisposed()) {
                donutChart.resize();
            }
        };

        window.addEventListener('resize', resizeCharts);
        setTimeout(resizeCharts, 80);

        // Event Listener saat user mengubah tema Dark/Light
        var themeHandler = function() {
            setTimeout(function() {
                if (activityChart && !activityChart.isDisposed()) {
                    activityChart.setOption(getOptionForRange(window.currentActivityRange || '7d'), true);
                }
                if (donutChart && !donutChart.isDisposed()) {
                    donutChart.setOption(getDonutOption(), true);
                }
            }, 50);
        };
        window.addEventListener('themechanged', themeHandler);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.initGentelellaDashboardCharts);
    } else {
        requestAnimationFrame(function() {
            window.initGentelellaDashboardCharts();
        });
    }
})();
</script>
