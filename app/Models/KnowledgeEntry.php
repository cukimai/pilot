<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\KnowledgeCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class KnowledgeEntry extends Model
{
    use HasUuids;

    /** @var array<int, string> */
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => KnowledgeCategory::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include active entries.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory(Builder $query, KnowledgeCategory $category): Builder
    {
        return $query->where('category', $category);
    }
}
