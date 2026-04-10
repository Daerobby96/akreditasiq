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
        $type = strtolower(str_replace('-', '', $lamType));

        if ($type === 'lamemba') {
            return "Specialized in LAMEMBA (Ekonomi, Manajemen, Bisnis, Akuntansi). You MUST evaluate based on the framework of 7 Criteria, 21 Dimensions, and 58 Indicators. Heavily weight the '8 Syarat Perlu Terakreditasi Unggul' (VMTS, Tata Kelola, Kurikulum, Penelitian, PKM, Dosen). If a document fails a 'Syarat Perlu', flag it clearly.";
        }

        if ($type === 'laminfokom') {
            $extraInstructions = "- Fokus pada siklus PPEPP dan kesesuaian dengan standar Kurikulum Komputasi 2.1 (2025).";
        } elseif ($type === 'lamteknik') {
            $extraInstructions = "- Fokus pada kesesuaian kurikulum dengan Industri 4.0, fasilitas laboratorium teknik, kerjasama industri, dan kompetensi praktis teknik.";
        } elseif ($type === 'lamdik') {
            $extraInstructions = "- Fokus pada pengembangan kompetensi pedagogik, praktik pengalaman lapangan (PPL), kurikulum pendidikan guru, dan kesiapan menjadi pendidik profesional.";
        }

        if ($type === 'lamteknik') {
            return "Specialized in LAM Teknik (Engineering programs). You MUST evaluate based on engineering accreditation standards focusing on curriculum alignment with industry 4.0, laboratory facilities and utilization, industry partnerships, technical competencies, certifications, and practical engineering skills. Emphasize hands-on training, technical certifications, and industry relevance.";
        }

        if ($type === 'lamdik') {
            return "Specialized in LAMDIK (Education programs). You MUST evaluate based on teacher education standards focusing on pedagogical competencies, teaching practice (PPL), curriculum for teacher training, educational facilities, partnerships with schools, and teacher certification. Emphasize development of teaching skills, classroom management, and educational innovation.";
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
        $type = strtolower(str_replace('-', '', $lamType));
        
        if ($type === 'lamemba') {
            $extraInstructions = "- Fokus pada evaluasi 58 indikator dan deteksi dini kegagalan '8 Syarat Perlu Unggul'.\n- Periksa apakah dokumen menunjukkan pelampauan SN-Dikti.";
        } elseif ($type === 'laminfokom') {
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
    public function auditNarrative(\App\Models\Kriteria $kriteria, array $narrativeContent, array $contextData): array
    {
        try {
            $lamLabel = strtoupper($kriteria->lam_type);
            $expertContext = $this->getExpertContext($kriteria->lam_type);
            
            $systemPrompt = "You are a professional auditor for Indonesian Higher Education Accreditation ({$lamLabel}). {$expertContext}
            Your task is to review the narrative (LED) provided by the institution and evaluate its compliance with the current standards.";
            
            $userPrompt = "Perform a strict COMPLIANCE AUDIT for {$lamLabel} Accreditation Criteria: {$kriteria->kode} - {$kriteria->nama}.
            
            NARRATIVE CONTENT TO AUDIT:
            " . json_encode($narrativeContent, JSON_PRETTY_PRINT) . "
            
            REFERENCE CONTEXT (Quantitative Data & Documents):
            " . json_encode($contextData, JSON_PRETTY_PRINT) . "
            
            AUDIT TASK:
            1. Evaluate if the narrative accurately reflects the quantitative context (LKPS).
            2. Check for missing elements required by the {$lamLabel} instrument for this specific criteria.
            3. Predict a score (1.0 to 4.0) based on quality, depth, and evidence linkage.
            4. Provide critical gaps and actionable recommendations.
            
            Respond ONLY in JSON format:
            {
                \"predicted_score\": (float),
                \"compliance_status\": \"Excellent|Good|Fair|Critical\",
                \"key_strengths\": [\"point 1\", \"point 2\"],
                \"detected_gaps\": [\"gap 1\", \"gap 2\"],
                \"recommendations\": [\"step 1\", \"step 2\"],
                \"analytical_summary\": \"Max 200 words summary\"
            }";

            return $this->callAI($userPrompt, $kriteria->lam_type);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AI Narrative Audit Error: ' . $e->getMessage());
            throw $e;
        }
    }
    public function checkDataConsistency(\App\Models\Kriteria $kriteria, array $narrativeContent, array $lkpsData): array
    {
        try {
            $systemPrompt = "You are a specialized Data Consistency Auditor for Accreditation. Your ONLY task is to find discrepancies between the qualitative narrative (LED) and the quantitative tables (LKPS).";
            
            $userPrompt = "Perform a Data Consistency Audit for Criteria {$kriteria->kode}.
            
            NARRATIVE CONTENT:
            " . json_encode($narrativeContent) . "
            
            LKPS DATA (The Source of Truth):
            " . json_encode($lkpsData) . "
            
            TASKS:
            1. Extract all numbers, years, percentages, and names mentioned in the NARRATIVE.
            2. Cross-reference them with the LKPS DATA.
            3. Flag any inconsistency (e.g. Narrative says '15 professors' but LKPS table only lists '12').
            4. Provide the correct data from LKPS as a fix.
            
            Respond ONLY in JSON format:
            {
                \"consistency_score\": (0-100),
                \"matches\": [{\"narrative_claim\": \"string\", \"lkps_value\": \"string\", \"status\": \"match\"}],
                \"discrepancies\": [
                    {
                        \"claim\": \"string from narrative\",
                        \"fact\": \"string from LKPS\",
                        \"severity\": \"high|medium\",
                        \"fix_suggestion\": \"how to rewrite it properly\"
                    }
                ],
                \"audit_summary\": \"Overall consistency report\"
            }";

            return $this->callAI($userPrompt, $kriteria->lam_type);

        } catch (\Exception $e) {
            Log::error('AI Consistency Audit Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function suggestCitations(array $narrativeContent, array $availableDocuments): array
    {
        try {
            $systemPrompt = "You are an Evidence Linking Specialist. Your task is to analyze an accreditation narrative and suggest which existing documents from the provided list should be cited as proof for specific claims.";
            
            $userPrompt = "NARRATIVE TO ANALYZE:
            " . json_encode($narrativeContent) . "
            
            AVAILABLE DOCUMENTS (LIST):
            " . json_encode($availableDocuments) . "
            
            TASK:
            1. Find claims or sentences in the narrative that require evidence (e.g. SK mentions, statistics, policies).
            2. Find the most relevant document(s) from the list that PROVE each claim.
            3. For each match, provide the specific text segment from the narrative and the document ID to link.
            
            Respond ONLY in JSON format:
            {
                \"suggestions\": [
                    {
                        \"text_segment\": \"string snippet from narrative\",
                        \"document_id\": (int),
                        \"document_name\": \"string\",
                        \"reason\": \"why this document proves the claim\"
                    }
                ]
            }";

            return $this->callAI($userPrompt, 'ban-pt');

        } catch (\Exception $e) {
            Log::error('AI Smart Citation Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
