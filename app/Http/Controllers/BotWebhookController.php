<?php

namespace App\Http\Controllers;

use App\Services\BotService;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Webhook pesan masuk dari Fonnte.
 * Fonnte POST: sender, message, device, ...
 */
class BotWebhookController extends Controller
{
    public function __construct(
        protected BotService $bot = new BotService(),
        protected FonnteService $fonnte = new FonnteService(),
    ) {}

    public function __invoke(Request $request)
    {
        $sender = (string) $request->input('sender', '');
        $message = (string) $request->input('message', '');

        Log::channel('fonnte')->info("Pesan masuk dari {$sender}: {$message}");

        if ($sender === '' || $message === '') {
            return response()->json(['ok' => false, 'alasan' => 'sender/message kosong']);
        }

        // Abaikan pesan grup & status broadcast
        if ($request->boolean('isGroup') || str_ends_with($sender, '@g.us')) {
            return response()->json(['ok' => true, 'alasan' => 'grup diabaikan']);
        }

        $balasan = $this->bot->handle($sender, $message);
        $kirim = $this->fonnte->kirim($sender, $balasan);

        return response()->json(['ok' => $kirim['ok']]);
    }
}
