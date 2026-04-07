<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class DataEncryptionService
{
    /**
     * Encryption algorithm to use
     */
    protected string $cipher = 'AES-256-GCB';

    /**
     * Encrypt sensitive data
     */
    public function encrypt(mixed $data): string
    {
        try {
            if (is_array($data) || is_object($data)) {
                $data = json_encode($data);
            }

            return Crypt::encryptString($data);
        } catch (\Exception $e) {
            Log::error('Data encryption failed', [
                'error' => $e->getMessage(),
                'data_type' => gettype($data)
            ]);
            throw new \RuntimeException('Failed to encrypt data: ' . $e->getMessage());
        }
    }

    /**
     * Decrypt sensitive data
     */
    public function decrypt(string $encryptedData): mixed
    {
        try {
            $decrypted = Crypt::decryptString($encryptedData);

            // Try to decode JSON for arrays/objects
            $jsonDecoded = json_decode($decrypted, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $jsonDecoded;
            }

            return $decrypted;
        } catch (\Exception $e) {
            Log::error('Data decryption failed', [
                'error' => $e->getMessage(),
                'data_length' => strlen($encryptedData)
            ]);
            throw new \RuntimeException('Failed to decrypt data: ' . $e->getMessage());
        }
    }

    /**
     * Encrypt specific fields in an array
     */
    public function encryptFields(array $data, array $fieldsToEncrypt): array
    {
        foreach ($fieldsToEncrypt as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->encrypt($data[$field]);
            }
        }

        return $data;
    }

    /**
     * Decrypt specific fields in an array
     */
    public function decryptFields(array $data, array $fieldsToDecrypt): array
    {
        foreach ($fieldsToDecrypt as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->decrypt($data[$field]);
            }
        }

        return $data;
    }

    /**
     * Generate a hash for data integrity verification
     */
    public function generateIntegrityHash(array $data): string
    {
        // Remove any existing hash to avoid circular references
        $dataForHash = array_diff_key($data, ['integrity_hash' => '']);

        ksort($dataForHash); // Ensure consistent ordering

        return hash('sha256', json_encode($dataForHash) . config('app.key'));
    }

    /**
     * Verify data integrity using hash
     */
    public function verifyIntegrityHash(array $data): bool
    {
        if (!isset($data['integrity_hash'])) {
            return false;
        }

        $expectedHash = $data['integrity_hash'];
        $actualHash = $this->generateIntegrityHash($data);

        return hash_equals($expectedHash, $actualHash);
    }

    /**
     * Encrypt file content
     */
    public function encryptFile(string $filePath, string $outputPath = null): string
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException('File does not exist: ' . $filePath);
        }

        $content = file_get_contents($filePath);
        $encryptedContent = $this->encrypt($content);

        $outputPath = $outputPath ?? $filePath . '.encrypted';

        if (file_put_contents($outputPath, $encryptedContent) === false) {
            throw new \RuntimeException('Failed to write encrypted file: ' . $outputPath);
        }

        return $outputPath;
    }

    /**
     * Decrypt file content
     */
    public function decryptFile(string $filePath, string $outputPath = null): string
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException('File does not exist: ' . $filePath);
        }

        $encryptedContent = file_get_contents($filePath);
        $decryptedContent = $this->decrypt($encryptedContent);

        $outputPath = $outputPath ?? str_replace('.encrypted', '', $filePath);

        if (file_put_contents($outputPath, $decryptedContent) === false) {
            throw new \RuntimeException('Failed to write decrypted file: ' . $outputPath);
        }

        return $outputPath;
    }

    /**
     * Create encrypted backup of sensitive data
     */
    public function createEncryptedBackup(array $data, string $filename): string
    {
        $encryptedData = $this->encrypt($data);
        $backupPath = storage_path('backups/' . date('Y-m-d') . '/' . $filename . '.enc');

        // Ensure directory exists
        $directory = dirname($backupPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_put_contents($backupPath, $encryptedData) === false) {
            throw new \RuntimeException('Failed to create encrypted backup: ' . $backupPath);
        }

        Log::info('Encrypted backup created', [
            'filename' => $filename,
            'path' => $backupPath,
            'data_keys' => array_keys($data)
        ]);

        return $backupPath;
    }

    /**
     * Restore data from encrypted backup
     */
    public function restoreFromEncryptedBackup(string $backupPath): array
    {
        if (!file_exists($backupPath)) {
            throw new \InvalidArgumentException('Backup file does not exist: ' . $backupPath);
        }

        $encryptedData = file_get_contents($backupPath);
        $data = $this->decrypt($encryptedData);

        Log::info('Data restored from encrypted backup', [
            'backup_path' => $backupPath,
            'data_keys' => is_array($data) ? array_keys($data) : 'non-array'
        ]);

        return $data;
    }

    /**
     * Securely wipe sensitive data from memory
     */
    public function secureWipe(mixed &$data): void
    {
        if (is_string($data)) {
            // Overwrite string with random data
            $data = str_repeat("\0", strlen($data));
        } elseif (is_array($data)) {
            // Recursively wipe array elements
            foreach ($data as &$value) {
                $this->secureWipe($value);
            }
        }

        // Unset the variable
        unset($data);
    }

    /**
     * Check if data appears to be encrypted
     */
    public function isEncrypted(string $data): bool
    {
        // Laravel's Crypt::encryptString adds specific prefixes
        return str_starts_with($data, 'eyJpdiI6');
    }

    /**
     * Get encryption status for debugging
     */
    public function getEncryptionInfo(): array
    {
        return [
            'cipher' => $this->cipher,
            'key_length' => strlen(config('app.key')) * 4, // Base64 decoded length in bits
            'laravel_version' => app()->version(),
            'encryption_enabled' => config('app.cipher') !== null
        ];
    }
}
