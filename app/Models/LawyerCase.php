<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'lawyer_id',
    'name',
    'slug',
    'case_number',
    'jurisdiction',
    'type',
    'type_label',
    'status',
    'year',
    'court',
    'won_party',
    'motions_count',
    'motion_success_rate',
    'timeline',
    'motions',
    'parties',
    'issues',
    'documents',
    'summary',
    'key_finding',
    'rate_boxes',
    'next_steps',
])]
class LawyerCase extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (LawyerCase $case) {
            if (empty($case->slug)) {
                $case->slug = static::generateUniqueSlug($case->name);
            }
        });

        static::updating(function (LawyerCase $case) {
            if ($case->isDirty('name') && !$case->isDirty('slug')) {
                $case->slug = static::generateUniqueSlug($case->name, $case->id);
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
            'year' => 'integer',
            'motions_count' => 'integer',
            'timeline' => 'array',
            'motions' => 'array',
            'parties' => 'array',
            'issues' => 'array',
            'documents' => 'array',
            'rate_boxes' => 'array',
            'next_steps' => 'array',
        ];
    }

    /**
     * Get the lawyer associated with this case.
     */
    public function lawyer(): BelongsTo
    {
        return $this->belongsTo(Lawyer::class);
    }

    /**
     * Get all lawyers associated with this case.
     */
    public function lawyers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Lawyer::class, 'case_lawyer')->withPivot('outcome')->withTimestamps();
    }
}
