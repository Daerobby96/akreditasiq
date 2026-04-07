<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class FileUploadSecurity
{
    /**
     * Allowed MIME types for different file categories
     */
    protected array $allowedMimeTypes = [
        'documents' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
        ],
        'images' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ],
        'archives' => [
            'application/zip',
            'application/x-rar-compressed',
        ]
    ];

    /**
     * Maximum file sizes (in bytes)
     */
    protected array $maxFileSizes = [
        'documents' => 10 * 1024 * 1024, // 10MB
        'images' => 5 * 1024 * 1024,     // 5MB
        'archives' => 50 * 1024 * 1024,  // 50MB
    ];

    /**
     * Dangerous file extensions to block
     */
    protected array $dangerousExtensions = [
        'exe', 'bat', 'cmd', 'com', 'scr', 'pif', 'jar', 'java', 'class',
        'php', 'asp', 'jsp', 'cgi', 'pl', 'py', 'rb', 'sh', 'bash',
        'dll', 'so', 'dylib', 'sys', 'drv', 'ocx', 'vbs', 'js', 'hta',
        'lnk', 'scf', 'url', 'reg', 'inf', 'msi', 'msp', 'deb', 'rpm'
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if request has file uploads
        if (!$request->hasFile('file') && !$request->hasFile('files')) {
            return $next($request);
        }

        $files = $this->collectFilesFromRequest($request);

        foreach ($files as $file) {
            if (!$this->validateFile($file, $request)) {
                return $this->createSecurityErrorResponse('File upload blocked due to security policy violation.');
            }
        }

        return $next($request);
    }

    /**
     * Collect all files from the request
     */
    protected function collectFilesFromRequest(Request $request): array
    {
        $files = [];

        // Handle single file uploads
        if ($request->hasFile('file')) {
            $files[] = $request->file('file');
        }

        // Handle multiple file uploads
        if ($request->hasFile('files')) {
            $uploadedFiles = $request->file('files');
            if (is_array($uploadedFiles)) {
                $files = array_merge($files, $uploadedFiles);
            } else {
                $files[] = $uploadedFiles;
            }
        }

        return $files;
    }

    /**
     * Validate a single file upload
     */
    protected function validateFile($file, Request $request): bool
    {
        // Check if file is valid
        if (!$file || !$file->isValid()) {
            Log::warning('Invalid file upload detected', [
                'client_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'original_name' => $file ? $file->getClientOriginalName() : 'unknown'
            ]);
            return false;
        }

        // Check file extension against dangerous extensions
        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, $this->dangerousExtensions)) {
            Log::warning('Dangerous file extension blocked', [
                'extension' => $extension,
                'filename' => $file->getClientOriginalName(),
                'client_ip' => $request->ip()
            ]);
            return false;
        }

        // Check MIME type
        $mimeType = $file->getMimeType();
        if (!$this->isAllowedMimeType($mimeType)) {
            Log::warning('Disallowed MIME type blocked', [
                'mime_type' => $mimeType,
                'filename' => $file->getClientOriginalName(),
                'client_ip' => $request->ip()
            ]);
            return false;
        }

        // Check file size
        $maxSize = $this->getMaxFileSize($mimeType);
        if ($file->getSize() > $maxSize) {
            Log::warning('File size exceeds limit', [
                'file_size' => $file->getSize(),
                'max_size' => $maxSize,
                'filename' => $file->getClientOriginalName(),
                'client_ip' => $request->ip()
            ]);
            return false;
        }

        // Check for malicious content (basic checks)
        if (!$this->performSecurityChecks($file)) {
            Log::warning('File failed security checks', [
                'filename' => $file->getClientOriginalName(),
                'client_ip' => $request->ip()
            ]);
            return false;
        }

        return true;
    }

    /**
     * Check if MIME type is allowed
     */
    protected function isAllowedMimeType(string $mimeType): bool
    {
        foreach ($this->allowedMimeTypes as $category => $types) {
            if (in_array($mimeType, $types)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get maximum file size for MIME type
     */
    protected function getMaxFileSize(string $mimeType): int
    {
        if (str_starts_with($mimeType, 'image/')) {
            return $this->maxFileSizes['images'];
        }

        if (in_array($mimeType, $this->allowedMimeTypes['archives'])) {
            return $this->maxFileSizes['archives'];
        }

        return $this->maxFileSizes['documents'];
    }

    /**
     * Perform additional security checks
     */
    protected function performSecurityChecks($file): bool
    {
        // Check for null bytes in filename
        if (str_contains($file->getClientOriginalName(), "\0")) {
            return false;
        }

        // Check for suspicious characters in filename
        $suspiciousChars = ['<', '>', ':', '"', '|', '?', '*'];
        foreach ($suspiciousChars as $char) {
            if (str_contains($file->getClientOriginalName(), $char)) {
                return false;
            }
        }

        // Check file content for basic patterns (for text files)
        if (str_starts_with($file->getMimeType(), 'text/')) {
            $content = file_get_contents($file->getPathname());
            if ($this->containsMaliciousPatterns($content)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check for malicious patterns in file content
     */
    protected function containsMaliciousPatterns(string $content): bool
    {
        $maliciousPatterns = [
            '/<\?php/i',           // PHP opening tags
            '/<%.*%>/i',          // ASP tags
            '/<script/i',         // Script tags
            '/javascript:/i',     // JavaScript URLs
            '/vbscript:/i',       // VBScript URLs
            '/onload\s*=/i',      // Event handlers
            '/eval\s*\(/i',       // Eval functions
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create security error response
     */
    protected function createSecurityErrorResponse(string $message): Response
    {
        return response()->json([
            'error' => 'Security Violation',
            'message' => $message,
            'code' => 'FILE_SECURITY_VIOLATION'
        ], 403);
    }
}
