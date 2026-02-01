<x-layouts.app title="Import Stock Prices">
    <div class="mx-auto max-w-3xl px-4 py-10">
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Import Stock Prices</h1>
                    <p class="mt-1 text-sm text-slate-600">
                        Upload an Excel file and the import will run in background (queue worker).
                    </p>
                </div>

                <a href="/api/v1/health" target="_blank"
                   class="rounded-xl bg-slate-100 px-3 py-2 text-sm font-medium text-slate-800 hover:bg-slate-200">
                    API Health
                </a>
            </div>

            @if (session('status'))
                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <div class="font-medium">Please fix the following:</div>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="mt-6 space-y-5" method="POST" action="{{ route('imports.submit') }}" enctype="multipart/form-data">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700">Company</label>
                    <select name="company_id" required
                            class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none">
                        <option value="" disabled selected>Select a company...</option>
                        @foreach ($companies as $c)
                            <option value="{{ $c->id }}">
                                #{{ $c->id }} — {{ $c->name }} @if($c->symbol) ({{ $c->symbol }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Excel file (.xlsx/.xls)</label>
                    <input name="file" type="file" required accept=".xlsx,.xls"
                           class="mt-2 block w-full text-sm file:mr-4 file:rounded-xl file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800" />
                    <p class="mt-2 text-xs text-slate-500">
                        Expected columns: <code class="rounded bg-slate-100 px-1 py-0.5">date</code> and
                        <code class="rounded bg-slate-100 px-1 py-0.5">stock_price</code> (or <code class="rounded bg-slate-100 px-1 py-0.5">price</code>).
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">
                        Upload & Start Import
                    </button>

                    <a href="{{ route('imports.form') }}"
                       class="text-sm font-medium text-slate-700 hover:text-slate-900">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-slate-500">
            No login required • UI path: <code class="rounded bg-slate-100 px-1 py-0.5">/imports</code>
        </p>
    </div>
</x-layouts.app>
