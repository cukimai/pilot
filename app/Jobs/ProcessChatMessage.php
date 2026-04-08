<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\ChatMessageReceived;
use App\Models\Conversation;
use App\Services\ChatAiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessChatMessage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 2;

    public int $timeout = 45;

    public function __construct(
        public Conversation $conversation,
    ) {
    }

    public function handle(ChatAiService $chatAiService): void
    {
        $message = $chatAiService->processMessage($this->conversation);

        try {
            ChatMessageReceived::dispatch($message);
        } catch (\Throwable $e) {
            Log::warning('Broadcasting failed (Reverb not running?)', ['error' => $e->getMessage()]);
        }
    }
}
