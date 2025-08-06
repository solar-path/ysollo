<?php

namespace App\Models;

use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;
    
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'workspace_name',
            'slug',
            'owner_id',
        ];
    }
    
    protected $casts = [
        'data' => 'array',
    ];
    
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($tenant) {
            if (empty($tenant->slug) && !empty($tenant->workspace_name)) {
                $tenant->slug = static::generateUniqueSlug($tenant->workspace_name);
            }
            
            if (empty($tenant->id)) {
                $tenant->id = $tenant->slug;
            }
        });
    }
    
    public static function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;
        
        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_users', 'tenant_id', 'user_id')
            ->withPivot(['role', 'status', 'joined_at'])
            ->withTimestamps();
    }
    
    public function activeUsers()
    {
        return $this->users()->wherePivot('status', 'active');
    }
    
    public function pendingUsers()
    {
        return $this->users()->wherePivot('status', 'pending');
    }
    
    public function getDatabaseName(): string
    {
        return $this->slug . '_tenant';
    }
    
    public function getDomainName(): string
    {
        return $this->slug . '.ysollo.com';
    }
}