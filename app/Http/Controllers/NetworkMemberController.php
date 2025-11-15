<?php

namespace App\Http\Controllers;

use App\Models\Network;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NetworkMemberController extends Controller
{
    /**
     * Display a listing of network members
     */
    public function index(Network $network)
    {
        // Verify user is member of this network
        if (!$network->members->contains(Auth::id())) {
            abort(403, 'No tienes acceso a esta red.');
        }

        $memberRole = $network->getMemberRole(Auth::user());
        $members = $network->members()->withPivot('role', 'joined_at')->orderBy('memberships.joined_at', 'desc')->get();

        // Load reviews for statistics
        $network->load('reviews.user');

        return view('networks.members.index', compact('network', 'memberRole', 'members'));
    }

    /**
     * Show the form for inviting members
     */
    public function invite(Network $network)
    {
        $memberRole = $network->getMemberRole(Auth::user());

        // Check if user can invite
        if (!in_array($memberRole, ['owner', 'admin'])) {
            if (!$network->allow_member_invites || $memberRole !== 'member') {
                abort(403, 'No tienes permiso para invitar miembros.');
            }
        }

        return view('networks.members.invite', compact('network'));
    }

    /**
     * Send invitation (store)
     */
    public function store(Request $request, Network $network)
    {
        $memberRole = $network->getMemberRole(Auth::user());

        // Check if user can invite
        if (!in_array($memberRole, ['owner', 'admin'])) {
            if (!$network->allow_member_invites || $memberRole !== 'member') {
                abort(403, 'No tienes permiso para invitar miembros.');
            }
        }

        $validated = $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:member,admin',
        ]);

        // Only owners can add admins
        if ($validated['role'] === 'admin' && $memberRole !== 'owner') {
            $validated['role'] = 'member';
        }

        // Find user by email
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()->with('error', 'No se encontró un usuario con ese email. El usuario debe registrarse primero.');
        }

        // Check if already a member
        if ($network->members->contains($user->id)) {
            return back()->with('error', 'Este usuario ya es miembro de la red.');
        }

        // Add member
        $network->members()->attach($user->id, [
            'role' => $validated['role'],
            'joined_at' => now(),
        ]);

        return redirect()
            ->route('networks.members.index', $network)
            ->with('success', 'Miembro añadido exitosamente.');
    }

    /**
     * Update member role
     */
    public function updateRole(Request $request, Network $network, User $user)
    {
        $memberRole = $network->getMemberRole(Auth::user());

        // Only owners and admins can update roles
        if (!in_array($memberRole, ['owner', 'admin'])) {
            abort(403, 'No tienes permiso para cambiar roles.');
        }

        $validated = $request->validate([
            'role' => 'required|in:member,admin',
        ]);

        // Only owners can promote to admin
        if ($validated['role'] === 'admin' && $memberRole !== 'owner') {
            abort(403, 'Solo el propietario puede promover administradores.');
        }

        // Cannot change owner role
        $userRole = $network->getMemberRole($user);
        if ($userRole === 'owner') {
            abort(403, 'No se puede cambiar el rol del propietario.');
        }

        // Update role
        $network->members()->updateExistingPivot($user->id, [
            'role' => $validated['role'],
        ]);

        return back()->with('success', 'Rol actualizado exitosamente.');
    }

    /**
     * Remove member from network
     */
    public function destroy(Network $network, User $user)
    {
        $memberRole = $network->getMemberRole(Auth::user());

        // Only owners and admins can remove members
        if (!in_array($memberRole, ['owner', 'admin'])) {
            abort(403, 'No tienes permiso para eliminar miembros.');
        }

        // Cannot remove owner
        $userRole = $network->getMemberRole($user);
        if ($userRole === 'owner') {
            abort(403, 'No se puede eliminar al propietario.');
        }

        // Admins cannot remove other admins
        if ($memberRole === 'admin' && $userRole === 'admin') {
            abort(403, 'Los administradores no pueden eliminar a otros administradores.');
        }

        $network->members()->detach($user->id);

        return back()->with('success', 'Miembro eliminado exitosamente.');
    }
}
