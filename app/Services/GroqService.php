<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.groq.key');
    }

    public function getAccreditationGuidance($kriteria, $prodi, $limit = 5)
    {
        if (!$this->apiKey) {
            return ['API Key Groq Belum Terpasang di .env'];
        }

        try {
            $systemPrompt = "Anda adalah Asesor Senior Akreditasi Perguruan Tinggi (LAM/BAN-PT). 
            Tugas Anda adalah memberikan DAFTAR CHECKLIST FILE FISIK (E-Document) yang sangat spesifik dan konkret yang harus disiapkan oleh Program Studi untuk kriteria tertentu.
            Jangan berikan kalimat pengantar. Langsung berikan daftar file dalam format JSON ARRAY string murni.
            Contoh output: [\"SK Izin Operasional\", \"Buku Kurikulum OBE\", \"Sertifikat Serdos\"]";

            $userPrompt = "Kriteria: {$kriteria->kode} - {$kriteria->nama}. 
            Program Studi: {$prodi->nama}. 
            Standar Akreditasi: {$prodi->lam_type}.
            Berikan daftar {$limit} file bukti fisik paling krusial untuk meraih Skor 4 (Unggul).";

            $response = Http::withToken($this->apiKey)
                ->withOptions(['verify' => false]) // Hindari error SSL di Laragon/Local
                ->timeout(10)
                ->post($this->baseUrl, [
                    'model' => config('services.groq.model', 'llama-3.3-70b-versatile'),
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'temperature' => 0.2,
                    'max_tokens' => 500,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                // Clean content if AI adds markdown backticks
                $content = str_replace(['```json', '```'], '', $content);
                $data = json_decode(trim($content), true);
                
                return is_array($data) ? $data : ['Gagal memproses rekomendasi AI'];
            }

            Log::error('Groq API Error: ' . $response->body());
            return ['Terjadi gangguan koneksi ke asisten AI Groq.'];

        } catch (\Exception $e) {
            Log::error('Groq Service Exception: ' . $e->getMessage());
        }
    }

    public function chat(array $messages)
    {
        if (!$this->apiKey) {
            return 'API Key Groq Belum Terpasang di .env';
        }

        try {
            $systemPrompt = "Anda adalah AKRE SMART AI, asisten cerdas khusus akreditasi perguruan tinggi di Indonesia. 
            Anda ahli dalam instrumen BAN-PT, LAM Teknik, LAMEMBA, LAMINfokom, dan LAMDIK.
            Berikan jawaban yang ramah, profesional, dan solutif dalam Bahasa Indonesia.
            Jika ditanya hal di luar akreditasi, arahkan kembali dengan sopan.";

            $response = Http::withToken($this->apiKey)
                ->withOptions(['verify' => false])
                ->timeout(15)
                ->post($this->baseUrl, [
                    'model' => config('services.groq.model', 'llama-3.3-70b-versatile'),
                    'messages' => array_merge(
                        [['role' => 'system', 'content' => $systemPrompt]],
                        $messages
                    ),
                    'temperature' => 0.7,
                    'max_tokens' => 1000,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            Log::error('Groq API Chat Error: ' . $response->body());
            return 'Terjadi gangguan koneksi ke asisten AI Groq.';

        } catch (\Exception $e) {
            Log::error('Groq Chat Exception: ' . $e->getMessage());
            return 'AI sedang sibuk, silakan coba beberapa saat lagi.';
        }
    }
}
