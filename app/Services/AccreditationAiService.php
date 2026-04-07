<?php

namespace App\Services;

use App\Models\Dokumen;
use App\Models\PenilaianAi;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Smalot\PdfParser\Parser;

class AccreditationAiService
{
    protected $provider;
    protected $model;
    protected $cacheTtl;

    public function __construct()
    {
        $this->provider = config('services.ai_provider', 'openai'); // default to openai
        if ($this->provider === 'groq') {
            $this->model = config('services.groq.model', 'llama-3.3-70b-versatile');
            $this->cacheTtl = config('services.groq.cache_ttl', 3600);
        } else {
            $this->model = config('services.openai.model', 'gpt-4o-mini');
            $this->cacheTtl = config('services.openai.cache_ttl', 3600);
        }
    }

    public function analyzeDocument(Dokumen $dokumen)
    {
        try {
            // Extract text from PDF
            $docContent = $this->extractTextFromPdf($dokumen);

            if (empty($docContent)) {
                throw new \Exception('Unable to extract text from document');
            }

            // Check cache first
            $cacheKey = 'ai_analysis_' . md5($docContent . $dokumen->kriteria->nama);
            $cachedResult = Cache::get($cacheKey);

            if ($cachedResult) {
                return PenilaianAi::create(array_merge($cachedResult, ['dokumen_id' => $dokumen->id]));
            }

            // Build prompt and call AI
            $prompt = $this->buildPrompt($dokumen, $docContent);
            $aiResponse = $this->callAI($prompt, $dokumen->kriteria->lam_type);

            // Parse and validate response
            $parsedResult = $this->parseAIResponse($aiResponse);

            // Cache the result
            Cache::put($cacheKey, $parsedResult, $this->cacheTtl);

            return PenilaianAi::create(array_merge($parsedResult, [
                'dokumen_id' => $dokumen->id,
                'raw_response' => $aiResponse
            ]));

        } catch (\Exception $e) {
            Log::error('AI Analysis Error for document ' . $dokumen->id . ': ' . $e->getMessage());
            throw $e; // Throw instead of returning null so UI can catch it
        }
    }

    protected function extractTextFromPdf(Dokumen $dokumen): string
    {
        $path = storage_path('app/public/' . $dokumen->file_path);

        if (!file_exists($path)) {
            throw new \Exception('Document file not found: ' . $path);
        }

        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        $text = $pdf->getText();

        // Clean up text - remove excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        // Limit text length to avoid token limits
        if (strlen($text) > 10000) {
            $text = substr($text, 0, 10000) . '...';
        }

        return $text;
    }

    protected function callAI(string $prompt, string $lamType = 'ban-pt'): array
    {
        if ($this->provider === 'groq') {
            return $this->callGroq($prompt, $lamType);
        } else {
            return $this->callOpenAI($prompt, $lamType);
        }
    }

    protected function callOpenAI(string $prompt, string $lamType = 'ban-pt'): array
    {
        $lamLabel = strtoupper($lamType);
        $expertContext = $this->getExpertContext($lamType);

        $response = OpenAI::chat()->create([
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "You are an expert accreditation evaluator for Indonesian higher education institutions. {$expertContext} Provide detailed, constructive analysis in Indonesian language formatted as JSON."
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.3,
            'max_tokens' => 2000,
            'response_format' => ['type' => 'json_object']
        ]);

        $content = $response->choices[0]->message->content;
        return json_decode($content, true);
    }

