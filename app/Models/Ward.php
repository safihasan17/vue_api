<?php

namespace App\Models;

use Database\Factories\WardFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'floor', 'type'])]
class Ward extends Model
{
    /** @use HasFactory<WardFactory> */
    use HasFactory;

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }
}
