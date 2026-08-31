import React, { useState } from 'react';
import { Barang } from '../types';

interface WelcomeAppProps {
  title?: string;
  user?: {
    name: string;
    role: string;
  } | null;
}

export const WelcomeApp: React.FC<WelcomeAppProps> = ({
  title = 'Sistem Peminjaman Laboratorium TKJ',
  user,
}) => {
  const [activeTab, setActiveTab] = useState<'overview' | 'features' | 'sample_items'>('overview');
  const [searchQuery, setSearchQuery] = useState('');

  const sampleItems: Barang[] = [
    {
      id: 1,
      kode_barang: 'LAB-TKJ-001',
      nama_barang: 'MikroTik RouterBOARD RB750Gr3',
      kategori: 'Networking',
      kondisi: 'baik',
      status: 'tersedia',
      stok: 12,
      lokasi: 'Rak Jaringan 01',
      deskripsi: 'Router Gigabit 5-port untuk praktikum routing dan manajemen bandwidth.',
    },
    {
      id: 2,
      kode_barang: 'LAB-TKJ-002',
      nama_barang: 'Cisco Catalyst 2960 Switch 24-Port',
      kategori: 'Switching',
      kondisi: 'baik',
      status: 'tersedia',
      stok: 6,
      lokasi: 'Rack Server Utama',
      deskripsi: 'Managed switch enterprise untuk konfigurasi VLAN, VTP, dan STP.',
    },
    {
      id: 3,
      kode_barang: 'LAB-TKJ-003',
      nama_barang: 'Fusion Splicer Fiber Optic',
      kategori: 'Fiber Optic',
      kondisi: 'baik',
      status: 'dipinjam',
      stok: 2,
      lokasi: 'Lemari Khusus FO',
      deskripsi: 'Penyambung core fiber optic presisi tinggi untuk praktikum FTTX.',
    },
    {
      id: 4,
      kode_barang: 'LAB-TKJ-004',
      nama_barang: 'Crimping Tool RJ45 & LAN Tester Kit',
      kategori: 'Tools & Perkakas',
      kondisi: 'rusak_ringan',
      status: 'maintenance',
      stok: 15,
      lokasi: 'Toolbox A3',
      deskripsi: 'Peralatan terminasi kabel UTP Cat5e/Cat6 dan pengujian kontinuitas.',
    },
  ];

  const filteredItems = sampleItems.filter((item) =>
    item.nama_barang.toLowerCase().includes(searchQuery.toLowerCase()) ||
    item.kode_barang.toLowerCase().includes(searchQuery.toLowerCase()) ||
    item.kategori.toLowerCase().includes(searchQuery.toLowerCase())
  );

  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 font-sans antialiased selection:bg-indigo-500 selection:text-white p-4 sm:p-8">
      {/* Background glow decorations */}
      <div className="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div className="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl"></div>
        <div className="absolute top-1/3 -right-40 w-96 h-96 bg-purple-600/15 rounded-full blur-3xl"></div>
        <div className="absolute -bottom-40 left-1/3 w-96 h-96 bg-cyan-600/15 rounded-full blur-3xl"></div>
      </div>

      <div className="max-w-6xl mx-auto space-y-8">
        {/* Header Bar */}
        <header className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-800/80">
          <div className="flex items-center gap-3">
            <div className="h-12 w-12 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/25">
              <svg className="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
              </svg>
            </div>
            <div>
              <h1 className="text-xl font-bold tracking-tight text-white">{title}</h1>
              <p className="text-xs text-indigo-300 font-medium">Powered by React 19 + TypeScript + Vite</p>
            </div>
          </div>

          <div className="flex items-center gap-3">
            {user ? (
              <div className="px-3.5 py-1.5 rounded-full bg-slate-900/90 border border-slate-800 text-xs text-slate-300 flex items-center gap-2">
                <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>{user.name}</span>
                <span className="text-slate-500">|</span>
                <span className="uppercase text-[10px] font-semibold tracking-wider text-indigo-400">{user.role}</span>
              </div>
            ) : (
              <div className="flex items-center gap-2">
                <a
                  href="/login"
                  className="px-4 py-2 text-xs font-semibold text-slate-200 hover:text-white bg-slate-900/90 hover:bg-slate-800 border border-slate-800 rounded-xl transition-all shadow-sm"
                >
                  Masuk
                </a>
                <a
                  href="/katalog"
                  className="px-4 py-2 text-xs font-semibold text-white bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 rounded-xl transition-all shadow-md shadow-indigo-500/20"
                >
                  Lihat Katalog
                </a>
              </div>
            )}
          </div>
        </header>

        {/* Navigation Tabs */}
        <div className="flex gap-2 p-1 bg-slate-900/80 backdrop-blur-md rounded-2xl border border-slate-800/80 w-fit">
          <button
            onClick={() => setActiveTab('overview')}
            className={`px-4 py-2 text-xs font-medium rounded-xl transition-all ${
              activeTab === 'overview'
                ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30'
                : 'text-slate-400 hover:text-slate-200'
            }`}
          >
            Ringkasan Sistem
          </button>
          <button
            onClick={() => setActiveTab('features')}
            className={`px-4 py-2 text-xs font-medium rounded-xl transition-all ${
              activeTab === 'features'
                ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30'
                : 'text-slate-400 hover:text-slate-200'
            }`}
          >
            Fitur Utama
          </button>
          <button
            onClick={() => setActiveTab('sample_items')}
            className={`px-4 py-2 text-xs font-medium rounded-xl transition-all ${
              activeTab === 'sample_items'
                ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30'
                : 'text-slate-400 hover:text-slate-200'
            }`}
          >
            Live Item Preview ({filteredItems.length})
          </button>
        </div>

        {/* Tab 1: Overview */}
        {activeTab === 'overview' && (
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fadeIn">
            <div className="md:col-span-2 p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-slate-900/90 via-slate-900/60 to-slate-950/80 border border-slate-800/80 backdrop-blur-xl shadow-xl flex flex-col justify-between">
              <div className="space-y-4">
                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                  <span className="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                  React + TypeScript Integration Ready
                </span>
                <h2 className="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                  Manajemen Peminjaman Lab Cepat, Aman & Modern
                </h2>
                <p className="text-slate-400 text-sm leading-relaxed max-w-xl">
                  Sistem peminjaman dan inventarisasi peralatan laboratorium TKJ kini terintegrasi dengan komponen interaktif React dan keamanan tipe statis dari TypeScript.
                </p>
              </div>

              <div className="grid grid-cols-3 gap-4 pt-8 mt-6 border-t border-slate-800/60">
                <div>
                  <div className="text-2xl font-bold text-white">100%</div>
                  <div className="text-xs text-slate-400 mt-1">TypeScript Strict Mode</div>
                </div>
                <div>
                  <div className="text-2xl font-bold text-indigo-400">React 19</div>
                  <div className="text-xs text-slate-400 mt-1">Next-gen UI Engine</div>
                </div>
                <div>
                  <div className="text-2xl font-bold text-cyan-400">Vite 5</div>
                  <div className="text-xs text-slate-400 mt-1">Ultra-fast HMR</div>
                </div>
              </div>
            </div>

            {/* Quick Status Card */}
            <div className="p-6 rounded-3xl bg-slate-900/70 border border-slate-800/80 backdrop-blur-xl flex flex-col justify-between space-y-6">
              <div>
                <h3 className="text-sm font-bold text-slate-200 mb-4 flex items-center justify-between">
                  <span>Status Ekosistem</span>
                  <span className="w-2 h-2 rounded-full bg-emerald-400"></span>
                </h3>

                <ul className="space-y-3 text-xs">
                  <li className="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/60 border border-slate-800">
                    <span className="text-slate-400">TypeScript Config</span>
                    <span className="font-mono text-emerald-400 font-semibold">tsconfig.json OK</span>
                  </li>
                  <li className="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/60 border border-slate-800">
                    <span className="text-slate-400">Entry File</span>
                    <span className="font-mono text-cyan-400 font-semibold">app.tsx Active</span>
                  </li>
                  <li className="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/60 border border-slate-800">
                    <span className="text-slate-400">Vite React Plugin</span>
                    <span className="font-mono text-indigo-400 font-semibold">Configured</span>
                  </li>
                </ul>
              </div>

              <div className="p-4 rounded-2xl bg-indigo-950/40 border border-indigo-800/30">
                <p className="text-[11px] text-indigo-300 leading-normal">
                  Komponen React dapat langsung dipanggil di Blade template menggunakan container <code className="text-indigo-200 bg-indigo-900/50 px-1 py-0.5 rounded">id="app"</code> atau via data attribute.
                </p>
              </div>
            </div>
          </div>
        )}

        {/* Tab 2: Features */}
        {activeTab === 'features' && (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 animate-fadeIn">
            <div className="p-6 rounded-3xl bg-slate-900/80 border border-slate-800/80 hover:border-indigo-500/40 transition-all group">
              <div className="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
              </div>
              <h3 className="text-base font-bold text-white mb-2">QR Code Scanner</h3>
              <p className="text-xs text-slate-400 leading-relaxed">
                Scan cepat kode QR barang untuk memverifikasi peminjaman atau mengecek detail unit alat lab secara real-time.
              </p>
            </div>

            <div className="p-6 rounded-3xl bg-slate-900/80 border border-slate-800/80 hover:border-cyan-500/40 transition-all group">
              <div className="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
              </div>
              <h3 className="text-base font-bold text-white mb-2">Peminjaman & Approval</h3>
              <p className="text-xs text-slate-400 leading-relaxed">
                Pengajuan peminjaman oleh siswa/guru dengan approval langsung dari petugas lab atau administrator.
              </p>
            </div>

            <div className="p-6 rounded-3xl bg-slate-900/80 border border-slate-800/80 hover:border-amber-500/40 transition-all group">
              <div className="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <h3 className="text-base font-bold text-white mb-2">Manajemen Maintenance</h3>
              <p className="text-xs text-slate-400 leading-relaxed">
                Pencatatan perbaikan dan perawatan berkala alat lab untuk menjaga performa inventaris tetap optimal.
              </p>
            </div>
          </div>
        )}

        {/* Tab 3: Sample Items */}
        {activeTab === 'sample_items' && (
          <div className="space-y-4 animate-fadeIn">
            <div className="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
              <div className="relative flex-1 max-w-md">
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Cari alat (contoh: MikroTik, Cisco, Switch)..."
                  className="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-slate-800 rounded-2xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors"
                />
                <svg className="w-4 h-4 text-slate-500 absolute left-3.5 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
              <span className="text-xs text-slate-400 self-center">Menampilkan {filteredItems.length} barang</span>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              {filteredItems.map((item) => (
                <div key={item.id} className="p-5 rounded-2xl bg-slate-900/70 border border-slate-800/80 hover:border-slate-700 transition-all flex flex-col justify-between">
                  <div className="space-y-2">
                    <div className="flex items-center justify-between gap-2">
                      <span className="font-mono text-[11px] font-semibold text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded-md border border-indigo-500/20">
                        {item.kode_barang}
                      </span>
                      <span
                        className={`text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full ${
                          item.status === 'tersedia'
                            ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'
                            : item.status === 'dipinjam'
                            ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20'
                            : 'bg-rose-500/10 text-rose-400 border border-rose-500/20'
                        }`}
                      >
                        {item.status}
                      </span>
                    </div>
                    <h4 className="text-sm font-bold text-white">{item.nama_barang}</h4>
                    <p className="text-xs text-slate-400 line-clamp-2">{item.deskripsi}</p>
                  </div>

                  <div className="flex items-center justify-between pt-4 mt-4 border-t border-slate-800 text-[11px] text-slate-400">
                    <span>Lokasi: <strong className="text-slate-200">{item.lokasi}</strong></span>
                    <span>Stok: <strong className="text-indigo-300">{item.stok} unit</strong></span>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
