<article class="sl-card p-5">
    <p class="sl-kicker">Dashboard</p>
    <h2 class="sl-section-title mt-1">Task okno a push</h2>
    <p class="sl-section-copy mt-1">Určuje, koľko dní dopredu sa ukáže v dashboard task liste a či chceš push pre nové úlohy.</p>

    <form method="POST" action="{{ route('settings.task-center.update') }}" class="mt-5 space-y-4">
        @csrf
        @method('PATCH')
        <div>
            <label class="sl-form-label">Zobraziť úlohy do X dní dopredu</label>
            <x-ts-input
                name="dashboard_task_window_days"
                :value="old('dashboard_task_window_days', auth()->user()->dashboard_task_window_days ?? 3)"
                inputmode="numeric"
                class="ts-field-reset"
            />
        </div>

        <label class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">
            <span>Push pri novej priradenej úlohe</span>
            <span class="flex items-center gap-2">
                <input type="hidden" name="web_push_notif_new_task" value="0">
                <input type="checkbox" name="web_push_notif_new_task" value="1" @checked(auth()->user()->web_push_notif_new_task) class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500/30">
            </span>
        </label>

        <div
            class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-4"
            data-web-push-panel
            data-web-push-public-key="{{ $webPushPublicKey }}"
            data-web-push-subscribe-url="{{ route('push-subscriptions.store') }}"
            data-web-push-unsubscribe-url="{{ route('push-subscriptions.destroy') }}"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <strong class="block text-sm text-slate-900">iPhone / PWA push</strong>
                    <p class="mt-1 text-sm text-slate-600">Najprv otvor Shoplink z Home Screen ikony, potom zapni push.</p>
                </div>
                <button type="button" class="sl-btn-primary" data-web-push-toggle>Zapnúť push</button>
            </div>
            <p class="mt-3 text-sm text-slate-500" data-web-push-status>Stav sa načítava...</p>
        </div>

        <button type="submit" class="sl-btn-primary w-full">Uložiť dashboard okno</button>
    </form>
</article>
