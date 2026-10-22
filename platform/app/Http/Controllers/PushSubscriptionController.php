<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string', 'max:4000'],
            'keys.p256dh' => ['required', 'string', 'max:2000'],
            'keys.auth' => ['required', 'string', 'max:2000'],
            'contentEncoding' => ['nullable', 'string', 'max:32'],
        ]);

        $subscription = PushSubscription::query()->updateOrCreate(
            ['endpoint_hash' => hash('sha256', $validated['endpoint'])],
            [
                'user_id' => $request->user()->id,
                'endpoint' => $validated['endpoint'],
                'public_key' => $validated['keys']['p256dh'],
                'auth_token' => $validated['keys']['auth'],
                'content_encoding' => $validated['contentEncoding'] ?? 'aes128gcm',
                'user_agent' => (string) $request->userAgent(),
                'last_seen_at' => now(),
            ],
        );

        return response()->json([
            'message' => 'Push notifikácie sú zapnuté.',
            'subscription_id' => $subscription->id,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string', 'max:4000'],
        ]);

        PushSubscription::query()
            ->where('user_id', $request->user()->id)
            ->where('endpoint_hash', hash('sha256', $validated['endpoint']))
            ->delete();

        return response()->json([
            'message' => 'Push notifikácie sú vypnuté.',
        ]);
    }
}
