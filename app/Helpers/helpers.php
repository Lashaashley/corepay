<?php

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

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