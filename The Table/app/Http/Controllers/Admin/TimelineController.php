<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cohort;
use App\Models\Timeline;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TimelineController extends Controller
{
    /**
     * Store timeline update
     */
    public function store(Request $request, Cohort $cohort)
    {
        // Verify admin
        if ($cohort->admin_id !== Auth::id() && !Auth::user()->isPlatformAdmin()) {
            abort(403, 'Only project manager can post timeline updates');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'event_date' => 'required|date',
            'event_type' => 'required|in:milestone,progress,profit,update,meeting,achievement,alert',
            'profit_amount' => 'nullable|numeric|min:0',
            'proof_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Check if business day
        $eventDate = \Carbon\Carbon::parse($validated['event_date']);
        $isBusinessDay = !in_array($eventDate->dayOfWeek, [0, 6]);

        $timelineData = [
            'cohort_id' => $cohort->id,
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'event_date' => $validated['event_date'],
            'event_type' => $validated['event_type'],
            'is_business_day' => $isBusinessDay,
        ];

        // Handle profit updates
        if ($validated['event_type'] === 'profit' && isset($validated['profit_amount'])) {
            $timelineData['profit_amount'] = (int)($validated['profit_amount'] * 100);
        }

        // Handle file upload
        if ($request->hasFile('proof_document')) {
            $path = $request->file('proof_document')->store('timeline-proofs', 'public');
            $timelineData['proof_document'] = $path;
        }

        $timeline = Timeline::create($timelineData);

        // Notify all members via NotificationService
        $description = $validated['description'] ?? 'Check the timeline for details';
        if (strlen($description) > 100) {
            $description = substr($description, 0, 100) . '...';
        }
        
        app(NotificationService::class)->notifyTimelineUpdate($cohort, $validated['title'], $description);

        return redirect()->back()->with('success', 'Timeline update posted successfully! All partners have been notified.');
    }

    /**
     * Get timeline for cohort
     */
    public function index(Cohort $cohort)
    {
        $timelines = $cohort->timelines()
            ->visible()
            ->with('user')
            ->orderBy('event_date', 'desc')
            ->paginate(20);

        return view('admin.timeline.index', compact('cohort', 'timelines'));
    }

    /**
     * Delete timeline entry
     */
    public function destroy(Cohort $cohort, Timeline $timeline)
    {
        // Verify admin
        if ($cohort->admin_id !== Auth::id() && !Auth::user()->isPlatformAdmin()) {
            abort(403);
        }

        // Verify timeline belongs to cohort
        if ($timeline->cohort_id !== $cohort->id) {
            abort(404);
        }

        // Delete proof document if exists
        if ($timeline->proof_document) {
            Storage::disk('public')->delete($timeline->proof_document);
        }

        $timeline->delete();

        return redirect()->back()->with('success', 'Timeline entry deleted successfully.');
    }
}
