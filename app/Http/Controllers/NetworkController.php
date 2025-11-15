<?php

namespace App\Http\Controllers;

use App\Models\Network;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NetworkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $networks = auth()->user()->networks()->withCount(['members', 'reviews'])->get();
        return view('networks.index', compact('networks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('networks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'allow_member_invites' => 'boolean',
        ]);

        // Crear la red
        $network = Network::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'allow_member_invites' => $validated['allow_member_invites'] ?? false,
            'is_active' => true,
        ]);

        // Agregar al usuario como owner
        $network->members()->attach(auth()->id(), [
            'role' => 'owner',
            'joined_at' => now(),
        ]);

        return redirect()
            ->route('networks.show', $network)
            ->with('success', '¡Red creada exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Network $network)
    {
        // Verificar que el usuario es miembro
        if (!$network->hasMember(auth()->user())) {
            abort(403, 'No tienes acceso a esta red.');
        }

        $network->load(['members', 'reviews.user', 'reviews.restaurant']);
        $memberRole = $network->getMemberRole(auth()->user());

        return view('networks.show', compact('network', 'memberRole'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Network $network)
    {
        // Solo owner puede editar
        if ($network->getMemberRole(auth()->user()) !== 'owner') {
            abort(403, 'Solo el propietario puede editar la red.');
        }

        return view('networks.edit', compact('network'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Network $network)
    {
        // Solo owner puede editar
        if ($network->getMemberRole(auth()->user()) !== 'owner') {
            abort(403, 'Solo el propietario puede editar la red.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'allow_member_invites' => 'boolean',
        ]);

        $network->update($validated);

        return redirect()
            ->route('networks.show', $network)
            ->with('success', 'Red actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Network $network)
    {
        // Solo owner puede eliminar
        if ($network->getMemberRole(auth()->user()) !== 'owner') {
            abort(403, 'Solo el propietario puede eliminar la red.');
        }

        $network->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Red eliminada exitosamente.');
    }
}
