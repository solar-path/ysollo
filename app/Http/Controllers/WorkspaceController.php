<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WorkspaceController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        $workspaces = $user->tenants()
            ->with(['owner', 'domains'])
            ->withCount('activeUsers')
            ->get()
            ->map(function ($tenant) {
                return [
                    'id' => $tenant->id,
                    'workspace_name' => $tenant->workspace_name,
                    'slug' => $tenant->slug,
                    'domain' => $tenant->domains->first()?->domain,
                    'role' => $tenant->pivot->role,
                    'status' => $tenant->pivot->status,
                    'joined_at' => $tenant->pivot->joined_at,
                    'owner' => [
                        'id' => $tenant->owner->id,
                        'name' => $tenant->owner->name,
                        'email' => $tenant->owner->email,
                    ],
                    'users_count' => $tenant->active_users_count,
                    'monthly_cost' => $tenant->active_users_count * 25,
                    'created_at' => $tenant->created_at,
                ];
            });
        
        $ownedWorkspaces = $user->ownedTenants()
            ->with(['domains'])
            ->withCount('activeUsers')
            ->get()
            ->map(function ($tenant) {
                return [
                    'id' => $tenant->id,
                    'workspace_name' => $tenant->workspace_name,
                    'slug' => $tenant->slug,
                    'domain' => $tenant->domains->first()?->domain,
                    'users_count' => $tenant->active_users_count,
                    'monthly_cost' => $tenant->active_users_count * 25,
                    'created_at' => $tenant->created_at,
                ];
            });
        
        return Inertia::render('workspaces/index', [
            'workspaces' => $workspaces,
            'ownedWorkspaces' => $ownedWorkspaces,
            'totalMonthlyBilling' => $ownedWorkspaces->sum('monthly_cost'),
        ]);
    }
    
    public function show(Request $request, string $tenantId): Response
    {
        $tenant = Tenant::with(['owner', 'domains', 'users' => function ($query) {
            $query->withPivot(['role', 'status', 'joined_at']);
        }])->findOrFail($tenantId);
        
        if (!$request->user()->canAccessTenant($tenant) && !$request->user()->isOwnerOf($tenant)) {
            abort(403, 'You do not have access to this workspace.');
        }
        
        $users = $tenant->users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->pivot->role,
                'status' => $user->pivot->status,
                'joined_at' => $user->pivot->joined_at,
            ];
        });
        
        return Inertia::render('workspaces/show', [
            'workspace' => [
                'id' => $tenant->id,
                'workspace_name' => $tenant->workspace_name,
                'slug' => $tenant->slug,
                'domain' => $tenant->domains->first()?->domain,
                'owner' => [
                    'id' => $tenant->owner->id,
                    'name' => $tenant->owner->name,
                    'email' => $tenant->owner->email,
                ],
                'created_at' => $tenant->created_at,
            ],
            'users' => $users,
            'monthlyBilling' => $users->where('status', 'active')->count() * 25,
            'isOwner' => $request->user()->isOwnerOf($tenant),
        ]);
    }
    
    public function switchWorkspace(Request $request, string $tenantId)
    {
        $tenant = Tenant::with('domains')->findOrFail($tenantId);
        
        if (!$request->user()->canAccessTenant($tenant)) {
            abort(403, 'You do not have access to this workspace.');
        }
        
        $domain = $tenant->domains->first();
        
        if (!$domain) {
            return back()->with('error', 'Workspace domain not configured.');
        }
        
        $protocol = $request->secure() ? 'https' : 'http';
        $url = "{$protocol}://{$domain->domain}";
        
        return redirect()->away($url);
    }
}