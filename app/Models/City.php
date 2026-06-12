<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['district_id', 'name', 'name_si', 'slug'];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class);
    }
}
