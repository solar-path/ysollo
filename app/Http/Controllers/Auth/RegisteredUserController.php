<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\CreateTenantJob;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'workspace_name' => 'required|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        
        $tenantSlug = Tenant::generateUniqueSlug($request->workspace_name);
        
        $request->validate([
            'workspace_name' => [
                function ($attribute, $value, $fail) use ($tenantSlug) {
                    if (Tenant::where('slug', $tenantSlug)->exists()) {
                        $fail('This workspace name is already taken.');
                    }
                }
            ]
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        
        CreateTenantJob::dispatch($user, $request->workspace_name, $tenantSlug);
        
        session()->flash('message', 'Your workspace is being created! You will receive an email once it\'s ready.');

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
