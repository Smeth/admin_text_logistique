<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Agence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'agence']);

        // Recherche
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtre par rôle
        if ($request->has('role_id') && $request->role_id) {
            $query->where('role_id', $request->role_id);
        }

        // Filtre par agence
        if ($request->has('agence_id') && $request->agence_id) {
            $query->where('agence_id', $request->agence_id);
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::orderBy('display_name')->get();
        $agences = Agence::orderBy('nom_agence')->get();

        return view('users.index', compact('users', 'roles', 'agences'));
    }

    public function create()
    {
        $roles = Role::orderBy('display_name')->get();
        $agences = Agence::orderBy('nom_agence')->get();
        return view('users.create', compact('roles', 'agences'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'agence_id' => 'nullable|exists:agences,id',
        ]);

        // Vérifier que l'agence est requise pour responsable_agence
        $role = Role::findOrFail($validated['role_id']);
        if ($role->name === 'responsable_agence' && !$validated['agence_id']) {
            return back()->withInput()
                ->with('error', 'Une agence est requise pour un responsable d\'agence.');
        }

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function show(User $user)
    {
        $user->load(['role', 'agence']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('display_name')->get();
        $agences = Agence::orderBy('nom_agence')->get();
        return view('users.edit', compact('user', 'roles', 'agences'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'agence_id' => 'nullable|exists:agences,id',
        ]);

        // Vérifier que l'agence est requise pour responsable_agence
        $role = Role::findOrFail($validated['role_id']);
        if ($role->name === 'responsable_agence' && !$validated['agence_id']) {
            return back()->withInput()
                ->with('error', 'Une agence est requise pour un responsable d\'agence.');
        }

        // Ne mettre à jour le mot de passe que s'il est fourni
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        // Empêcher la suppression du dernier admin
        if ($user->isAdmin()) {
            $adminCount = User::whereHas('role', function($q) {
                $q->where('name', 'admin');
            })->count();
            
            if ($adminCount <= 1) {
                return back()->with('error', 'Impossible de supprimer le dernier administrateur.');
            }
        }

        // Empêcher l'utilisateur de se supprimer lui-même
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
