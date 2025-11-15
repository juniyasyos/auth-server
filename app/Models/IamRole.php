<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IamRole extends Model
{
    protected $table = 'iam_roles';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'application_id',
        'slug',
        'name',
        'description',
        'is_system',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * Get the application this role belongs to.
     */
    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    /**
     * Get all users that have this role.
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_application_roles',
            'role_id',
            'user_id'
        )
            ->withPivot('assigned_by')
            ->withTimestamps();
    }
}
