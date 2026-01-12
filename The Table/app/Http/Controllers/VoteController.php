<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\VoteResponse;
use App\Models\Cohort;
use App\Models\Notification;
use App\Helpers\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    /**
     * Show create vote form
     */
    public function create(Cohort $cohort)
    {
        // Verify user is admin of this cohort
        if ($cohort->admin_id !== Auth::id()) {
            abort(403, 'Only cohort admin can create votes.');
        }

        return view('admin.votes.create', compact('cohort'));
    }

    /**
     * Store new vote
     */
    public function store(Request $request, Cohort $cohort)
    {
        // Verify user is admin of this cohort
        if ($cohort->admin_id !== Auth::id()) {
            abort(403, 'Only cohort admin can create votes.');
        }

        $validated = $request->validate([
            'vote_type' => 'required|in:standard,supermajority,unanimous',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'voting_options' => 'required|array|min:2',
            'voting_options.*' => 'required|string|max:255',
            'deadline' => 'required|date|after:now',
            'requires_minimum_participation' => 'boolean',
            'minimum_participation_percent' => 'nullable|numeric|min:1|max:100',
        ]);

        // Create vote
        $vote = Vote::create([
            'cohort_id' => $cohort->id,
            'created_by' => Auth::id(),
            'vote_type' => $validated['vote_type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'voting_options' => $validated['voting_options'],
            'deadline' => $validated['deadline'],
            'requires_minimum_participation' => $request->boolean('requires_minimum_participation'),
            'minimum_participation_percent' => $validated['minimum_participation_percent'] ?? null,
            'status' => 'active',
        ]);

        // Create notifications for all cohort members
        $cohort->members->each(function ($member) use ($vote, $cohort) {
            Notification::send(
                $member->user_id,
                'vote_created',
                'New Vote: ' . $vote->title,
                "A new vote has been created for {$cohort->name}. Your participation is required.",
                $cohort->id,
                route('member.votes.show', [$cohort, $vote]),
                'Cast Vote',
                'high'
            );
        });

        ActivityLog::log('vote_created', Auth::id(), $cohort->id, "Created vote: {$vote->title}");

        return redirect()->route('admin.cohorts.show', $cohort)
            ->with('success', 'Vote created successfully! All members have been notified.');
    }

    /**
     * Show vote details
     */
    public function show(Cohort $cohort, Vote $vote)
    {
        // Verify vote belongs to cohort
        if ($vote->cohort_id !== $cohort->id) {
            abort(404);
        }

        // Check if user is member of cohort
        $membership = $cohort->members()->where('user_id', Auth::id())->first();
        
        if (!$membership && $cohort->admin_id !== Auth::id()) {
            abort(403, 'You must be a member of this cohort to view this vote.');
        }

        // Get user's vote response if exists
        $userResponse = $vote->responses()->where('user_id', Auth::id())->first();

        // Load vote with responses and users
        $vote->load(['responses.user', 'creator']);

        // Calculate voting statistics
        $stats = [
            'total_members' => $cohort->members()->count(),
            'total_responses' => $vote->responses()->count(),
            'participation_rate' => $vote->getParticipationRate(),
            'vote_distribution' => $vote->getVoteDistribution(),
            'leading_option' => $vote->getLeadingOption(),
        ];

        return view('votes.show', compact('cohort', 'vote', 'userResponse', 'membership', 'stats'));
    }

    /**
     * Cast a vote
     */
    public function cast(Request $request, Cohort $cohort, Vote $vote)
    {
        // Verify vote belongs to cohort
        if ($vote->cohort_id !== $cohort->id) {
            abort(404);
        }

        // Check if vote is still active
        if ($vote->status !== 'active') {
            return redirect()->back()
                ->with('error', 'This vote is no longer active.');
        }

        // Check if deadline has passed
        if ($vote->deadline->isPast()) {
            return redirect()->back()
                ->with('error', 'The voting deadline has passed.');
        }

        // Get user's membership
        $membership = $cohort->members()->where('user_id', Auth::id())->first();
        
        if (!$membership) {
            abort(403, 'You must be a member of this cohort to vote.');
        }

        $validated = $request->validate([
            'vote_option' => 'required|string',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Verify option is valid
        if (!in_array($validated['vote_option'], $vote->voting_options)) {
            return redirect()->back()
                ->with('error', 'Invalid voting option selected.');
        }

        // Check if user has already voted
        $existingResponse = $vote->responses()->where('user_id', Auth::id())->first();
        
        if ($existingResponse) {
            // Update existing vote
            $existingResponse->update([
                'vote_option' => $validated['vote_option'],
                'comment' => $validated['comment'] ?? null,
                'voting_power' => $membership->shares,
            ]);

            ActivityLog::log('vote_updated', Auth::id(), $cohort->id, "Updated vote on: {$vote->title}");

            return redirect()->back()
                ->with('success', 'Your vote has been updated successfully!');
        }

        // Create new vote response
        VoteResponse::create([
            'vote_id' => $vote->id,
            'user_id' => Auth::id(),
            'vote_option' => $validated['vote_option'],
            'comment' => $validated['comment'] ?? null,
            'voting_power' => $membership->shares,
        ]);

        // Notify admin
        Notification::send(
            $cohort->admin_id,
            'vote_cast',
            'Vote Cast: ' . $vote->title,
            Auth::user()->first_name . ' ' . Auth::user()->last_name . ' has cast their vote.',
            $cohort->id,
            route('admin.votes.show', [$cohort, $vote]),
            'View Results'
        );

        ActivityLog::log('vote_cast', Auth::id(), $cohort->id, "Cast vote on: {$vote->title}");

        return redirect()->back()
            ->with('success', 'Your vote has been recorded successfully!');
    }

    /**
     * Close a vote
     */
    public function close(Cohort $cohort, Vote $vote)
    {
        // Verify user is admin of this cohort
        if ($cohort->admin_id !== Auth::id()) {
            abort(403, 'Only cohort admin can close votes.');
        }

        // Verify vote belongs to cohort
        if ($vote->cohort_id !== $cohort->id) {
            abort(404);
        }

        // Check if vote is already closed
        if ($vote->status !== 'active') {
            return redirect()->back()
                ->with('error', 'This vote is already closed.');
        }

        // Determine result
        $result = $vote->determineResult();

        $vote->update([
            'status' => 'closed',
            'result' => $result['outcome'],
            'winning_option' => $result['winning_option'] ?? null,
            'final_vote_count' => $vote->responses()->count(),
            'closed_at' => now(),
        ]);

        // Notify all members of result
        $cohort->members->each(function ($member) use ($vote, $cohort, $result) {
            Notification::send(
                $member->user_id,
                'vote_closed',
                'Vote Closed: ' . $vote->title,
                "The vote has been closed. Result: {$result['outcome']}",
                $cohort->id,
                route('member.votes.show', [$cohort, $vote]),
                'View Results'
            );
        });

        ActivityLog::log('vote_closed', Auth::id(), $cohort->id, "Closed vote: {$vote->title} - Result: {$result['outcome']}");

        return redirect()->route('admin.cohorts.show', $cohort)
            ->with('success', 'Vote closed successfully! Result: ' . $result['outcome']);
    }
}
