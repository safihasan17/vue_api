<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class Role extends Model
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory;

    /**
     * Roles 1-4 (Super Admin, Doctor, Receptionist, Pharmacist) are used by the
     * route middleware (role_id:1,2,3,4) and the sidebar, so they can't be deleted.
     */
    public const PROTECTED_IDS = [1, 2, 3, 4];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function isProtected(): bool
    {
        return in_array($this->id, self::PROTECTED_IDS, true);
    }
}
