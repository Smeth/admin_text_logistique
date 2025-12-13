<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('display_name')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Role::create($validated);

        return redirect()->route('roles.index')
            ->with('success', 'Rôle créé avec succès.');
    }

    public function show(Role $role)
    {
        $role->load('users.agence');
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $role->update($validated);

        return redirect()->route('roles.index')
            ->with('success', 'Rôle mis à jour avec succès.');
    }

    public function destroy(Role $role)
    {
        // Empêcher la suppression des rôles système
        $rolesSysteme = ['admin', 'agent', 'responsable_agence', 'superviseur'];
        if (in_array($role->name, $rolesSysteme)) {
            return back()->with('error', 'Ce rôle système ne peut pas être supprimé.');
        }

        // Vérifier si des utilisateurs utilisent ce rôle
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer un rôle utilisé par des utilisateurs.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rôle supprimé avec succès.');
    }
}
