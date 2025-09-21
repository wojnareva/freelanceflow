<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeEntry;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class TimeEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = TimeEntry::where('user_id', auth()->id())
            ->with(['project', 'task']);

        // Date filtering
        if ($request->has('date_from')) {
            $query->where('start_time', '>=', $request->get('date_from'));
        }

        if ($request->has('date_to')) {
            $query->where('start_time', '<=', $request->get('date_to'));
        }

        // Project filtering
        if ($request->has('project_id')) {
            $query->where('project_id', $request->get('project_id'));
        }

        // Billable filtering
        if ($request->has('billable')) {
            $query->where('billable', $request->boolean('billable'));
        }

        // Billed filtering
        if ($request->has('billed')) {
            $query->where('billed', $request->boolean('billed'));
        }

        $timeEntries = $query->orderBy('start_time', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $timeEntries->items(),
            'meta' => [
                'current_page' => $timeEntries->currentPage(),
                'last_page' => $timeEntries->lastPage(),
                'per_page' => $timeEntries->perPage(),
                'total' => $timeEntries->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'nullable|exists:tasks,id',
            'description' => 'required|string|max:1000',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'duration_seconds' => 'nullable|integer|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'billable' => 'boolean',
            'billed' => 'boolean',
        ]);

        // Verify project belongs to user
        $project = Project::where('id', $validated['project_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $validated['user_id'] = auth()->id();

        // Calculate duration if not provided
        if (!isset($validated['duration_seconds']) && isset($validated['end_time'])) {
            $start = Carbon::parse($validated['start_time']);
            $end = Carbon::parse($validated['end_time']);
            $validated['duration_seconds'] = $end->diffInSeconds($start);
        }

        // Set hourly rate from project if not provided
        if (!isset($validated['hourly_rate'])) {
            $validated['hourly_rate'] = $project->hourly_rate ?? $project->client->hourly_rate ?? 0;
        }

        $timeEntry = TimeEntry::create($validated);
        $timeEntry->load(['project', 'task']);

        return response()->json([
            'data' => $timeEntry,
            'message' => 'Time entry created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TimeEntry $timeEntry): JsonResponse
    {
        $this->authorize('view', $timeEntry);

        $timeEntry->load(['project', 'task']);

        return response()->json([
            'data' => $timeEntry,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TimeEntry $timeEntry): JsonResponse
    {
        $this->authorize('update', $timeEntry);

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'nullable|exists:tasks,id',
            'description' => 'required|string|max:1000',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'duration_seconds' => 'nullable|integer|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'billable' => 'boolean',
            'billed' => 'boolean',
        ]);

        // Verify project belongs to user
        Project::where('id', $validated['project_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Calculate duration if not provided
        if (!isset($validated['duration_seconds']) && isset($validated['end_time'])) {
            $start = Carbon::parse($validated['start_time']);
            $end = Carbon::parse($validated['end_time']);
            $validated['duration_seconds'] = $end->diffInSeconds($start);
        }

        $timeEntry->update($validated);
        $timeEntry->load(['project', 'task']);

        return response()->json([
            'data' => $timeEntry,
            'message' => 'Time entry updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeEntry $timeEntry): JsonResponse
    {
        $this->authorize('delete', $timeEntry);

        if ($timeEntry->billed) {
            return response()->json([
                'error' => 'Cannot delete billed time entry'
            ], 422);
        }

        $timeEntry->delete();

        return response()->json([
            'message' => 'Time entry deleted successfully'
        ]);
    }

    /**
     * Start a new timer
     */
    public function start(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'nullable|exists:tasks,id',
            'description' => 'required|string|max:1000',
        ]);

        // Verify project belongs to user
        $project = Project::where('id', $validated['project_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Stop any running timers first
        TimeEntry::where('user_id', auth()->id())
            ->whereNull('end_time')
            ->update(['end_time' => now()]);

        $validated['user_id'] = auth()->id();
        $validated['start_time'] = now();
        $validated['hourly_rate'] = $project->hourly_rate ?? $project->client->hourly_rate ?? 0;

        $timeEntry = TimeEntry::create($validated);
        $timeEntry->load(['project', 'task']);

        return response()->json([
            'data' => $timeEntry,
            'message' => 'Timer started successfully'
        ], 201);
    }

    /**
     * Stop a running timer
     */
    public function stop(TimeEntry $timeEntry): JsonResponse
    {
        $this->authorize('update', $timeEntry);

        if ($timeEntry->end_time) {
            return response()->json([
                'error' => 'Timer is already stopped'
            ], 422);
        }

        $endTime = now();
        $duration = $endTime->diffInSeconds($timeEntry->start_time);

        $timeEntry->update([
            'end_time' => $endTime,
            'duration_seconds' => $duration,
        ]);

        $timeEntry->load(['project', 'task']);

        return response()->json([
            'data' => $timeEntry,
            'message' => 'Timer stopped successfully'
        ]);
    }

    /**
     * Get currently running timer
     */
    public function running(): JsonResponse
    {
        $runningTimer = TimeEntry::where('user_id', auth()->id())
            ->whereNull('end_time')
            ->with(['project', 'task'])
            ->first();

        return response()->json([
            'data' => $runningTimer,
        ]);
    }

    /**
     * Bulk create time entries
     */
    public function bulk(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'entries' => 'required|array|min:1|max:100',
            'entries.*.project_id' => 'required|exists:projects,id',
            'entries.*.task_id' => 'nullable|exists:tasks,id',
            'entries.*.description' => 'required|string|max:1000',
            'entries.*.start_time' => 'required|date',
            'entries.*.end_time' => 'nullable|date|after:entries.*.start_time',
            'entries.*.duration_seconds' => 'nullable|integer|min:0',
            'entries.*.hourly_rate' => 'nullable|numeric|min:0',
            'entries.*.billable' => 'boolean',
            'entries.*.billed' => 'boolean',
        ]);

        $createdEntries = [];
        $errors = [];

        foreach ($validated['entries'] as $index => $entryData) {
            try {
                // Verify project belongs to user
                $project = Project::where('id', $entryData['project_id'])
                    ->where('user_id', auth()->id())
                    ->firstOrFail();

                $entryData['user_id'] = auth()->id();

                // Calculate duration if not provided
                if (!isset($entryData['duration_seconds']) && isset($entryData['end_time'])) {
                    $start = Carbon::parse($entryData['start_time']);
                    $end = Carbon::parse($entryData['end_time']);
                    $entryData['duration_seconds'] = $end->diffInSeconds($start);
                }

                // Set hourly rate from project if not provided
                if (!isset($entryData['hourly_rate'])) {
                    $entryData['hourly_rate'] = $project->hourly_rate ?? $project->client->hourly_rate ?? 0;
                }

                $timeEntry = TimeEntry::create($entryData);
                $timeEntry->load(['project', 'task']);
                $createdEntries[] = $timeEntry;

            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'data' => $createdEntries,
            'errors' => $errors,
            'message' => sprintf(
                'Created %d time entries successfully. %d errors.',
                count($createdEntries),
                count($errors)
            )
        ], count($errors) > 0 ? 207 : 201); // 207 Multi-Status if there are errors
    }
}