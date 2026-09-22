<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Log;

class CandidateApprovalController extends Controller
{
    public function approve($id)
    {
        try {
            $candidate = Candidate::findOrFail($id);
            $oldStatus = $candidate->status;
            $candidate->status = 'Active';
            $candidate->save();

            $admin = auth()->user();
            AuditLogger::log(
                request(),
                $admin ? $admin->id : null,
                'AUDIT',
                'UPDATE',
                'Candidate Approved',
                'Approved candidate: ' . ($candidate->name ?? ('#' . $candidate->id)),
                'candidate',
                (int) $candidate->id,
                ['status' => $oldStatus],
                ['status' => $candidate->status]
            );

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('admin.candidates.index')->with('success', 'Candidate approved successfully.');
        } catch (\Exception $e) {
            Log::error('Error approving candidate: ' . $e->getMessage());
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->route('admin.candidates.index')->with('error', 'Failed to approve candidate: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $candidate = Candidate::findOrFail($id);
            $oldStatus = $candidate->status;
            $candidate->status = 'Inactive';
            $candidate->save();

            $admin = auth()->user();
            AuditLogger::log(
                request(),
                $admin ? $admin->id : null,
                'AUDIT',
                'UPDATE',
                'Candidate Rejected',
                'Rejected candidate: ' . ($candidate->name ?? ('#' . $candidate->id)),
                'candidate',
                (int) $candidate->id,
                ['status' => $oldStatus],
                ['status' => $candidate->status]
            );

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('admin.candidates.index')->with('success', 'Candidate rejected successfully.');
        } catch (\Exception $e) {
            Log::error('Error rejecting candidate: ' . $e->getMessage());
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->route('admin.candidates.index')->with('error', 'Failed to reject candidate: ' . $e->getMessage());
        }
    }
}
