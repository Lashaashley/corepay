<?php


use App\Models\AuditTrail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


if (!function_exists('logAuditTrail')) {
    
    function logAuditTrail(
        $userId,
        $action,
        $tableName,
        $recordId = null,
        $oldValues = null,
        $newValues = null,
        $contextData = null
    ) {
        try {
            AuditTrail::create([
                'user_id' => $userId,
                'action' => $action,
                'table_name' => $tableName,
                'record_id' => $recordId,
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'context_data' => $contextData ? json_encode($contextData) : null,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error('Audit trail logging failed: ' . $e->getMessage());
        }
    }
}

class SecureQuery
{
    /**
     * Safely execute raw queries with parameter binding
     */
    public static function selectRaw(string $query, array $bindings = [])
    {
        // Validate that query doesn't contain string concatenation
        if (preg_match('/\.\s*\$|concat\s*\(/i', $query)) {
            throw new \Exception("Potential SQL injection detected: String concatenation in query");
        }

        return DB::select($query, $bindings);
    }

    /**
     * Safely build WHERE IN clauses
     */
    public static function whereIn(string $column, array $values)
    {
        // Sanitize column name (alphanumeric, underscore, dot only)
        if (!preg_match('/^[a-zA-Z0-9_.]+$/', $column)) {
            throw new \Exception("Invalid column name: {$column}");
        }

        $placeholders = implode(',', array_fill(0, count($values), '?'));
        
        return [
            'sql' => "{$column} IN ({$placeholders})",
            'bindings' => $values
        ];
    }

    /**
     * Normalize bank codes for safe comparison
     */
    public static function normalizeBankCode($bankCode)
    {
        // Remove leading zeros and validate numeric
        $normalized = ltrim((string)$bankCode, '0');
        
        if (!is_numeric($normalized)) {
            throw new \Exception("Invalid bank code format: {$bankCode}");
        }

        return $normalized;
    }
}

class SecureFileUpload
{
    /**
     * Securely handle Excel file uploads
     */
    public static function handleExcelUpload(UploadedFile $file, string $directory = 'uploads/excel')
    {
        // Validate file
        $maxSize = 10 * 1024 * 1024; // 10MB
        
        if ($file->getSize() > $maxSize) {
            throw new \Exception("File too large. Maximum size is 10MB");
        }

        // Check MIME type
        $allowedMimes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception("Invalid file type. Only Excel files (.xls, .xlsx) are allowed");
        }

        // Check extension
        $allowedExtensions = ['xls', 'xlsx'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception("Invalid file extension. Only .xls and .xlsx are allowed");
        }

        // Generate secure filename
        $fileName = time() . '_' . Str::random(16) . '.' . $extension;
        
        // Store outside public directory
        $path = $file->storeAs($directory, $fileName, 'local');

        Log::info("File uploaded securely", [
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $fileName,
            'path' => $path,
            'size' => $file->getSize()
        ]);

        return [
            'path' => $path,
            'filename' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize()
        ];
    }

    /**
     * Scan file for malicious content (basic)
     */
    public static function scanFile(string $path)
    {
        $content = Storage::get($path);
        
        // Check for PHP code in file
        if (preg_match('/<\?php|<\?=|<script/i', $content)) {
            Log::critical("Malicious content detected in uploaded file", [
                'path' => $path
            ]);
            
            Storage::delete($path);
            throw new \Exception("File contains potentially malicious content");
        }

        return true;
    }
}