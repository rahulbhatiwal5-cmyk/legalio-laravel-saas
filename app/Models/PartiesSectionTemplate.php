<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartiesSectionTemplate extends Model
{
    protected $fillable = [
        'parties_type',
        'name',
        'party_a_count',
        'party_b_count',
        'parties_section_text',
        'signature_section_text',
        'questions',
        'is_active',
    ];

    protected $casts = [
        'questions' => 'array',
        'parties_section_text' => 'array',
        'is_active' => 'boolean',
    ];


    public static function dropdownOptions(): array
    {
        return static::where('is_active', true)
            ->orderBy('parties_type')
            ->get()
            ->mapWithKeys(fn($t) => [$t->parties_type => "{$t->parties_type} — {$t->name}"])
            ->toArray();
    }
}