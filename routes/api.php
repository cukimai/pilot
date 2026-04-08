<?php

use App\Http\Controllers\Api\VoiceWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/voice/webhook', VoiceWebhookController::class);
