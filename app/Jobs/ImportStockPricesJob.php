<?php

namespace App\Jobs;

use App\Models\StockImport;
use App\Models\StockPrice;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Reader\XLSX\Reader;
use Illuminate\Support\Facades\Cache;
use Throwable;

class ImportStockPricesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly int $stockImportId) {}

    public function handle(): void
    {
        $total = 0;
        $inserted = 0;

        $reader = new Reader();

        try {
            $import = StockImport::query()->findOrFail($this->stockImportId);

            $import->update([
                'status' => 'processing',
                'error_message' => null,
                'total_rows' => null,
                'inserted_rows' => null,
            ]);



            $fullPath = Storage::disk('local')->path($import->stored_path);
            $reader->open($fullPath);

            DB::transaction(function () use ($import, $reader, &$total, &$inserted) {
                $batch = [];
                $batchSize = 1000;

                $headerFound = false;
                $dateCol = null;
                $priceCol = null;

                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {

                        $cells = $row->toArray();

                        if (empty($cells)) {
                            continue;
                        }

                        if (!$headerFound) {
                            $headers = [];
                            foreach ($cells as $idx => $val) {
                                if ($val === null) continue;

                                $h = strtolower(trim((string) $val));
                                $h = str_replace([' ', '-'], '_', $h);
                                $headers[$idx] = $h;
                            }

                            $dateCol = array_search('date', $headers, true);

                            $priceCol = array_search('stock_price', $headers, true);
                            if ($priceCol === false) $priceCol = array_search('stockprice', $headers, true);
                            if ($priceCol === false) $priceCol = array_search('price', $headers, true);

                            if ($dateCol !== false && $priceCol !== false) {
                                $headerFound = true;
                            }

                            continue;
                        }

                        $dateRaw  = $cells[$dateCol]  ?? null;
                        $priceRaw = $cells[$priceCol] ?? null;

                        if ($dateRaw === null || $dateRaw === '' || $priceRaw === null || $priceRaw === '') {
                            continue;
                        }

                        $date = $this->normalizeExcelDate($dateRaw);
                        if (!$date) continue;

                        if (is_string($priceRaw)) {
                            $priceRaw = str_replace([',', ' '], '', $priceRaw);
                        }

                        if (!is_numeric($priceRaw)) continue;

                        $batch[] = [
                            'company_id' => $import->company_id,
                            'date' => $date,
                            'price' => (float) $priceRaw,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        $total++;

                        if (count($batch) >= $batchSize) {
                            $inserted += $this->upsertBatch($batch);
                            $batch = [];
                        }
                    }

                    break;
                }

                if (!$headerFound) {
                    throw new \RuntimeException('Invalid Excel format: header "date" and "stock_price" not found.');
                }

                if (!empty($batch)) {
                    $inserted += $this->upsertBatch($batch);
                }
            });

            $import->update([
                'status' => 'done',
                'total_rows' => $total,
                'inserted_rows' => $inserted,
                'error_message' => null,
            ]);

            $revKey = "stock:rev:{$import->company_id}";
            $current = (int) Cache::get($revKey, 0);
            Cache::forever($revKey, $current + 1);


        } catch (Throwable $e) {
            report($e);

            Log::error('ImportStockPricesJob failed', [
                'stock_import_id' => $this->stockImportId,
                'error' => $e->getMessage(),
            ]);

            try {
                StockImport::query()
                    ->whereKey($this->stockImportId)
                    ->update([
                        'status' => 'failed',
                        'total_rows' => $total ?: null,
                        'inserted_rows' => $inserted ?: null,
                        'error_message' => $e->getMessage(),
                    ]);
            } catch (Throwable) {}

            return;
        } finally {
            try { $reader->close(); } catch (Throwable) {}
        }
    }

    private function upsertBatch(array $rows): int
    {
        StockPrice::query()->upsert(
            $rows,
            ['company_id', 'date'],
            ['price', 'updated_at']
        );

        return count($rows);
    }

    private function normalizeExcelDate(mixed $value): ?string
    {
        try {
            if (is_numeric($value)) {
                return Carbon::create(1899, 12, 30)
                    ->addDays((int) $value)
                    ->toDateString();
            }

            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->toDateString();
            }

            if (is_string($value)) {
                $value = trim($value);
                if ($value === '') return null;

                return Carbon::parse($value)->toDateString();
            }

            return null;
        } catch (Throwable) {
            return null;
        }
    }
}
