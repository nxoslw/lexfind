<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'user_id', 'name', 'slug', 'title', 'firm', 'city', 'state', 'specialty', 'bio',
    'avatar_color', 'initials', 'email', 'phone', 'website', 'linkedin',
    'years_experience', 'cases_count', 'cases_won', 'cases_lost', 'cases_settled',
    'cases_active', 'financial_recovery', 'fee_structure', 'is_certified', 'rating',
    'availability', 'criminal_record', 'bar_discipline', 'trial_style',
    'peer_reviews', 'recent_activity', 'practice_areas', 'trial_style_details',
])]
class Lawyer extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Lawyer $lawyer) {
            if (empty($lawyer->slug)) {
                $lawyer->slug = static::generateUniqueSlug($lawyer->name);
            }
        });

        static::updating(function (Lawyer $lawyer) {
            if ($lawyer->isDirty('name') && !$lawyer->isDirty('slug')) {
                $lawyer->slug = static::generateUniqueSlug($lawyer->name, $lawyer->id);
            }
        });
    }

    public static function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = \Illuminate\Support\Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_certified' => 'boolean',
            'rating' => 'float',
            'peer_reviews' => 'array',
            'recent_activity' => 'array',
            'practice_areas' => 'array',
            'trial_style_details' => 'array',
        ];
    }

    /**
     * Get the user account assigned to this lawyer profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cases associated with this lawyer.
     */
    public function cases(): BelongsToMany
    {
        return $this->belongsToMany(LawyerCase::class, 'case_lawyer')->withPivot('outcome')->withTimestamps();
    }

    /**
     * Recalculate and update the lawyer's case outcome statistics.
     */
    public function recalculateStatistics(): void
    {
        $cases = $this->cases()->get();

        $won = 0;
        $lost = 0;
        $settled = 0;
        $active = 0;

        foreach ($cases as $case) {
            $outcome = $case->pivot->outcome ?? $case->status;
            if ($outcome === 'won') {
                $won++;
            } elseif ($outcome === 'lost') {
                $lost++;
            } elseif ($outcome === 'settled') {
                $settled++;
            } elseif ($outcome === 'active') {
                $active++;
            }
        }

        $this->update([
            'cases_count' => $won + $lost + $settled + $active,
            'cases_won' => $won,
            'cases_lost' => $lost,
            'cases_settled' => $settled,
            'cases_active' => $active,
        ]);
    }
}
