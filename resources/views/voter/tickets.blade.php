@extends('layouts.voter')

@section('title', 'Support Tickets | PCU Online Election')

@section('content')
<div class="page-content tickets-page">
    <header class="page-intro">
        <h1>Support Tickets</h1>
        <p>Submit a vote concern ticket (vote reset request / wrong vote) and wait for an admin response.</p>
    </header>

    @if (session('success'))
        <div class="notice notice-success tickets-alert" role="status">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="notice notice-error tickets-alert" role="alert">
            <ul class="error-list">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <section class="surface-card">
        <h2>Submit Ticket</h2>
        <form method="POST" action="{{ route('tickets.store') }}" class="ticket-form">
            @csrf
            <div class="field">
                <label for="ticket-type">Concern Type</label>
                @php($type = old('type', 'Vote Concern'))
                <select id="ticket-type" name="type" required>
                    <option value="Vote Concern" {{ $type === 'Vote Concern' ? 'selected' : '' }}>Vote Concern</option>
                    <option value="Reset Vote Request" {{ $type === 'Reset Vote Request' ? 'selected' : '' }}>Reset Vote Request</option>
                    <option value="Wrong Vote" {{ $type === 'Wrong Vote' ? 'selected' : '' }}>Wrong Vote</option>
                </select>
            </div>
            <div class="field">
                <label for="ticket-subject">Subject</label>
                <input id="ticket-subject" name="subject" value="{{ old('subject') }}" placeholder="e.g. Request reset for President" required>
            </div>
            <div class="field">
                <label for="ticket-message">Message</label>
                <textarea id="ticket-message" name="message" rows="5" placeholder="Explain what happened and what you need." required>{{ old('message') }}</textarea>
            </div>
            <div class="ticket-actions"><button type="submit" class="button button-primary">Submit</button></div>
        </form>
    </section>

    <section class="surface-card">
        <h2>My Tickets</h2>
        <div class="table-scroll">
            <table class="data-table tickets-table">
                <thead><tr><th>Date</th><th>Type</th><th>Subject and details</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse(($tickets ?? collect()) as $t)
                        <tr>
                            <td>{{ optional($t->created_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ $t->type }}</td>
                            <td class="ticket-details">
                                <strong>{{ $t->subject }}</strong>
                                <p>{{ $t->message }}</p>
                                @if ($t->admin_reply)
                                    <div class="ticket-reply"><strong>Admin Reply:</strong><p>{{ $t->admin_reply }}</p></div>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $t->status === 'Resolved' ? 'badge-success' : ($t->status === 'In Progress' ? 'badge-warning' : 'badge-info') }}">{{ $t->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted-text">No tickets yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
