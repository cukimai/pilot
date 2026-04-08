<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Channel;
use App\Enums\ConversationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
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
            'channel' => Channel::class,
            'status' => ConversationStatus::class,
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class);
    }
}
