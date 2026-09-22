<?php

namespace App\Http\Controllers;

use App\Models\ElectionEventSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ElectionEventSettingsController extends Controller
{
    public function edit()
    {
        if (!Schema::hasTable('election_event_settings')) {
            $settings = null;
            return view('admin.election-events-settings', compact('settings'))
                ->with('warning', 'Manage Election Events table is not created yet. Please run migrations.');
        }

        $settings = ElectionEventSetting::query()->first();

        return view('admin.election-events-settings', compact('settings'));
    }

    public function update(Request $request)
    {
        if (!Schema::hasTable('election_event_settings')) {
            return redirect()->route('admin.election-events-settings.edit')
                ->withErrors(['label' => 'Manage Election Events table is not created yet. Please run migrations first.']);
        }

        $validated = $request->validate([
            'label' => 'nullable|string|max:255',
            'label_image' => 'nullable|image|max:5120',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:upcoming,ongoing,completed',

            'event_1_title' => 'nullable|string|max:255',
            'event_1_date' => 'nullable|string|max:255',
            'event_1_description' => 'nullable|string|max:2000',
            'event_1_image' => 'nullable|image|max:5120',

            'event_2_title' => 'nullable|string|max:255',
            'event_2_date' => 'nullable|string|max:255',
            'event_2_description' => 'nullable|string|max:2000',
            'event_2_image' => 'nullable|image|max:5120',

            'event_3_title' => 'nullable|string|max:255',
            'event_3_date' => 'nullable|string|max:255',
            'event_3_description' => 'nullable|string|max:2000',
            'event_3_image' => 'nullable|image|max:5120',
        ]);

        $settings = ElectionEventSetting::query()->first() ?? new ElectionEventSetting();

        $settings->fill([
            'label' => $validated['label'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'status' => $validated['status'] ?? 'upcoming',

            'event_1_title' => $validated['event_1_title'] ?? null,
            'event_1_date' => $validated['event_1_date'] ?? null,
            'event_1_description' => $validated['event_1_description'] ?? null,

            'event_2_title' => $validated['event_2_title'] ?? null,
            'event_2_date' => $validated['event_2_date'] ?? null,
            'event_2_description' => $validated['event_2_description'] ?? null,

            'event_3_title' => $validated['event_3_title'] ?? null,
            'event_3_date' => $validated['event_3_date'] ?? null,
            'event_3_description' => $validated['event_3_description'] ?? null,
        ]);

        if ($request->hasFile('label_image')) {
            $path = $request->file('label_image')->store('upload/event-label', 'public');
            $relative = str_replace('upload/', '', $path);
            $settings->label_image = $relative;
        }

        for ($i = 1; $i <= 3; $i++) {
            $field = "event_{$i}_image";
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('upload/event-images', 'public');
                $relative = str_replace('upload/', '', $path);
                $settings->{"event_{$i}_image"} = $relative;
            }
        }

        $settings->save();

        return redirect()->route('admin.election-events-settings.edit')->with('success', 'Manage Election Events updated successfully.');
    }
}
