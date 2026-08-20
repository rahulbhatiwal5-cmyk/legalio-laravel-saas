<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StateSpecificClause extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'text',
        'state',
        'questions',
        'is_active',
        'clause_type',
        'states'
    ];

    protected $casts = [
        'questions' => 'array',
        'is_active' => 'boolean',
        'states'      => 'array',
    ];

    /**
     * Get all contracts using this clause
     */
    // public function contracts()
    // {
    //     return $this->belongsToMany(ContractContent::class, 'contract_state_clause')
    //         ->withPivot('order')
    //         ->withTimestamps();
    // }

    /**
     * Get all US states
     */
    public static function getStates()
    {
        return [
            'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California',
            'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia',
            'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa',
            'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland',
            'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri',
            'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey',
            'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio',
            'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina',
            'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont',
            'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'
        ];
    }

    /**
     * Generate placeholder for AI
     */
    public function getPlaceholder()
    {
        return "{{STATE_CLAUSE_{$this->id}}}";
    }

    /**
     * Scope to filter by state
     */
    public function scopeForState($query, $state)
    {
        return $query->where('state', $state);
    }

    /**
     * Scope to get active clauses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}