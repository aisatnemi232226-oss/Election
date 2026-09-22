<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTicketController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = Ticket::query()
            ->with(['user', 'admin'])
            ->orderByRaw("CASE status WHEN 'Open' THEN 1 WHEN 'In Progress' THEN 2 WHEN 'Resolved' THEN 3 ELSE 4 END")
            ->orderByDesc('created_at')
            ->get();

        return view('admin.tickets', [
            'tickets' => $tickets,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $ticket = Ticket::query()->findOrFail($id);

        $data = $request->validate([
            'status' => ['required', 'in:Open,In Progress,Resolved'],
            'admin_reply' => ['nullable', 'string'],
        ]);

        $ticket->status = $data['status'];
        $ticket->admin_reply = $data['admin_reply'] ?? $ticket->admin_reply;
        $ticket->admin_id = $request->user()->id;

        if ($ticket->status === 'Resolved') {
            $ticket->resolved_at = now();
        } else {
            $ticket->resolved_at = null;
        }

        $ticket->save();

        return back()->with('success', 'Ticket updated.');
    }
}
