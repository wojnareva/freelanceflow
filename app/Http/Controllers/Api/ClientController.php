<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Client::where('user_id', auth()->id());

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        $clients = $query->orderBy('name')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $clients->items(),
            'meta' => [
                'current_page' => $clients->currentPage(),
                'last_page' => $clients->lastPage(),
                'per_page' => $clients->perPage(),
                'total' => $clients->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'vat_number' => 'nullable|string|max:50',
            'currency' => 'nullable|string|size:3',
            'hourly_rate' => 'nullable|numeric|min:0',
            'status' => 'in:active,inactive',
            'settings' => 'nullable|array',
        ]);

        $validated['user_id'] = auth()->id();

        $client = Client::create($validated);

        return response()->json([
            'data' => $client,
            'message' => 'Client created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client): JsonResponse
    {
        $this->authorize('view', $client);

        $client->load(['projects', 'invoices']);

        return response()->json([
            'data' => $client,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client): JsonResponse
    {
        $this->authorize('update', $client);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'vat_number' => 'nullable|string|max:50',
            'currency' => 'nullable|string|size:3',
            'hourly_rate' => 'nullable|numeric|min:0',
            'status' => 'in:active,inactive',
            'settings' => 'nullable|array',
        ]);

        $client->update($validated);

        return response()->json([
            'data' => $client,
            'message' => 'Client updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): JsonResponse
    {
        $this->authorize('delete', $client);

        // Check if client has projects or invoices
        if ($client->projects()->count() > 0 || $client->invoices()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete client with existing projects or invoices'
            ], 422);
        }

        $client->delete();

        return response()->json([
            'message' => 'Client deleted successfully'
        ]);
    }

    /**
     * Get client's projects
     */
    public function projects(Client $client): JsonResponse
    {
        $this->authorize('view', $client);

        $projects = $client->projects()
            ->with(['tasks', 'timeEntries'])
            ->get();

        return response()->json([
            'data' => $projects,
        ]);
    }

    /**
     * Get client's invoices
     */
    public function invoices(Client $client): JsonResponse
    {
        $this->authorize('view', $client);

        $invoices = $client->invoices()
            ->with(['items', 'payments'])
            ->orderBy('invoice_date', 'desc')
            ->get();

        return response()->json([
            'data' => $invoices,
        ]);
    }

    /**
     * Get client's time entries
     */
    public function timeEntries(Client $client): JsonResponse
    {
        $this->authorize('view', $client);

        $timeEntries = $client->timeEntries()
            ->with(['project', 'task'])
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json([
            'data' => $timeEntries,
        ]);
    }
}