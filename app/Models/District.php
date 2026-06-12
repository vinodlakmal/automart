<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_si', 'slug'];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class)->orderBy('name');
    }

    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class);
    }
}
