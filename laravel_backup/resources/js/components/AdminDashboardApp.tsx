import React, { useState, useEffect } from 'react';
import { 
  Package, 
  CheckCircle2, 
  Wrench, 
  AlertTriangle, 
  DollarSign, 
  ArrowLeftRight, 
  CalendarClock, 
  PlusCircle, 
  ArrowRight, 
  Layers, 
  Sparkles, 
  FileText,
  ChevronRight,
  TrendingUp,
  Zap
} from 'lucide-react';
import { Skeleton } from '@/components/ui/skeleton';

export interface AdminDashboardProps {
  data?: {
    totalBarang: number;
    barangBaik: number;
    barangRusak: number;
    barangPerbaikan: number;
    barangPerawatan: number;
    barangHilang: number;
    totalNilai: number;
    barangDipinjam: number;
    maintenanceBulanIni: number;
    barangBaruTahunIni: number;
    peminjamanTerlambat: number;
    peminjamanAktif: Array<{
      id: number;
      nama_peminjam: string;
      barang?: { nama_barang: string };
      tanggal_kembali_rencana: string;
      status: string;
    }>;
    kategoriStats: Array<{
      kategori: string;
      total: number;
    }>;
    kondisiStats: Record<string, number>;
    barangTerbaru: Array<{
      id: number;
      nama_barang: string;
      kode_barang: string;
      kategori: string;
      kondisi: string;
      jumlah: number;
      satuan: string;
    }>;
    routes?: {
      tambahBarang: string;
      peminjaman: string;
      laporan: string;
      barangIndex: string;
    };
  };
}

