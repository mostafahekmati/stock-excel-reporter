<x-layouts.app title="Import Status">
    <div class="mx-auto max-w-3xl px-4 py-10">
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Import Status</h1>
                    <p class="mt-1 text-sm text-slate-600">
                        Import #{{ $import->id }} • Company #{{ $import->company_id }}
                    </p>
                </div>

                <a href="{{ route('imports.form') }}"
                   class="rounded-xl bg-slate-100 px-3 py-2 text-sm font-medium text-slate-800 hover:bg-slate-200">
                    ← New Import
                </a>
            </div>

            <div class="mt-6 grid gap-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="text-xs text-slate-500">Status</div>
                        <div class="mt-1 text-lg font-semibold">
                            {{ strtoupper((string) $import->status?->value ?? $import->status) }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="text-xs text-slate-500">File</div>
                        <div class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $import->original_filename }}
                        </div>
                        <div class="mt-1 text-xs text-slate-500">
                            {{ $import->stored_path }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="text-xs text-slate-500">Total rows</div>
                        <div class="mt-1 text-lg font-semibold">{{ $import->total_rows ?? '-' }}</div>
                    </div>

                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="text-xs text-slate-500">Inserted rows</div>
                        <div class="mt-1 text-lg font-semibold">{{ $import->inserted_rows ?? '-' }}</div>
                    </div>
                </div>

                @if ($import->error_message)
                    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                        <div class="font-semibold">Error</div>
                        <div class="mt-2">{{ $import->error_message }}</div>
                    </div>
                @endif

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <button type="button"
                            onclick="window.location.reload()"
                            class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        Refresh
                    </button>

                    <a href="/api/v1/stock-imports/{{ $import->id }}" target="_blank"
                       class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-200">
                        View JSON
                    </a>
                </div>

                <p class="text-xs text-slate-500">
                    This page does not auto-refresh. Click “Refresh” to update status.
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>
