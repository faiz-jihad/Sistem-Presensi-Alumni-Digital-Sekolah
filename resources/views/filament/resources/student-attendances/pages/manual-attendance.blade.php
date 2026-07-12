<x-filament-panels::page>
<style>
    .ma-header {
        position: relative;
        overflow: hidden;
        border-radius: 1.25rem;
        background: linear-gradient(135deg, #1d4ed8 0%, #4f46e5 50%, #1e40af 100%);
        padding: 1.75rem 2rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 20px 40px -10px rgba(30, 64, 175, 0.4);
    }
    @media (min-width: 768px) {
        .ma-header { flex-direction: row; align-items: center; justify-content: space-between; }
    }
    .ma-header-deco1 {
        position: absolute; top: -3rem; right: -3rem;
        width: 10rem; height: 10rem; border-radius: 50%;
        background: rgba(255,255,255,0.08); pointer-events: none;
    }
    .ma-header-deco2 {
        position: absolute; bottom: -4rem; left: -2rem;
        width: 12rem; height: 12rem; border-radius: 50%;
        background: rgba(255,255,255,0.05); pointer-events: none;
    }
    .ma-header-left { display: flex; align-items: flex-start; gap: 1rem; position: relative; z-index: 1; }
    @media (min-width: 768px) { .ma-header-left { align-items: center; } }
    .ma-header-icon {
        width: 3.25rem; height: 3.25rem; flex-shrink: 0;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 1rem;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    .ma-header-title {
        font-size: 1.4rem; font-weight: 800; color: white;
        letter-spacing: -0.02em; line-height: 1.2;
    }
    .ma-header-desc {
        font-size: 0.85rem; color: rgba(219,234,254,0.9);
        margin-top: 0.35rem; max-width: 38rem; line-height: 1.5;
    }
    .ma-header-badge {
        position: relative; z-index: 1;
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: rgba(0,0,0,0.18);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 0.75rem;
        padding: 0.6rem 1rem;
        font-size: 0.75rem; font-weight: 600; color: rgba(255,255,255,0.9);
        white-space: nowrap;
    }
    .ma-badge-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #fbbf24; flex-shrink: 0;
        animation: ma-pulse 1.5s infinite;
    }
    @keyframes ma-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

    .ma-card {
        position: relative;
        border-radius: 1.25rem;
        background: white;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .dark .ma-card { background: #18181b; border-color: #27272a; }
    .ma-card-topbar {
        height: 4px;
        background: linear-gradient(to right, #3b82f6, #6366f1, #a855f7);
    }
    .ma-card-body { padding: 1.75rem 2rem; }

    .summary-bar {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        margin-bottom: 16px;
    }
    @media (max-width: 640px) {
        .summary-bar { grid-template-columns: repeat(3, 1fr); }
    }
    .summary-item {
        padding: 12px;
        border-radius: 10px;
        text-align: center;
        border: 1px solid transparent;
    }
    .summary-item-present  { background: #ecfdf5; border-color: #a7f3d0; }
    .summary-item-late     { background: #fffbeb; border-color: #fde68a; }
    .summary-item-sick     { background: #eff6ff; border-color: #bae6fd; }
    .summary-item-izin     { background: #fffbeb; border-color: #fde68a; }
    .summary-item-absent   { background: #fef2f2; border-color: #fca5a5; }

    .dark .summary-item-present  { background: rgba(6,95,70,0.15);  border-color: rgba(6,95,70,0.4);  }
    .dark .summary-item-late     { background: rgba(146,64,14,0.15); border-color: rgba(146,64,14,0.4); }
    .dark .summary-item-sick     { background: rgba(7,89,133,0.15);  border-color: rgba(7,89,133,0.4);  }
    .dark .summary-item-izin     { background: rgba(146,64,14,0.15); border-color: rgba(146,64,14,0.4); }
    .dark .summary-item-absent   { background: rgba(153,27,27,0.15); border-color: rgba(153,27,27,0.4); }

    .summary-item-present .summary-number  { color: #065f46; }
    .summary-item-late .summary-number     { color: #92400e; }
    .summary-item-sick .summary-number     { color: #075985; }
    .summary-item-izin .summary-number     { color: #92400e; }
    .summary-item-absent .summary-number   { color: #991b1b; }

    .dark .summary-item-present .summary-number  { color: #34d399; }
    .dark .summary-item-late .summary-number     { color: #fbbf24; }
    .dark .summary-item-sick .summary-number     { color: #38bdf8; }
    .dark .summary-item-izin .summary-number     { color: #fbbf24; }
    .dark .summary-item-absent .summary-number   { color: #f87171; }

    .ma-form-wrap {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
    }
    .dark .ma-form-wrap { background: rgba(39,39,42,0.4); border-color: rgba(63,63,70,0.5); }

    .ma-divider {
        border: none; border-top: 1px solid #f1f5f9;
        margin: 1.5rem 0 0;
    }
    .dark .ma-divider { border-top-color: rgba(39,39,42,0.8); }

    .ma-summary-header { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; }
    .ma-summary-icon {
        width: 2rem; height: 2rem;
        background: #eef2ff; border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center;
        color: #4f46e5; flex-shrink: 0;
    }
    .dark .ma-summary-icon { background: rgba(79,70,229,0.2); color: #a5b4fc; }
    .ma-summary-title {
        font-size: 0.78rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.08em;
        color: #374151;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .dark .ma-summary-title { color: #e5e7eb; }
    .ma-summary-badge {
        background: #e0e7ff; color: #4338ca;
        padding: 0.18rem 0.65rem; border-radius: 999px;
        font-size: 0.7rem; font-weight: 600;
    }
    .dark .ma-summary-badge { background: rgba(79,70,229,0.25); color: #a5b4fc; }

    .ma-grid-5 {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.85rem;
    }
    @media (max-width: 767px) { .ma-grid-5 { grid-template-columns: repeat(2, 1fr); } }

    .ma-stat {
        border-radius: 1rem; padding: 1.1rem 0.75rem;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center; gap: 0.35rem;
        border: 1px solid transparent;
        transition: transform 0.2s, box-shadow 0.2s;
        text-align: center; cursor: default;
    }
    .ma-stat:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
    .ma-stat-hadir     { background: linear-gradient(135deg,#ecfdf5,#d1fae5); border-color: #a7f3d0; }
    .ma-stat-terlambat { background: linear-gradient(135deg,#fffbeb,#fef3c7); border-color: #fde68a; }
    .ma-stat-sakit     { background: linear-gradient(135deg,#faf5ff,#ede9fe); border-color: #ddd6fe; }
    .ma-stat-izin      { background: linear-gradient(135deg,#f0f9ff,#e0f2fe); border-color: #bae6fd; }
    .ma-stat-alpa      { background: linear-gradient(135deg,#fff1f2,#ffe4e6); border-color: #fecdd3; }

    .dark .ma-stat-hadir     { background: rgba(6,78,59,0.18);   border-color: rgba(6,95,70,0.3); }
    .dark .ma-stat-terlambat { background: rgba(120,53,15,0.18);  border-color: rgba(146,64,14,0.3); }
    .dark .ma-stat-sakit     { background: rgba(88,28,135,0.18);  border-color: rgba(107,33,168,0.3); }
    .dark .ma-stat-izin      { background: rgba(7,89,133,0.18);   border-color: rgba(14,116,144,0.3); }
    .dark .ma-stat-alpa      { background: rgba(127,29,29,0.18);  border-color: rgba(15,23,42,0.3); }

    .ma-stat-num { font-size: 2.25rem; font-weight: 900; line-height: 1; }
    .ma-stat-hadir     .ma-stat-num { color: #047857; }
    .ma-stat-terlambat .ma-stat-num { color: #b45309; }
    .ma-stat-sakit     .ma-stat-num { color: #7c3aed; }
    .ma-stat-izin      .ma-stat-num { color: #0369a1; }
    .ma-stat-alpa      .ma-stat-num { color: #be123c; }
    .dark .ma-stat-hadir     .ma-stat-num { color: #34d399; }
    .dark .ma-stat-terlambat .ma-stat-num { color: #fbbf24; }
    .dark .ma-stat-sakit     .ma-stat-num { color: #c084fc; }
    .dark .ma-stat-izin      .ma-stat-num { color: #38bdf8; }
    .dark .ma-stat-alpa      .ma-stat-num { color: #fb7185; }

    .ma-stat-label {
        font-size: 0.65rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.75;
    }
    .ma-stat-hadir     .ma-stat-label { color: #065f46; }
    .ma-stat-terlambat .ma-stat-label { color: #92400e; }
    .ma-stat-sakit     .ma-stat-label { color: #6b21a8; }
    .ma-stat-izin      .ma-stat-label { color: #075985; }
    .ma-stat-alpa      .ma-stat-label { color: #9f1239; }
    .dark .ma-stat-hadir     .ma-stat-label { color: #6ee7b7; }
    .dark .ma-stat-terlambat .ma-stat-label { color: #fcd34d; }
    .dark .ma-stat-sakit     .ma-stat-label { color: #d8b4fe; }
    .dark .ma-stat-izin      .ma-stat-label { color: #7dd3fc; }
    .dark .ma-stat-alpa      .ma-stat-label { color: #fda4af; }

    .ma-actions {
        display: flex; flex-wrap: wrap; gap: 0.75rem;
        justify-content: space-between; align-items: center;
        padding-top: 1.5rem; margin-top: 1.5rem;
        border-top: 1px solid #f1f5f9;
    }
    .dark .ma-actions { border-top-color: rgba(39,39,42,0.8); }

    /* Circular Quick Attendance Buttons with precise CSS */
    .ma-btn-status {
        width: 36px !important;
        height: 36px !important;
        border-radius: 50% !important;
        border: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        flex-shrink: 0 !important;
        transition: all 120ms ease-in-out !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
        padding: 0 !important;
    }
    .ma-btn-status:hover {
        transform: scale(1.08) !important;
    }
    .ma-btn-status svg {
        pointer-events: none !important;
    }

    .ma-btn-present-active { background-color: #22c55e !important; color: white !important; }
    .ma-btn-present-inactive { background-color: #f1f5f9 !important; color: #94a3b8 !important; }
    .dark .ma-btn-present-inactive { background-color: #27272a !important; color: #52525b !important; }

    .ma-btn-permission-active { background-color: #3b82f6 !important; color: white !important; }
    .ma-btn-permission-inactive { background-color: #f1f5f9 !important; color: #94a3b8 !important; }
    .dark .ma-btn-permission-inactive { background-color: #27272a !important; color: #52525b !important; }

    .ma-btn-sick-active { background-color: #f59e0b !important; color: white !important; }
    .ma-btn-sick-inactive { background-color: #f1f5f9 !important; color: #94a3b8 !important; }
    .dark .ma-btn-sick-inactive { background-color: #27272a !important; color: #52525b !important; }

    .ma-btn-absent-active { background-color: #ef4444 !important; color: white !important; }
    .ma-btn-absent-inactive { background-color: #f1f5f9 !important; color: #94a3b8 !important; }
    .dark .ma-btn-absent-inactive { background-color: #27272a !important; color: #52525b !important; }
</style>

<div style="display:flex;flex-direction:column;gap:1.5rem;">

    {{-- Header Banner --}}
    <div class="ma-header">
        <div class="ma-header-deco1"></div>
        <div class="ma-header-deco2"></div>

        <div class="ma-header-left">
            <div class="ma-header-icon">
                <x-heroicon-o-clipboard-document-list style="width:26px;height:26px;color:white;" />
            </div>
            <div>
                <div class="ma-header-title">Presensi Manual Kelas</div>
                <div class="ma-header-desc">
                    Pilih kelas dan tanggal, lalu muat daftar siswa untuk mengisi kehadiran.
                    Notifikasi WhatsApp akan <strong style="color:white;">dikirim otomatis</strong> ke orang tua saat disimpan.
                </div>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 10px; min-width: 250px;">
            <div class="ma-header-badge">
                <span class="ma-badge-dot"></span>
                Status Sakit/Izin butuh verifikasi
            </div>

            {{-- Info Box --}}
            <div style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 12px; padding: 12px 16px; color: white;">
                <p class="text-xs font-semibold" style="margin: 0; color: #fef08a;">Info Sistem</p>
                <ul class="text-xs space-y-1 list-disc list-inside" style="margin: 4px 0 0; padding: 0; opacity: 0.95;">
                    <li>WA Otomatis dikirim ke orang tua</li>
                    <li>Sakit & Izin perlu disetujui</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="ma-card" x-data="{
        attendances: @entangle('attendances'),
        studentList: @entangle('studentList'),
        setStudentStatus(studentId, status) {
            let index = this.attendances.findIndex(item => item.student_id == studentId);
            if (index !== -1) {
                this.attendances[index].status = status;
            } else {
                this.attendances.push({
                    student_id: studentId,
                    status: status,
                    note: ''
                });
            }
        },
        getSummary() {
            let summary = { present: 0, late: 0, sick: 0, permission: 0, absent: 0, total: 0 };
            this.attendances.forEach(item => {
                let status = item.status || 'present';
                if (summary.hasOwnProperty(status)) {
                    summary[status]++;
                }
                summary.total++;
            });
            return summary;
        }
    }">
        <div class="ma-card-topbar"></div>

        <form wire:submit.prevent="submit" class="ma-card-body">
            <div style="display:flex;flex-direction:column;gap:1.75rem;">

                {{-- Form Inputs --}}
                <div class="ma-form-wrap">
                    {{ $this->form }}
                </div>

                {{-- Loading State --}}
                <div wire:loading wire:target="data.class_id" style="display: none; text-align: center; padding: 3rem 0; background: white; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);" class="dark:bg-zinc-900 dark:border-zinc-800">
                    <div style="display: inline-flex; align-items: center; justify-content: center;">
                        <svg class="animate-spin" style="width: 2.5rem; height: 2.5rem; color: #4f46e5;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <p style="margin-top: 16px; font-weight: 600; font-size: 0.95rem; color: #4f46e5;" class="dark:text-zinc-400">
                        Sedang memuat daftar siswa...
                    </p>
                    <p style="margin-top: 4px; font-size: 0.8rem; color: #9ca3af;">
                        Menghubungkan ke database sekolah
                    </p>
                </div>

                {{-- Student List Section --}}
                <div wire:loading.remove wire:target="data.class_id" x-show="studentList.length > 0" style="display: none; margin-top: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                    <h3 style="font-size: 1rem; font-weight: 700; color: #374151;" class="dark:text-zinc-300">
                        Daftar Siswa Kelas
                    </h3>

                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);" class="dark:bg-zinc-900 dark:border-zinc-800">
                        <div style="display: flex; flex-direction: column;" class="divide-y divide-gray-100 dark:divide-zinc-800">
                            <template x-for="(student, idx) in studentList" :key="student.id">
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #f1f5f9;" class="dark:border-zinc-800">

                                    {{-- Left Side: Student Info & History --}}
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <span style="font-weight: 600; font-size: 14px; color: #1f2937;" class="dark:text-zinc-200" x-text="student.name"></span>

                                        {{-- Attendance History dots --}}
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <span style="font-size: 11px; color: #9ca3af;">Riwayat:</span>
                                            <template x-if="student.history && student.history.length > 0">
                                                <div style="display: flex; gap: 4px;">
                                                    <template x-for="pastAtt in student.history">
                                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 14px; height: 14px; border-radius: 50%; color: white; font-size: 7px; font-weight: 800;"
                                                              :style="{
                                                                  backgroundColor: pastAtt.status === 'present' ? '#22c55e' :
                                                                                  pastAtt.status === 'late' ? '#f59e0b' :
                                                                                  pastAtt.status === 'permission' ? '#3b82f6' :
                                                                                  pastAtt.status === 'sick' ? '#06b6d4' : '#ef4444'
                                                              }"
                                                              :title="pastAtt.status"
                                                              x-text="pastAtt.status === 'present' ? '✓' :
                                                                     pastAtt.status === 'late' ? 'T' :
                                                                     pastAtt.status === 'permission' ? 'I' :
                                                                     pastAtt.status === 'sick' ? 'S' : 'A'">
                                                        </span>
                                                    </template>
                                                </div>
                                            </template>
                                            <template x-if="!student.history || student.history.length === 0">
                                                <span style="font-size: 11px; color: #9ca3af; font-style: italic;">Belum ada riwayat</span>
                                            </template>
                                        </div>

                                        {{-- Text Note Input (Alpine model) --}}
                                        <input type="text"
                                               x-model="attendances[idx].note"
                                               placeholder="Tambahkan catatan presensi (opsional)..."
                                               style="margin-top: 6px; display: block; width: 280px; border-radius: 6px; border: 1px solid #d1d5db; padding: 4px 8px; font-size: 11px; outline: none;"
                                               class="dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" />
                                    </div>
                                    {{-- Right Side: Quick Attendance Buttons (Alpine bound) --}}
                                    <div style="display: flex !important; gap: 12px !important; align-items: center !important; flex-shrink: 0 !important;">

                                        {{-- Hadir (present) --}}
                                        <button type="button"
                                                @click="setStudentStatus(student.id, 'present')"
                                                class="ma-btn-status"
                                                :class="attendances[idx] && attendances[idx].status === 'present' ? 'ma-btn-present-active' : 'ma-btn-present-inactive'"
                                                title="Hadir">
                                            <svg xmlns="http://www.w3.org/2000/svg" style="width:18px; height:18px;" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        {{-- Izin (permission) --}}
                                        <button type="button"
                                                @click="setStudentStatus(student.id, 'permission')"
                                                class="ma-btn-status"
                                                :class="attendances[idx] && attendances[idx].status === 'permission' ? 'ma-btn-permission-active' : 'ma-btn-permission-inactive'"
                                                title="Izin">
                                            <span style="font-weight: 800; font-size: 14px; font-family: sans-serif;">i</span>
                                        </button>

                                        {{-- Sakit (sick) --}}
                                        <button type="button"
                                                @click="setStudentStatus(student.id, 'sick')"
                                                class="ma-btn-status"
                                                :class="attendances[idx] && attendances[idx].status === 'sick' ? 'ma-btn-sick-active' : 'ma-btn-sick-inactive'"
                                                title="Sakit">
                                            <svg xmlns="http://www.w3.org/2000/svg" style="width:18px; height:18px;" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        {{-- Alpha (absent) --}}
                                        <button type="button"
                                                @click="setStudentStatus(student.id, 'absent')"
                                                class="ma-btn-status"
                                                :class="attendances[idx] && attendances[idx].status === 'absent' ? 'ma-btn-absent-active' : 'ma-btn-absent-inactive'"
                                                title="Alpa">
                                            <svg xmlns="http://www.w3.org/2000/svg" style="width:16px; height:16px;" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Empty state (Alpine bound) --}}
                <div wire:loading.remove wire:target="data.class_id" x-show="studentList.length === 0" style="text-align: center; padding: 2.5rem 0;" class="text-zinc-500">
                    <x-heroicon-o-users style="width: 48px; height: 48px; margin: 0 auto 12px; opacity: 0.5;" />
                    <p style="font-weight: 500;">Pilih kelas terlebih dahulu untuk memuat daftar siswa.</p>
                </div>

                {{-- Summary Section (Alpine bound) --}}
                <div wire:loading.remove wire:target="data.class_id" x-show="attendances.length > 0" style="display: none;">
                    <div class="ma-summary-header">
                        <div class="ma-summary-icon">
                            <x-heroicon-o-chart-pie style="width:16px;height:16px;" />
                        </div>
                        <div class="ma-summary-title">
                            Ringkasan Kehadiran
                            <span class="ma-summary-badge" x-text="getSummary().total + ' Siswa'"></span>
                        </div>
                    </div>

                    <div class="ma-grid-5">
                        <div class="ma-stat ma-stat-hadir">
                            <x-heroicon-o-check-circle style="width:24px;height:24px;color:#047857;" class="ma-stat-icon" />
                            <span class="ma-stat-num" x-text="getSummary().present"></span>
                            <span class="ma-stat-label">Hadir</span>
                        </div>
                        <div class="ma-stat ma-stat-terlambat">
                            <x-heroicon-o-clock style="width:24px;height:24px;color:#b45309;" class="ma-stat-icon" />
                            <span class="ma-stat-num" x-text="getSummary().late"></span>
                            <span class="ma-stat-label">Terlambat</span>
                        </div>
                        <div class="ma-stat ma-stat-sakit">
                            <x-heroicon-o-heart style="width:24px;height:24px;color:#7c3aed;" class="ma-stat-icon" />
                            <span class="ma-stat-num" x-text="getSummary().sick"></span>
                            <span class="ma-stat-label">Sakit</span>
                        </div>
                        <div class="ma-stat ma-stat-izin">
                            <x-heroicon-o-document-check style="width:24px;height:24px;color:#0369a1;" class="ma-stat-icon" />
                            <span class="ma-stat-num" x-text="getSummary().permission"></span>
                            <span class="ma-stat-label">Izin</span>
                        </div>
                        <div class="ma-stat ma-stat-alpa">
                            <x-heroicon-o-x-circle style="width:24px;height:24px;color:#be123c;" class="ma-stat-icon" />
                            <span class="ma-stat-num" x-text="getSummary().absent"></span>
                            <span class="ma-stat-label">Alpa</span>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div wire:loading.remove wire:target="data.class_id" class="ma-actions">
                    <x-filament::button
                        @click.prevent="attendances.forEach(item => item.status = 'present')"
                        color="gray"
                        icon="heroicon-m-arrow-path"
                        outlined
                    >
                        Reset Semua Hadir
                    </x-filament::button>

                    <x-filament::button
                        type="submit"
                        color="primary"
                        icon="heroicon-m-check-circle"
                    >
                        Simpan Presensi &amp; Kirim Notif WA
                    </x-filament::button>
                </div>

            </div>
        </form>
    </div>

</div>

<x-filament-actions::modals />
</x-filament-panels::page>