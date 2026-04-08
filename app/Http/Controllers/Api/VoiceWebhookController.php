<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessVoiceWebhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoiceWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'caller_name' => 'nullable|string',
            'caller_phone' => 'nullable|string',
            'summary' => 'nullable|string',
            'type' => 'nullable|string|in:storing,afspraak,offerte,overig',
            'urgency' => 'nullable|string|in:low,medium,high,urgent',
            'transcript' => 'nullable|array',
            'transcript.*.role' => 'required_with:transcript|string|in:user,assistant',
            'transcript.*.content' => 'required_with:transcript|string',
        ]);

        ProcessVoiceWebhook::dispatch($payload);

        return response()->json(['status' => 'ok']);
    }
}
