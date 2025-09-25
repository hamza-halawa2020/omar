<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\CreateBackup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends BaseController
{
    public function __construct()
    {
        // $this->middleware('check.permission:categories_index')->only('index');
        // $this->middleware('check.permission:categories_update')->only(['edit', 'update']);

        $this->middleware('auth');
    }

    public function index()
    {
        return view('dashboard.profile.index');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            if ($request->hasFile('profile_image')) {
                if ($user->profile_image) {
                    Storage::delete('public/'.$user->profile_image);
                }
                $path = $request->file('profile_image')->store('profile_images', 'public');
                $validated['profile_image'] = $path;
            }

            $user->update($validated);

            //event(new CreateBackup);

            return response()->json([
                'message' => __('messages.profile_updated_successfully'),
                'profile_image' => $user->profile_image ?? null,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => __('messages.failed_to_update_profile').': '.$e->getMessage(),
            ], 500);
        }
    }
}
