<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\TenantCreated;
use App\Mail\TenantCreationFailed;

class CreateTenantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;
    
    protected $user;
    protected $workspaceName;
    protected $tenantSlug;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $workspaceName, string $tenantSlug)
    {
        $this->user = $user;
        $this->workspaceName = $workspaceName;
        $this->tenantSlug = $tenantSlug;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Creating tenant', ['workspace' => $this->workspaceName, 'user' => $this->user->id]);
            
            $tenant = Tenant::create([
                'id' => $this->tenantSlug,
                'workspace_name' => $this->workspaceName,
                'slug' => $this->tenantSlug,
                'owner_id' => $this->user->id,
            ]);
            
            $domain = $tenant->domains()->create([
                'domain' => $this->tenantSlug . '.ysollo.com',
            ]);
            
            $tenant->users()->attach($this->user->id, [
                'role' => 'owner',
                'status' => 'active',
                'joined_at' => now(),
            ]);
            
            Log::info('Tenant created successfully', ['tenant' => $tenant->id]);
            
            if (class_exists(TenantCreated::class)) {
                Mail::to($this->user)->send(new TenantCreated($tenant, $domain->domain));
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to create tenant', [
                'workspace' => $this->workspaceName,
                'user' => $this->user->id,
                'error' => $e->getMessage()
            ]);
            
            if (class_exists(TenantCreationFailed::class)) {
                Mail::to($this->user)->send(new TenantCreationFailed($this->workspaceName, $e->getMessage()));
            }
            
            throw $e;
        }
    }
    
    public function failed(\Throwable $exception)
    {
        Log::error('Tenant creation job failed after all retries', [
            'workspace' => $this->workspaceName,
            'user' => $this->user->id,
            'error' => $exception->getMessage()
        ]);
    }
}