export const AdminDashboardApp: React.FC<AdminDashboardProps | any> = (props) => {
  const [isLoading, setIsLoading] = useState(true);
  const [animated, setAnimated] = useState(false);

  // Mendukung props { data: {...} } atau props langsung {...}
  const data = props?.data ? props.data : props;

  useEffect(() => {
    // Skeleton loading transisi saat pertama kali masuk
    const timer = setTimeout(() => {
      setIsLoading(false);
      // Animasi progress bar dimulai sesaat setelah data muncul
      setTimeout(() => setAnimated(true), 50);
    }, 900);
    return () => clearTimeout(timer);
  }, []);

  const totalBarang = Number(data?.totalBarang ?? 0);
  const safeTotal = Math.max(totalBarang, 1);
  const totalNilai = Number(data?.totalNilai ?? 0);
  const barangBaik = Number(data?.barangBaik ?? 0);
  const barangPerbaikan = Number(data?.barangPerbaikan ?? 0);
  const barangRusak = Number(data?.barangRusak ?? 0);
  const barangPerawatan = Number(data?.barangPerawatan ?? 0);
  const barangHilang = Number(data?.barangHilang ?? 0);
  const barangDipinjam = Number(data?.barangDipinjam ?? 0);
  const maintenanceBulanIni = Number(data?.maintenanceBulanIni ?? 0);
  const barangBaruTahunIni = Number(data?.barangBaruTahunIni ?? 0);
  const peminjamanTerlambat = Number(data?.peminjamanTerlambat ?? 0);

  const kondisiStats: Record<string, number> = data?.kondisiStats && Object.keys(data.kondisiStats).length > 0
    ? data.kondisiStats
    : {
        Baik: barangBaik,
        Perawatan: barangPerawatan,
        Perbaikan: barangPerbaikan,
        'Rusak Berat': barangRusak,
        Hilang: barangHilang,
      };

  const kategoriStats = Array.isArray(data?.kategoriStats) ? data.kategoriStats : [];
  const peminjamanAktif = Array.isArray(data?.peminjamanAktif) ? data.peminjamanAktif : [];
  const barangTerbaru = Array.isArray(data?.barangTerbaru) ? data.barangTerbaru : [];

  const routes = data?.routes ?? {
    tambahBarang: '/barang/create',
    peminjaman: '/peminjaman',
    laporan: '/laporan',
    barangIndex: '/barang',
  };

  const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      maximumFractionDigits: 0,
    }).format(val);
  };

  const getConditionColor = (kondisi: string) => {
    switch (kondisi.toLowerCase()) {
      case 'baik':
        return {
          badge: 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
          bar: 'from-emerald-500 to-teal-500',
        };
      case 'perbaikan':
        return {
          badge: 'bg-amber-50 text-amber-700 border-amber-200/80',
          bar: 'from-amber-500 to-yellow-500',
        };
      case 'perawatan':
        return {
          badge: 'bg-amber-50 text-amber-700 border-amber-200/80',
          bar: 'from-amber-500 to-orange-400',
        };
      case 'rusak berat':
        return {
          badge: 'bg-rose-50 text-rose-700 border-rose-200/80',
          bar: 'from-rose-500 to-red-600',
        };
      case 'hilang':
        return {
          badge: 'bg-slate-100 text-slate-700 border-slate-200',
          bar: 'from-slate-500 to-gray-600',
        };
      default:
        return {
          badge: 'bg-slate-100 text-slate-700 border-slate-200',
          bar: 'from-indigo-500 to-indigo-600',
        };
    }
  };

  if (isLoading) {
    return <AdminDashboardSkeleton />;
  }

  return (
    <div className="space-y-4 sm:space-y-6">
      {/* Alert Peminjaman Terlambat */}
      {peminjamanTerlambat > 0 && (
        <section className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 rounded-xl border border-rose-200 bg-rose-50/95 p-3.5 sm:p-4 shadow-sm backdrop-blur transition-all duration-300 hover:shadow-md">
          <div className="flex items-start gap-3 min-w-0">
            <div className="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-rose-100 text-rose-700 shadow-inner">
              <AlertTriangle className="h-5 w-5 animate-pulse text-rose-700" />
            </div>
            <div className="min-w-0 flex-1">
              <h2 className="text-sm sm:text-base font-semibold text-rose-900 leading-snug">
                {peminjamanTerlambat} peminjaman melewati batas pengembalian
              </h2>
              <p className="mt-0.5 text-xs sm:text-sm text-rose-700 leading-relaxed">
                Periksa daftar peminjaman aktif dan segera tindak lanjuti barang yang belum dikembalikan.
              </p>
            </div>
          </div>
          <a
            href={routes.peminjaman}
            className="w-full sm:w-auto inline-flex items-center justify-center min-h-[40px] shrink-0 rounded-lg border border-rose-300 bg-white px-4 py-2 text-xs sm:text-sm font-semibold text-rose-700 shadow-sm transition-all duration-200 active:scale-[0.98] hover:bg-rose-100 hover:shadow focus:outline-none"
          >
            Periksa Data
          </a>
        </section>
      )}

      {/* Header Banner Bersih Persis Tampilan Asli */}
      <header className="rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 lg:p-6 shadow-sm transition-all duration-300 hover:shadow-md">
        <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div className="min-w-0 flex-1">
            <div className="inline-flex items-center gap-1.5 rounded-md bg-indigo-50 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider text-indigo-700 shadow-sm">
              <span className="relative flex h-2 w-2">
                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                <span className="relative inline-flex rounded-full h-2 w-2 bg-indigo-600"></span>
              </span>
              Sistem Manajemen Lab TKJ
            </div>
            <h1 className="mt-2 text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
              Dashboard Admin
            </h1>
            <p className="mt-1 text-xs sm:text-sm leading-relaxed text-slate-500">
              Ringkasan inventaris, kondisi barang, peminjaman, dan aktivitas laboratorium secara realtime.
            </p>
          </div>

          <div className="grid grid-cols-2 sm:flex sm:items-center gap-2 sm:gap-3 w-full lg:w-auto pt-2 lg:pt-0">
            <a
              href={routes.tambahBarang}
              className="group inline-flex min-h-[44px] items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition-all duration-200 active:scale-[0.97] hover:bg-indigo-700 hover:shadow-md hover:shadow-indigo-500/20 focus:outline-none"
            >
              <PlusCircle className="h-4 w-4 shrink-0 transition-transform duration-200 group-hover:rotate-90" />
              <span className="truncate">Tambah Barang</span>
            </a>

            <a
              href={routes.peminjaman}
              className="group inline-flex min-h-[44px] items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-700 shadow-sm transition-all duration-200 active:scale-[0.97] hover:bg-slate-50 hover:border-slate-400 hover:text-slate-950 focus:outline-none"
            >
              <ArrowLeftRight className="h-4 w-4 shrink-0 transition-transform duration-200 group-hover:-translate-y-0.5" />
              <span className="truncate">Peminjaman</span>
            </a>
          </div>
        </div>
      </header>

      {/* Ringkasan Inventaris (8 Kartu Bersih Sesuai Desain Awal) */}
      <section aria-labelledby="ringkasan-heading">
        <div className="mb-3 px-1">
          <h2 id="ringkasan-heading" className="text-sm sm:text-base font-semibold text-slate-900">
            Ringkasan Inventaris
          </h2>
          <p className="mt-0.5 text-xs sm:text-sm text-slate-500">
            Ikhtisar status aset dan pemakaian sarana laboratorium.
          </p>
        </div>

        <div className="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-4">
          {/* 1. Total Barang */}
          <article className="group relative flex items-center justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3 sm:p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-indigo-200">
            <div className="min-w-0 flex-1 pr-1.5 sm:pr-3">
              <p className="text-[11px] sm:text-xs font-medium text-slate-500 uppercase tracking-wide truncate">Total Barang</p>
              <p className="mt-1 sm:mt-1.5 text-xl sm:text-3xl font-bold tabular-nums text-slate-900 group-hover:text-indigo-600 transition-colors">
                {totalBarang}
              </p>
            </div>
            <div className="flex h-8 w-8 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-indigo-50 text-indigo-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white shadow-sm">
              <Package className="h-4 w-4 sm:h-6 sm:w-6" />
            </div>
          </article>

          {/* 2. Kondisi Baik */}
          <article className="group relative flex items-center justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3 sm:p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-emerald-200">
            <div className="min-w-0 flex-1 pr-1.5 sm:pr-3">
              <p className="text-[11px] sm:text-xs font-medium text-slate-500 uppercase tracking-wide truncate">Kondisi Baik</p>
              <p className="mt-1 sm:mt-1.5 text-xl sm:text-3xl font-bold tabular-nums text-emerald-600">
                {barangBaik}
              </p>
            </div>
            <div className="flex h-8 w-8 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-emerald-50 text-emerald-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white shadow-sm">
              <CheckCircle2 className="h-4 w-4 sm:h-6 sm:w-6" />
            </div>
          </article>

          {/* 3. Dalam Perbaikan */}
          <article className="group relative flex items-center justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3 sm:p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-amber-200">
            <div className="min-w-0 flex-1 pr-1.5 sm:pr-3">
              <p className="text-[11px] sm:text-xs font-medium text-slate-500 uppercase tracking-wide truncate">Dalam Perbaikan</p>
              <p className="mt-1 sm:mt-1.5 text-xl sm:text-3xl font-bold tabular-nums text-amber-600">
                {barangPerbaikan}
              </p>
            </div>
            <div className="flex h-8 w-8 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-amber-50 text-amber-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white shadow-sm">
              <Wrench className="h-4 w-4 sm:h-6 sm:w-6" />
            </div>
          </article>

          {/* 4. Rusak Berat */}
          <article className="group relative flex items-center justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3 sm:p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-rose-200">
            <div className="min-w-0 flex-1 pr-1.5 sm:pr-3">
              <p className="text-[11px] sm:text-xs font-medium text-slate-500 uppercase tracking-wide truncate">Rusak Berat</p>
              <p className="mt-1 sm:mt-1.5 text-xl sm:text-3xl font-bold tabular-nums text-rose-600">
                {barangRusak}
              </p>
            </div>
            <div className="flex h-8 w-8 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-rose-50 text-rose-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-rose-600 group-hover:text-white shadow-sm">
              <AlertTriangle className="h-4 w-4 sm:h-6 sm:w-6" />
            </div>
          </article>

          {/* 5. Nilai Aset Lab */}
          <article className="col-span-2 md:col-span-1 lg:col-span-1 group relative flex items-center justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3.5 sm:p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-slate-300">
            <div className="min-w-0 flex-1 pr-2 sm:pr-3">
              <p className="text-[11px] sm:text-xs font-medium text-slate-500 uppercase tracking-wide truncate">Nilai Aset Lab</p>
              <p className="mt-1 sm:mt-1.5 text-lg sm:text-2xl font-bold tabular-nums text-slate-900 truncate group-hover:text-indigo-600 transition-colors">
                {formatRupiah(totalNilai)}
              </p>
            </div>
            <div className="flex h-8 w-8 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-slate-100 text-slate-700 transition-transform duration-300 group-hover:scale-110 group-hover:bg-slate-800 group-hover:text-white shadow-sm">
              <DollarSign className="h-4 w-4 sm:h-6 sm:w-6" />
            </div>
          </article>

          {/* 6. Dipinjam Aktif */}
          <article className="group relative flex items-center justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3 sm:p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-sky-200">
            <div className="min-w-0 flex-1 pr-1.5 sm:pr-3">
              <p className="text-[11px] sm:text-xs font-medium text-slate-500 uppercase tracking-wide truncate">Dipinjam Aktif</p>
              <p className="mt-1 sm:mt-1.5 text-xl sm:text-3xl font-bold tabular-nums text-sky-600">
                {barangDipinjam}
              </p>
            </div>
            <div className="flex h-8 w-8 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-sky-50 text-sky-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-sky-600 group-hover:text-white shadow-sm">
              <ArrowLeftRight className="h-4 w-4 sm:h-6 sm:w-6" />
            </div>
          </article>

          {/* 7. Maintenance Bulan Ini */}
          <article className="group relative flex items-center justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3 sm:p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-violet-200">
            <div className="min-w-0 flex-1 pr-1.5 sm:pr-3">
              <p className="text-[11px] sm:text-xs font-medium text-slate-500 uppercase tracking-wide truncate">Maintenance Bulan Ini</p>
              <p className="mt-1 sm:mt-1.5 text-xl sm:text-3xl font-bold tabular-nums text-violet-600">
                {maintenanceBulanIni}
              </p>
            </div>
            <div className="flex h-8 w-8 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-violet-50 text-violet-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-violet-600 group-hover:text-white shadow-sm">
              <CalendarClock className="h-4 w-4 sm:h-6 sm:w-6" />
            </div>
          </article>

          {/* 8. Barang Baru Tahun Ini */}
          <article className="group relative flex items-center justify-between overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-3 sm:p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-teal-200">
            <div className="min-w-0 flex-1 pr-1.5 sm:pr-3">
              <p className="text-[11px] sm:text-xs font-medium text-slate-500 uppercase tracking-wide truncate">Barang Baru (Tahun Ini)</p>
              <p className="mt-1 sm:mt-1.5 text-xl sm:text-3xl font-bold tabular-nums text-teal-600">
                {barangBaruTahunIni}
              </p>
            </div>
            <div className="flex h-8 w-8 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-teal-50 text-teal-600 transition-transform duration-300 group-hover:scale-110 group-hover:bg-teal-600 group-hover:text-white shadow-sm">
              <PlusCircle className="h-4 w-4 sm:h-6 sm:w-6" />
            </div>
          </article>
        </div>
      </section>

      {/* Bagian Visualisasi Grafik Bar: Distribusi Kondisi & Kategori */}
      <section className="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6" aria-label="Statistik inventaris">
        {/* Distribusi Kondisi Barang */}
        <article className="rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white shadow-sm overflow-hidden flex flex-col justify-between transition-all duration-300 hover:shadow-md">
          <div className="bg-slate-900 p-4 sm:p-5 border-b border-slate-800 flex items-center justify-between">
            <div>
              <h2 className="text-sm sm:text-base font-bold text-white">Distribusi Kondisi Barang</h2>
              <p className="mt-0.5 text-xs sm:text-sm text-slate-300">Persentase kondisi dari keseluruhan barang inventaris.</p>
            </div>
            <span className="flex h-2.5 w-2.5 rounded-full bg-emerald-400 shadow-sm animate-pulse" title="Realtime Data"></span>
          </div>

          <div className="space-y-4 p-4 sm:p-5 flex-1">
            {Object.entries(kondisiStats).length === 0 ? (
              <div className="flex h-32 items-center justify-center rounded-lg border border-dashed border-slate-200 text-xs sm:text-sm text-slate-500">
                Data kondisi barang belum tersedia.
              </div>
            ) : (
              Object.entries(kondisiStats).map(([kondisi, count]) => {
                const countNum = Number(count);
                const percentage = Math.min(Math.round((countNum / safeTotal) * 100), 100);
                const color = getConditionColor(kondisi);

                return (
                  <div key={kondisi} className="group">
                    <div className="mb-1.5 flex items-center justify-between gap-3 text-xs sm:text-sm">
                      <span className="font-medium text-slate-700 truncate group-hover:text-slate-950 transition-colors">
                        {kondisi}
                      </span>
                      <span className="shrink-0 tabular-nums text-slate-500">
                        <span className="font-semibold text-slate-900">{countNum}</span> unit{' '}
                        <span className="text-slate-400 font-normal">({percentage}%)</span>
                      </span>
                    </div>
                    <div className="relative h-2.5 sm:h-3 w-full overflow-hidden rounded-full bg-slate-100/90 p-0.5 ring-1 ring-inset ring-slate-200/40">
                      <div
                        className={`h-full rounded-full bg-gradient-to-r ${color.bar} transition-all duration-1000 ease-out shadow-xs`}
                        style={{
                          width: animated ? `${percentage}%` : '0%',
                        }}
                      ></div>
                    </div>
                  </div>
                );
              })
            )}
          </div>
        </article>

        {/* Kategori Barang */}
        <article className="rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white shadow-sm overflow-hidden flex flex-col justify-between transition-all duration-300 hover:shadow-md">
          <div className="bg-slate-900 p-4 sm:p-5 border-b border-slate-800 flex items-center justify-between">
            <div>
              <h2 className="text-sm sm:text-base font-bold text-white">Kategori Barang</h2>
              <p className="mt-0.5 text-xs sm:text-sm text-slate-300">Sebaran kuantitas inventaris berdasarkan kategori alat.</p>
            </div>
            <span className="flex h-2.5 w-2.5 rounded-full bg-indigo-400 shadow-sm animate-pulse" title="Realtime Data"></span>
          </div>

          <div className="space-y-4 p-4 sm:p-5 flex-1">
            {kategoriStats.length === 0 ? (
              <div className="flex h-32 items-center justify-center rounded-lg border border-dashed border-slate-200 text-xs sm:text-sm text-slate-500">
                Data kategori barang belum tersedia.
              </div>
            ) : (
              kategoriStats.map((cat: any) => {
                const catTotal = Number(cat.total ?? 0);
                const percentage = Math.min(Math.round((catTotal / safeTotal) * 100), 100);

                return (
                  <div key={cat.kategori} className="group">
                    <div className="mb-1.5 flex items-center justify-between gap-3 text-xs sm:text-sm">
                      <span className="font-medium text-slate-700 truncate group-hover:text-slate-950 transition-colors">
                        {cat.kategori}
                      </span>
                      <span className="shrink-0 tabular-nums text-slate-500">
                        <span className="font-semibold text-slate-900">{catTotal}</span> unit{' '}
                        <span className="text-slate-400 font-normal">({percentage}%)</span>
                      </span>
                    </div>
                    <div className="relative h-2.5 sm:h-3 w-full overflow-hidden rounded-full bg-slate-100/90 p-0.5 ring-1 ring-inset ring-slate-200/40">
                      <div
                        className="h-full rounded-full bg-gradient-to-r from-indigo-500 via-indigo-600 to-indigo-700 transition-all duration-1000 ease-out shadow-xs"
                        style={{
                          width: animated ? `${percentage}%` : '0%',
                        }}
                      ></div>
                    </div>
                  </div>
                );
              })
            )}
          </div>
        </article>
      </section>

      {/* Grid 2 Tabel Aktivitas: Peminjaman Aktif & Barang Baru */}
      <section className="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6" aria-label="Aktivitas terbaru">
        {/* Peminjaman Aktif */}
        <article className="min-w-0 overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md">
          <div className="bg-slate-900 p-4 sm:p-5 border-b border-slate-800 flex items-center justify-between gap-3">
            <div className="min-w-0">
              <h2 className="text-sm sm:text-base font-bold text-white">Peminjaman Aktif</h2>
              <p className="mt-0.5 text-xs text-slate-300 truncate">Barang yang sedang dipinjam saat ini.</p>
            </div>
            <a
              href={routes.peminjaman}
              className="group inline-flex items-center gap-1 shrink-0 text-xs sm:text-sm font-semibold text-indigo-300 hover:text-white p-1 transition-colors"
            >
              Lihat semua <span className="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
            </a>
          </div>

          <div className="divide-y divide-slate-100">
            {peminjamanAktif.length === 0 ? (
              <div className="px-4 py-8 text-center text-xs sm:text-sm text-slate-500">
                Tidak ada peminjaman aktif saat ini.
              </div>
            ) : (
              peminjamanAktif.map((pinjam: any) => {
                const isTerlambat = pinjam.status === 'Terlambat' || new Date(pinjam.tanggal_kembali_rencana) < new Date();

                return (
                  <div key={pinjam.id} className="p-3.5 sm:p-4 space-y-2.5 transition-colors hover:bg-slate-50/60">
                    <div className="flex items-start justify-between gap-2">
                      <div className="min-w-0 flex-1">
                        <p className="font-semibold text-slate-900 text-sm truncate">{pinjam.nama_peminjam}</p>
                        <p className="text-xs text-slate-500 mt-0.5 truncate">{pinjam.barang?.nama_barang ?? '-'}</p>
                      </div>
                      {isTerlambat ? (
                        <span className="inline-flex shrink-0 items-center rounded-full border border-rose-200 bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-700 shadow-sm">
                          Terlambat
                        </span>
                      ) : (
                        <span className="inline-flex shrink-0 items-center rounded-full border border-sky-200 bg-sky-50 px-2 py-0.5 text-[11px] font-semibold text-sky-700 shadow-sm">
                          Dipinjam
                        </span>
                      )}
                    </div>
                    <div className="flex items-center justify-between text-xs pt-1 border-t border-slate-50">
                      <span className="text-slate-500">Batas Kembali:</span>
                      <span className={`font-medium tabular-nums ${isTerlambat ? 'text-rose-600 font-semibold' : 'text-slate-700'}`}>
                        {pinjam.tanggal_kembali_rencana}
                      </span>
                    </div>
                  </div>
                );
              })
            )}
          </div>
        </article>

        {/* Barang Baru */}
        <article className="min-w-0 overflow-hidden rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md">
          <div className="bg-slate-900 p-4 sm:p-5 border-b border-slate-800 flex items-center justify-between gap-3">
            <div className="min-w-0">
              <h2 className="text-sm sm:text-base font-bold text-white">Barang Baru</h2>
              <p className="mt-0.5 text-xs text-slate-300 truncate">Inventaris yang baru ditambahkan.</p>
            </div>
            <a
              href={routes.barangIndex}
              className="group inline-flex items-center gap-1 shrink-0 text-xs sm:text-sm font-semibold text-indigo-300 hover:text-white p-1 transition-colors"
            >
              Lihat semua <span className="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
            </a>
          </div>

          <div className="divide-y divide-slate-100">
            {barangTerbaru.length === 0 ? (
              <div className="px-4 py-8 text-center text-xs sm:text-sm text-slate-500">
                Belum ada barang baru yang ditambahkan.
              </div>
            ) : (
              barangTerbaru.map((brg: any) => {
                const color = getConditionColor(brg.kondisi);

                return (
                  <div key={brg.id} className="p-3.5 sm:p-4 space-y-2.5 transition-colors hover:bg-slate-50/60">
                    <div className="flex items-start justify-between gap-2">
                      <div className="min-w-0 flex-1">
                        <p className="font-semibold text-slate-900 text-sm truncate">{brg.nama_barang}</p>
                        <p className="font-mono text-xs text-slate-500 mt-0.5 truncate">{brg.kode_barang}</p>
                      </div>
                      <span className={`${color.badge} inline-flex shrink-0 items-center rounded-full border px-2 py-0.5 text-[11px] font-semibold shadow-sm`}>
                        {brg.kondisi}
                      </span>
                    </div>
                    <div className="grid grid-cols-2 gap-2 text-xs pt-1 border-t border-slate-50">
                      <div className="truncate">
                        <span className="text-slate-400">Kategori: </span>
                        <span className="font-medium text-slate-700">{brg.kategori}</span>
                      </div>
                      <div className="text-right truncate">
                        <span className="text-slate-400">Stok: </span>
                        <span className="font-medium text-slate-900">{brg.jumlah} {brg.satuan}</span>
                      </div>
                    </div>
                  </div>
                );
              })
            )}
          </div>
        </article>
      </section>
    </div>
  );
};

