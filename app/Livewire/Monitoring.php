<?php

namespace App\Livewire;

use App\Models\Dokumen;
use App\Models\Kriteria;
use App\Models\Workflow;
use Livewire\Component;

class Monitoring extends Component
{
    public $showModal = false;
    public $showAiGuide = false;
    public $selectedKriteriaId = null;
    public $aiGuidance = [];
    public $existingTasks = [];

    public $form = [
        'peringkat_saat_ini' => '',
        'tanggal_kadaluarsa' => '',
        'target_submit' => '',
        'status_akreditasi' => 'aktif',
        'target_peringkat' => '',
    ];

    public function openEditModal()
    {
        $prodiId = session('selected_prodi_id');
        $prodi = \App\Models\Prodi::find($prodiId) ?? \App\Models\Prodi::first();
        
        $this->form = [
            'peringkat_saat_ini' => $prodi->peringkat_saat_ini,
            'tanggal_kadaluarsa' => $prodi->tanggal_kadaluarsa,
            'target_submit' => $prodi->target_submit,
            'status_akreditasi' => $prodi->status_akreditasi ?: 'aktif',
            'target_peringkat' => $prodi->target_peringkat,
        ];
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function saveRoadmap()
    {
        $prodiId = session('selected_prodi_id');
        $prodi = \App\Models\Prodi::find($prodiId) ?? \App\Models\Prodi::first();
        
        $prodi->update($this->form);
        
        $this->showModal = false;
        
        $this->dispatch('swal:success', [
            'title' => 'Saved!',
            'text' => 'Roadmap Strategis berhasil diperbarui.',
        ]);
    }

    public function openAiGuide($id)
    {
        $this->selectedKriteriaId = $id;
        $k = \App\Models\Kriteria::find($id);
        
        $prodiId = session('selected_prodi_id');
        $prodi = \App\Models\Prodi::find($prodiId) ?? \App\Models\Prodi::first();
        
        // 1. Cek apakah sudah ada di database (Ingatan Jangka Panjang)
        $guidance = \App\Models\AiGuidance::where('prodi_id', $prodi->id)
            ->where('kriteria_id', $id)
            ->first();

        if ($guidance) {
            $this->aiGuidance = $guidance->guidance;
        } else {
            // 2. Jika belum ada, baru panggil Real AI Groq
            $groq = new \App\Services\GroqService();
            $this->aiGuidance = $groq->getAccreditationGuidance($k, $prodi);
            
            // 3. Simpan ke database agar tidak hilang
            \App\Models\AiGuidance::create([
                'prodi_id' => $prodi->id,
                'kriteria_id' => $id,
                'guidance' => $this->aiGuidance,
                'last_generated_at' => now(),
            ]);
        }
        
        $this->showAiGuide = true;
        $this->updateExistingTasks();
    }

    public function updateExistingTasks()
    {
        $prodiId = session('selected_prodi_id');
        $prodi = \App\Models\Prodi::find($prodiId) ?? \App\Models\Prodi::first();

        $this->existingTasks = \App\Models\Dokumen::where('prodi_id', $prodi->id)
            ->where('kriteria_id', $this->selectedKriteriaId)
            ->pluck('nama_file')
            ->toArray();
    }

    public function refreshAiGuide($id)
    {
        $k = \App\Models\Kriteria::find($id);
        $prodiId = session('selected_prodi_id');
        $prodi = \App\Models\Prodi::find($prodiId) ?? \App\Models\Prodi::first();

        $groq = new \App\Services\GroqService();
        $this->aiGuidance = $groq->getAccreditationGuidance($k, $prodi);

        \App\Models\AiGuidance::updateOrCreate(
            ['prodi_id' => $prodi->id, 'kriteria_id' => $id],
            ['guidance' => $this->aiGuidance, 'last_generated_at' => now()]
        );

        $this->updateExistingTasks();
    }

    public function closeAiGuide()
    {
        $this->showAiGuide = false;
    }

    private function getAiMapping($kode, $lamType = 'ban-pt')
    {
        $kode = strtoupper($kode);
        
        $map = [
            'ban-pt' => [
                'A.'  => ['SK Izin Operasional Prodi & SK Pendirian PT', 'Buku Profil UPPS (Sejarah & Pimpinan)', 'Bagan Struktur Organisasi UPPS', 'Sertifikat Akreditasi Institusi Terakhir'],
                'B.1' => ['File PDF SK Penetapan Visi Misi', 'Foto Dokumentasi Banner/Baliho Visi Misi', 'Laporan Survey Pemahaman VMTS oleh Dosen & Mhs', 'Sertifikat Akreditasi Prodi Sebelumnya'],
                'B.2' => ['Manual Mutu SPMI (Hardcopy/Digital)', 'Laporan Audit Mutu Internal (AMI)', 'Dokumen MoU Kerjasama (Aktif & Legal)', 'SOP Rekrutmen & Seleksi Pengelola'],
                'B.3' => ['Laporan Statistik Calon Mahasiswa Baru', 'Buku Panduan Akademik Mahasiswa', 'Scan Sertifikat Juara Lomba Mahasiswa', 'Laporan Layanan Beasiswa & Kesehatan'],
                'B.4' => ['Scan Ijazah & Serdos Seluruh DTPS', 'SK Jafung (Asisten Ahli s/d Guru Besar)', 'Laporan Beban Kerja Dosen (BKD/EWMP)', 'Sertifikat Kompetensi / Keahlian Dosen'],
                'B.5' => ['Laporan Keuangan Diaudit / Opini Akuntan', 'Sertifikat Sertifikasi Sarana (Lab/Gedung)', 'Katalog Koleksi Perpustakaan Digital', 'Bukti Anggaran Investasi Alat Laboratorium'],
                'B.6' => ['Buku Kurikulum OBE (Outcome Based Education)', 'Kumpulan RPS (Rencana Pembelajaran Semester)', 'Laporan Hasil Survey Kepuasan Perkuliahan', 'Dokumen Kerjasama Magang / Prakerin'],
                'B.7' => ['Daftar Roadmap Penelitian Dosen', 'Scan Kontrak Hibah Penelitian (Internal/Dikti)', 'Bukti Sitasi (Capture Google Scholar/Scopus)', 'Sertifikat HKI / Hak Cipta Karya Dosen'],
                'C.'  => ['Buku Dokumen Rencana Strategis (Renstra)', 'Analisis SWOT & Strategi Pengembangan', 'Laporan Keberlanjutan Finansial Prodi'],
            ],
            'lam-infokom' => [
                'C1' => ['Buku Visi Misi Bidang IT', 'Laporan SWOT Strategi Digital', 'Bukti Benchmarking Kurikulum IT Intal'],
                'C3' => ['Data Statistik Pendaftar Online', 'Sertifikat Lomba Hackathon/Coding Mahasiswa', 'Berita Acara Seleksi Mhs Baru', 'Sistem Informasi Layanan Mahasiswa'],
                'C4' => ['Sertifikat IT Professional (Cisco, Mikrotik, AWS)', 'Bukti Keanggotaan APTIKOM', 'Profil Dosen dengan Kepakaran IT Spesifik', 'SK Penugasan Laboran/Teknisi'],
                'C6' => ['Dokumen Kurikulum Berbasis Computing (ACM/IEEE)', 'Logbook Praktikum di Laboratorium IT', 'Daftar Aplikasi LMS yang Digunakan', 'Bukti Tracer Study Lulusan di Perusahaan IT'],
                'C7' => ['Publikasi Jurnal Informatika (SINTA/Scopus)', 'Sertifikat HKI Software / Program Komputer', 'Laporan Produk Inovasi IT Mahasiswa & Dosen', 'Bukti Kerjasama Riset dengan Tech-Company'],
            ],
            'lamemba' => [
                'A.'  => ['Profil UPPS (Sejarah, Visi UPPS)', 'Statuta / Aturan Tata Kelola Institusi', 'SIM Terintegrasi Fakultas', 'Laporan Kinerja UPPS'],
                'B.1' => ['Buku Dokumen VMTS', 'Renstra & Renop Fakultas/Prodi', 'Laporan Hasil Audit VMTS', 'Bukti Sosialisasi VMTS Eksternal'],
                'B.2' => ['SK Pengelola UPPS', 'SOP Tata Pamong & Tata Kelola', 'MoU & MoA Kerjasama Internasional', 'Laporan Analisis SWOT Strategis'],
                'B.3' => ['Laporan Seleksi & Penerimaan Mahasiswa', 'Panduan Pelayanan & Beasiswa Mhs', 'Sertifikat Prestasi Mahasiswa (Nasional)', 'Bukti Layanan Bimbingan Karir/Alumni'],
                'B.4' => ['Daftar Dosen Tetap (DTPS) & Ijazah', 'Sertifikat Pendidik & Profesi (CFA/CPA)', 'SK Homebase & EWMP Dosen', 'Bukti Rekognisi/Kepakaran Dosen'],
                'B.5' => ['Laporan Audit Keuangan (WTP)', 'Sertifikat Kelayakan Sarpras', 'Logbook Perawatan Fasilitas', 'Dokumen Investasi Dana Inovasi'],
                'B.6' => ['Kurikulum OBE / MBKM Bisnis', 'RPS Lengkap & Bahan Ajar Digital', 'Laporan AMI Bidang Pendidikan', 'Bukti Kerja Lapangan / Magang Industri'],
                'B.7' => ['Roadmap Penelitian & PkM Bidang Bisnis', 'Kontrak Hibah Penelitian Dana Eksternal', 'Bukti Sitasi & Jurnal Bereputasi', 'Sertifikat Hak Cipta / Buku Ber-ISBN'],
                'C.'  => ['Laporan Analisis Strategi Pengembangan', 'Analisis Keberlanjutan Prodi (Sustainability)', 'Rencana Tindak Lanjut Perbaikan Mutu'],
            ]
        ];

        $selectedMap = $map[$lamType] ?? $map['ban-pt'];

        // 1. Try Exact Match First
        if (isset($selectedMap[$kode])) {
            return $selectedMap[$kode];
        }

        // 2. Try Prefix Match (e.g., B.1 matching B.)
        foreach($selectedMap as $key => $docs) {
            if (str_starts_with($kode, $key)) return $docs;
        }

        return [
            'Laporan Pelaksanaan Kegiatan Strategis ' . strtoupper($lamType), 
            'Surat Keputusan (SK) Pengangkatan / Penugasan Terkait', 
            'SOP Operasional Sesuai Standar ' . strtoupper($lamType), 
            'Bukti Capaian Indikator Kinerja Utama'
        ];
    }

    public function addAsTask($docName)
    {
        try {
            $prodiId = session('selected_prodi_id');
            $prodi = \App\Models\Prodi::find($prodiId) ?? \App\Models\Prodi::first();

            // Cek apakah dokumen dengan nama ini sudah ada di kriteria ini
            $exists = \App\Models\Dokumen::where('prodi_id', $prodi->id)
                ->where('kriteria_id', $this->selectedKriteriaId)
                ->where('nama_file', $docName)
                ->exists();

            if ($exists) {
                $this->dispatch('swal:warning', [
                    'title' => 'Sudah Ada!',
                    'text' => 'Draft dokumen ini sudah ada.',
                ]);
                return;
            }

            // Buat placeholder dokumen
            \App\Models\Dokumen::create([
                'prodi_id' => $prodi->id,
                'kriteria_id' => $this->selectedKriteriaId,
                'nama_file' => $docName,
                'file_path' => 'PENDING',
                'status' => 'draft',
                'user_id' => auth()->id() ?? 1 // Fallback ke ID 1 jika tidak login
            ]);

            $this->dispatch('swal:success', [
                'title' => 'Tugas Dibuat!',
                'text' => 'Draft [' . $docName . '] telah ditambahkan.',
            ]);

            $this->updateExistingTasks();

        } catch (\Exception $e) {
            \Log::error('Gagal membuat tugas: ' . $e->getMessage());
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $prodiId = session('selected_prodi_id');
        $prodi = \App\Models\Prodi::find($prodiId) ?? \App\Models\Prodi::first();
        $lamType = $prodi->lam_type ?? 'ban-pt';

        $workflows = Workflow::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        $kriterias = Kriteria::where('lam_type', $lamType)->withCount([
            'dokumens',
            'dokumens as draft_count' => fn($q) => $q->where('status', 'draft'),
            'dokumens as submitted_count' => fn($q) => $q->where('status', 'submitted'),
            'dokumens as review_count' => fn($q) => $q->where('status', 'review'),
            'dokumens as approved_count' => fn($q) => $q->where('status', 'approved'),
            'dokumens as revision_count' => fn($q) => $q->where('status', 'revision'),
        ])->orderBy('kode', 'asc')->get();

        $statusSummary = [
            'draft' => Dokumen::where('status', 'draft')->count(),
            'submitted' => Dokumen::where('status', 'submitted')->count(),
            'review' => Dokumen::where('status', 'review')->count(),
            'approved' => Dokumen::where('status', 'approved')->count(),
            'revision' => Dokumen::where('status', 'revision')->count(),
        ];
        $totalDocs = array_sum($statusSummary);

        $notifications = \App\Models\Notification::latest()->limit(5)->get();
        $selectedCriterion = $this->selectedKriteriaId ? \App\Models\Kriteria::find($this->selectedKriteriaId) : null;

        return view('livewire.monitoring', [
            'workflows' => $workflows,
            'kriterias' => $kriterias,
            'statusSummary' => $statusSummary,
            'totalDocs' => $totalDocs,
            'notifications' => $notifications,
            'prodi' => $prodi,
            'selectedCriterion' => $selectedCriterion,
            'prodis' => \App\Models\Prodi::withCount('dokumens')->get()
        ])->layout('layouts.app');
    }
}