    protected function callGroq(string $prompt, string $lamType = 'ban-pt'): array
    {
        $apiKey = config('services.groq.key');
        $apiUrl = config('services.groq.url');
        $expertContext = $this->getExpertContext($lamType);

        if (!$apiKey) {
            throw new \Exception('Groq API key not configured');
        }

        $request = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ]);

        $response = $request->post($apiUrl, [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "You are an expert accreditation evaluator for Indonesian higher education institutions. {$expertContext} Provide detailed, constructive analysis in Indonesian language formatted as JSON."
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.3,
            'max_tokens' => 2000,
            'response_format' => ['type' => 'json_object']
        ]);

        if ($response->failed()) {
            throw new \Exception('Groq API call failed: ' . $response->body());
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'];
        return json_decode($content, true);
    }

    protected function getExpertContext(string $lamType): string
    {
        if ($lamType === 'lam-emba') {
            return "Specialized in LAMEMBA (Ekonomi, Manajemen, Bisnis, Akuntansi). You MUST evaluate based on the framework of 7 Criteria, 21 Dimensions, and 58 Indicators. Heavily weight the '8 Syarat Perlu Terakreditasi Unggul' (VMTS, Tata Kelola, Kurikulum, Penelitian, PKM, Dosen). If a document fails a 'Syarat Perlu', flag it clearly.";
        }

        if ($lamType === 'lam-infokom') {
            return "Specialized in LAM-INFOKOM (Informatika dan Komputer). You MUST evaluate based on the PPEPP (Penetapan, Pelaksanaan, Evaluasi, Pengendalian, Peningkatan) cycle. Focus on OBE (Outcome-Based Education), industrial certifications, and Lab infrastructures.";
        }

        return "Specialized in BAN-PT standards. Use the 9 Criteria framework with focus on output and outcome quality.";
    }

    protected function parseAIResponse(array $response): array
    {
        return [
            'skor' => (float) ($response['skor'] ?? 0),
            'analisis_teks' => $response['analisis_teks'] ?? 'Tidak ada analisis tersedia.',
            'gap_analysis' => $response['gap_analysis'] ?? 'Kesenjangan tidak terdeteksi.',
            'rekomendasi' => $response['rekomendasi'] ?? 'Tidak ada rekomendasi khusus.',
            'engine' => $this->provider . ' / ' . $this->model
        ];
    }

    public function generateNarrative(\App\Models\Kriteria $kriteria, array $contextData, string $prompt = ''): string
    {
        try {
            $lamLabel = strtoupper($kriteria->lam_type);
            $expertContext = $this->getExpertContext($kriteria->lam_type);
            $systemPrompt = "You are an expert accreditation writer. {$expertContext} 
            
            MANDATORY FORMATTING & CITATION RULES:
            1. Use <h2> for titles, <h3> for sub-sections.
            2. Use <strong> or <b> for key terms.
            3. Use <p style='text-align: justify;'> for paragraphs.
            4. **CITATIONS (CRITICAL)**: When referencing a claim that comes from a specific 'DOKUMEN' provided in the context, you MUST include a citation tag exactly like this: <span class='evidence-link' data-id='ID_DOKUMEN_DISINI'>[Lihat Bukti: NAMA_FILE]</span>.
            5. Ensure the citation appears naturally at the end of a sentence or claim.
            6. **INFOGRAPHICS (PRO)**: If the LKPS context data is numerical (trends, ratios, or counts), you MUST insert a visualization tag like this: <div class='ai-chart' data-type='bar|line|pie|doughnut' data-label='JUDUL_GRAFIK' data-labels='[\"Jan\", \"Feb\"]' data-values='[10, 20]' style='max-width: 500px; margin: 2rem auto;'></div>.
            7. Tone: Formal Academic.";
            
            $userPrompt = "Generate a structured HTML narrative for {$lamLabel} Accreditation Criteria: {$kriteria->kode} - {$kriteria->nama}.
            
            CONTEXT DATA:
            " . json_encode($contextData, JSON_PRETTY_PRINT) . "
            
            USER REQUEST:
            {$prompt}
            
            Respond ONLY with the formatted HTML content. Ensure CITATIONS link to the real IDs provided in context.";

            if ($this->provider === 'groq') {
                return $this->callGroqNarrative($systemPrompt, $userPrompt);
            }

            $response = OpenAI::chat()->create([
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2500
            ]);

            return $response->choices[0]->message->content;

        } catch (\Exception $e) {
            Log::error('AI Narrative Generation Error: ' . $e->getMessage());
            return "Maaf, terjadi kesalahan saat generate narasi AI: " . $e->getMessage();
        }
    }

    protected function callGroqNarrative(string $systemPrompt, string $userPrompt): string
    {
        $apiKey = config('services.groq.key');
        $apiUrl = config('services.groq.url');

        $request = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ]);

        $response = $request->post($apiUrl, [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 2500
        ]);

        return $response->json()['choices'][0]['message']['content'];
    }

    protected function buildPrompt(Dokumen $dokumen, string $content): string
    {
        $lamType = $dokumen->kriteria->lam_type ?? 'ban-pt';
        $lamLabel = strtoupper($lamType);
        
        $extraInstructions = "";
        if ($lamType === 'lam-emba') {
            $extraInstructions = "- Fokus pada evaluasi 58 indikator dan deteksi dini kegagalan '8 Syarat Perlu Unggul'.\n- Periksa apakah dokumen menunjukkan pelampauan SN-Dikti.";
        } elseif ($lamType === 'lam-infokom') {
            $extraInstructions = "- Fokus pada siklus PPEPP dan kesesuaian dengan standar Kurikulum Komputasi 2.1 (2025).";
        }

        return "Analisis dokumen akreditasi untuk Kriteria {$lamLabel}: {$dokumen->kriteria->nama}

KONTEN DOKUMEN:
{$content}

TUGAS ANDA:
Evaluasi dokumen ini berdasarkan standar instrumen akreditasi {$lamLabel}.

Berikan respons dalam format JSON dengan struktur berikut:
{
  \"skor\": (angka 1.0-4.0),
  \"analisis_teks\": \"Analisis detail terhadap standar {$lamLabel}\",
  \"gap_analysis\": \"Identifikasi kesenjangan\",
  \"rekomendasi\": \"Saran peningkatan konkrit\"
}

INSTRUKSI KHUSUS {$lamLabel}:
{$extraInstructions}

Pertimbangkan:
- Kualitas data dan bukti fisik.
- Potensi mencapai status Terakreditasi Unggul.";
    }
}
