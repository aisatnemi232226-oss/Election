<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if ($request->has('filter') && $request->filter) {
            $filter = $request->filter;
            $query->where(function ($q) use ($filter) {
                $q->where('action', 'like', "%{$filter}%")
                  ->orWhere('description', 'like', "%{$filter}%")
                  ->orWhere('log_type', 'like', "%{$filter}%")
                  ->orWhere('operation', 'like', "%{$filter}%")
                  ->orWhereHas('user', function ($userQuery) use ($filter) {
                      $userQuery->where('name', 'like', "%{$filter}%");
                  });
            });
        }

        if ($request->has('log_type') && $request->log_type) {
            $query->where('log_type', $request->log_type);
        }

        if ($request->has('operation') && $request->operation) {
            $query->where('operation', $request->operation);
        }

        $logs = $query->paginate(50);

        $logTypes = ['AUDIT', 'SECURITY', 'USER_ACTIVITY', 'TRANSACTION'];
        $operations = ['INSERT', 'UPDATE', 'DELETE', 'READ', 'LOGIN', 'LOGOUT'];

        return view('admin.audit-logs', compact('logs', 'logTypes', 'operations'));
    }

    public function export()
    {
        $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->get();

        $csvData = "Timestamp,User,Log Type,Operation,Table,Record ID,IP,Action,Details\n";
        
        foreach ($logs as $log) {
            $csvData .= '"' . $log->formatted_timestamp . '","' .
                       $log->user_name . '","' .
                       ($log->log_type ?? '') . '","' .
                       ($log->operation ?? '') . '","' .
                       ($log->table_name ?? '') . '","' .
                       ($log->record_id ?? '') . '","' .
                       ($log->ip_address ?? '') . '","' .
                       $log->action . '","' .
                       $log->description . '"' . "\n";
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="audit_logs_' . date('Y-m-d') . '.csv"');
    }
}