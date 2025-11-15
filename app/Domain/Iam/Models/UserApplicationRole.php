<?php

namespace App\Domain\Iam\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserApplicationRole extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'iam_user_application_roles';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'role_id',
        'assigned_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [];

    /**
     * Get the user this assignment belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the role in this assignment.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(ApplicationRole::class);
    }

    /**
     * Get the user who assigned this role.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_by');
    }

    /**
     * Get the application through the role.
     */
    public function application(): BelongsTo
    {
        return $this->role->application();
    }
}
