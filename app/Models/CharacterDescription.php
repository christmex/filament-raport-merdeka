<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CharacterDescription extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function rangeCharacterDescription():BelongsTo
    {
        return $this->belongsTo(RangeCharacterDescription::class);
    }

}
