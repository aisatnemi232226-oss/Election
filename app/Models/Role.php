<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Role extends Model
{
    use SoftDeletes;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'description',
        'permissions',
        'status'
    ];

    protected $dates = [
        'created_at',
        'deleted_at'
    ];

    protected $casts = [
        'permissions' => 'array',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Accessor for permissions to handle both array and string formats
    public function getPermissionsAttribute($value)
    {
        if (is_string($value)) {
            // If stored as comma-separated string, convert to array
            return explode(', ', $value);
        }
        return $value ?: [];
    }

    // Mutator for permissions to store as comma-separated string
    public function setPermissionsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['permissions'] = implode(', ', $value);
        } else {
            $this->attributes['permissions'] = $value;
        }
    }

    // Get formatted permissions for display
    public function getFormattedPermissionsAttribute()
    {
        $permissions = $this->permissions;
        if (is_array($permissions)) {
            return implode(', ', $permissions);
        }
        return $permissions ?: 'No permissions';
    }

    // Scope for active roles
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Check if role has specific permission
    public function hasPermission($permission)
    {
        $permissions = $this->permissions;
        if (is_array($permissions)) {
            return in_array($permission, $permissions);
        }
        return strpos($permissions, $permission) !== false;
    }

    // Get all available permissions
    public static function getAvailablePermissions()
    {
        return [
            'manage-elections-view' => 'Manage Elections - View',
            'manage-elections-add' => 'Manage Elections - Add',
            'manage-elections-edit' => 'Manage Elections - Edit',
            'manage-elections-delete' => 'Manage Elections - Delete',
            'manage-candidates-view' => 'Manage Candidates - View',
            'manage-candidates-add' => 'Manage Candidates - Add',
            'manage-candidates-edit' => 'Manage Candidates - Edit',
            'manage-candidates-delete' => 'Manage Candidates - Delete',
            'manage-positions-view' => 'Manage Positions - View',
            'manage-positions-add' => 'Manage Positions - Add',
            'manage-positions-edit' => 'Manage Positions - Edit',
            'manage-positions-delete' => 'Manage Positions - Delete',
            'manage-voters-view' => 'Manage Voters - View',
            'manage-voters-add' => 'Manage Voters - Add',
            'manage-voters-edit' => 'Manage Voters - Edit',
            'manage-voters-delete' => 'Manage Voters - Delete',
            'manage-articles-view' => 'Manage Articles - View',
            'manage-articles-add' => 'Manage Articles - Add',
            'manage-articles-edit' => 'Manage Articles - Edit',
            'manage-articles-delete' => 'Manage Articles - Delete',
            'view-results' => 'View Results',
            'reports' => 'Reports',
            'admin-roles-view' => 'Admin Roles - View',
            'admin-roles-add' => 'Admin Roles - Add',
            'admin-roles-edit' => 'Admin Roles - Edit',
            'admin-roles-delete' => 'Admin Roles - Delete',
            'audit-logs-view' => 'Audit Logs - View',
            'verify-voters-view' => 'Verify Voters - View',
            'verify-voters-add' => 'Verify Voters - Add',
            'verify-voters-edit' => 'Verify Voters - Edit',
            'verify-voters-delete' => 'Verify Voters - Delete',
            'set-academic-year-view' => 'Set Academic Year - View',
            'set-academic-year-add' => 'Set Academic Year - Add',
            'set-academic-year-edit' => 'Set Academic Year - Edit',
            'set-academic-year-delete' => 'Set Academic Year - Delete'
        ];
    }
}
