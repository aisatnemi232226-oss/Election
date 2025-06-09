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
                  ->orWhereHas('user', function ($userQuery) use ($filter) {
                      $userQuery->where('name', 'like', "%{$filter}%");
                  });
            });
        }

        if ($request->has('log_type') && $request->log_type) {
            $query->where('log_type', $request->log_type);
        }

        $logs = $query->paginate(50);

        return view('admin.audit-logs', compact('logs'));
    }

    public function export()
    {
        $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->get();

        $csvData = "Timestamp,User,Action,Details\n";
        
        foreach ($logs as $log) {
            $csvData .= '"' . $log->formatted_timestamp . '","' . 
                       $log->user_name . '","' . 
                       $log->action . '","' . 
                       $log->description . '"' . "\n";
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="audit_logs_' . date('Y-m-d') . '.csv"');
    }
}