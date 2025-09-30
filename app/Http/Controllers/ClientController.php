<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Rules\ValidIco;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Store a newly created client (web form submit).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('clients', 'email'),
            ],
            'phone' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'ico' => [
                'nullable',
                'string',
                'size:8',
                'regex:/^[0-9]{8}$/',
                new ValidIco,
                Rule::unique('clients', 'ico'),
            ],
            'dic' => ['nullable', 'string', 'max:15'],
        ]);

        $validated['user_id'] = $request->user()->id;

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('message', __('clients.client_created'));
    }
}
