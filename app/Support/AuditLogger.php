<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogger
{
    public static function log(
        ?Request $request,
        ?int $userId,
        string $logType,
        string $operation,
        string $action,
        string $description,
        string $tableName,
        ?int $recordId = null,
        ?array $oldData = null,
        ?array $newData = null
    ): void {
        try {
            AuditLog::query()->create([
                'user_id' => $userId,
                'log_type' => $logType,
                'action' => $action,
                'description' => $description,
                'table_name' => $tableName,
                'record_id' => $recordId,
                'operation' => $operation,
                'old_data' => $oldData,
                'new_data' => $newData,
                'ip_address' => $request ? $request->ip() : null,
                'user_agent' => $request ? $request->userAgent() : null,
            ]);
        } catch (\Throwable $e) {
            // best-effort logging only
        }
    }
}
