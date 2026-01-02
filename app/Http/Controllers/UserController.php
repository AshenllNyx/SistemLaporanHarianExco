<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display list of users with filtering and search (admin only)
     */
    public function index(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->level != 'admin') {
            return redirect()->route('homepage')->with('error', 'Akses ditolak.');
        }

        $query = User::query();
        $search = $request->get('search');
        $status = $request->get('status'); // all, approved, not-approved

        // Search by name, email, or username
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%");
        }

        // Filter by approval status
        if ($status === 'approved') {
            $query->where('is_approved', true);
        } elseif ($status === 'not-approved') {
            $query->where('is_approved', false);
        }

        $users = $query->get();
        return view('user', compact('users', 'search', 'status'));
    }

    /**
     * Show edit profile form for user
     */
    public function editProfile()
    {
        $user = Auth::user();

        // Redirect admins to admin profile edit page
        if ($user->level == 'admin') {
            return redirect()->route('profile.editAdmin');
        }

        return view('edit-profile');
    }

    /**
     * Show edit profile form for admin
     */
    public function editProfileAdmin()
    {
        $user = Auth::user();

        // Ensure only admins can access this
        if ($user->level != 'admin') {
            return redirect()->route('profile.edit');
        }

        return view('edit-profile-admin');
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->no_ic . ',no_ic',
            'jantina' => 'required|in:Lelaki,Perempuan',
            'password' => 'nullable|min:8|confirmed',
        ], [
            'name.required' => 'Nama penuh diperlukan.',
            'email.required' => 'Email diperlukan.',
            'email.email' => 'Email tidak sah.',
            'email.unique' => 'Email ini sudah digunakan.',
            'jantina.required' => 'Jantina diperlukan.',
            'password.min' => 'Kata laluan mesti sekurang-kurangnya 8 aksara.',
            'password.confirmed' => 'Pengesahan kata laluan tidak sepadan.',
        ]);

        // Update user data
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'jantina' => $validated['jantina'],
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        // Redirect to appropriate homepage based on user level
        $redirectRoute = $user->level == 'admin' ? 'homepage.admin' : 'homepage';

        return redirect()->route($redirectRoute)->with('success', 'Profil anda telah berjaya dikemaskini.');
    }

    /**
     * Approve a pending user (admin only)
     */
    public function approve($no_ic)
    {
        // Check if user is admin
        if (Auth::user()->level != 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $user = User::findOrFail($no_ic);
        $user->update(['is_approved' => true]);

        return redirect()->route('users.index')->with('success', "Pengguna {$user->name} telah disahkan.");
    }

    /**
     * Show edit form for a user (admin only)
     */
    public function edit($no_ic)
    {
        // Check if user is admin
        if (Auth::user()->level != 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $editUser = User::findOrFail($no_ic);
        return view('user-edit', compact('editUser'));
    }

    /**
     * Update a user's data (admin only)
     */
    public function update(Request $request, $no_ic)
    {
        // Check if user is admin
        if (Auth::user()->level != 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $editUser = User::findOrFail($no_ic);

        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $no_ic . ',no_ic',
            'user_name' => 'required|string|unique:users,user_name,' . $no_ic . ',no_ic',
            'jantina' => 'required|in:Lelaki,Perempuan',
            'level' => 'required|in:user,admin',
        ], [
            'name.required' => 'Nama penuh diperlukan.',
            'email.required' => 'Email diperlukan.',
            'email.email' => 'Email tidak sah.',
            'email.unique' => 'Email ini sudah digunakan.',
            'user_name.required' => 'Username diperlukan.',
            'user_name.unique' => 'Username ini sudah digunakan.',
            'jantina.required' => 'Jantina diperlukan.',
            'level.required' => 'Tahap pengguna diperlukan.',
        ]);

        $editUser->update($validated);

        return redirect()->route('users.index')->with('success', "Data pengguna {$editUser->name} telah berjaya dikemaskini.");
    }

    /**
     * Delete a user (admin only)
     */
    public function destroy($no_ic)
    {
        // Check if user is admin
        if (Auth::user()->level != 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $user = User::findOrFail($no_ic);
        $userName = $user->name;
        $user->delete();

        return redirect()->route('users.index')->with('success', "Pengguna {$userName} telah berjaya dihapus.");
    }

}