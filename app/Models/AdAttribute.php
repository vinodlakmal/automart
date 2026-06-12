<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdAttribute extends Model
{
    use HasFactory;

    protected $fillable = ['ad_id', 'attribute_key', 'attribute_value'];

    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }
}