// Skeleton Placeholder State dengan struktur item (avatar/icon bulat + 2 bar teks + badge/button)
function AdminDashboardSkeleton() {
  return (
    <div className="space-y-4 sm:space-y-6">
      {/* Banner Skeleton */}
      <div className="rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-5 sm:p-6 space-y-3">
        <Skeleton className="h-4 w-36 rounded-md" />
        <Skeleton className="h-7 w-56 rounded-lg" />
        <Skeleton className="h-4 w-full max-w-md rounded-md" />
        <div className="flex gap-3 pt-2">
          <Skeleton className="h-10 w-32 rounded-xl" />
          <Skeleton className="h-10 w-28 rounded-xl" />
        </div>
      </div>

      {/* Grid 8 Cards Skeleton */}
      <div className="space-y-3">
        <Skeleton className="h-4 w-44 rounded-md" />
        <div className="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-4">
          {Array.from({ length: 8 }).map((_, i) => (
            <div key={i} className="rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 flex items-center justify-between">
              <div className="space-y-2 flex-1 pr-3">
                <Skeleton className="h-3 w-20 rounded" />
                <Skeleton className="h-7 w-12 rounded-md" />
              </div>
              <Skeleton className="h-10 w-10 rounded-xl shrink-0" />
            </div>
          ))}
        </div>
      </div>

      {/* Grid 2 Visuals Skeleton */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <div className="rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white overflow-hidden space-y-4">
          <div className="bg-slate-900 p-4">
            <Skeleton className="h-5 w-44 rounded bg-slate-700" />
          </div>
          <div className="p-4 sm:p-5 space-y-4">
            {Array.from({ length: 4 }).map((_, i) => (
              <div key={i} className="space-y-2">
                <div className="flex justify-between">
                  <Skeleton className="h-3.5 w-24 rounded" />
                  <Skeleton className="h-3.5 w-16 rounded" />
                </div>
                <Skeleton className="h-2.5 w-full rounded-full" />
              </div>
            ))}
          </div>
        </div>

        <div className="rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white overflow-hidden space-y-4">
          <div className="bg-slate-900 p-4">
            <Skeleton className="h-5 w-44 rounded bg-slate-700" />
          </div>
          <div className="p-4 sm:p-5 space-y-4">
            {Array.from({ length: 4 }).map((_, i) => (
              <div key={i} className="space-y-2">
                <div className="flex justify-between">
                  <Skeleton className="h-3.5 w-24 rounded" />
                  <Skeleton className="h-3.5 w-16 rounded" />
                </div>
                <Skeleton className="h-2.5 w-full rounded-full" />
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Aktivitas List Skeleton (Sesuai Struktur Particle Snippet yang Diminta: size-10 rounded-full + flex-1 column + button skeleton) */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <div className="rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white overflow-hidden">
          <div className="bg-slate-900 p-4 flex items-center justify-between">
            <Skeleton className="h-5 w-36 rounded bg-slate-700" />
            <Skeleton className="h-4 w-16 rounded bg-slate-700" />
          </div>
          <div className="p-4 sm:p-5 space-y-4 divide-y divide-slate-100">
            {Array.from({ length: 3 }).map((_, i) => (
              <div key={i} className="flex w-full items-center gap-4 pt-3.5 first:pt-0">
                <Skeleton className="h-10 w-10 rounded-full shrink-0" />
                <div className="flex flex-1 flex-col">
                  <Skeleton className="my-0.5 h-4 w-3/4 max-w-[216px]" />
                  <div className="flex w-full max-w-[216px] items-center gap-1">
                    <Skeleton className="my-0.5 h-3.5 w-1/2" />
                    <Skeleton className="my-0.5 h-3.5 w-1/2" />
                  </div>
                </div>
                <Skeleton className="h-6 w-16 rounded-md shrink-0" />
              </div>
            ))}
          </div>
        </div>

        <div className="rounded-xl sm:rounded-2xl border border-slate-200/90 bg-white overflow-hidden">
          <div className="bg-slate-900 p-4 flex items-center justify-between">
            <Skeleton className="h-5 w-36 rounded bg-slate-700" />
            <Skeleton className="h-4 w-16 rounded bg-slate-700" />
          </div>
          <div className="p-4 sm:p-5 space-y-4 divide-y divide-slate-100">
            {Array.from({ length: 3 }).map((_, i) => (
              <div key={i} className="flex w-full items-center gap-4 pt-3.5 first:pt-0">
                <Skeleton className="h-10 w-10 rounded-full shrink-0" />
                <div className="flex flex-1 flex-col">
                  <Skeleton className="my-0.5 h-4 w-3/4 max-w-[216px]" />
                  <div className="flex w-full max-w-[216px] items-center gap-1">
                    <Skeleton className="my-0.5 h-3.5 w-1/2" />
                    <Skeleton className="my-0.5 h-3.5 w-1/2" />
                  </div>
                </div>
                <Skeleton className="h-6 w-16 rounded-md shrink-0" />
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

