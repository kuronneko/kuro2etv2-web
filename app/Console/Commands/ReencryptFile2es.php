<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\File2e;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Schema;

class ReencryptFile2es extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Options:
     *  --chunk=100     rows per chunk
     *  --backup        store legacy values before overwriting (to column or CSV)
     *  --backup-file=  optional path for the CSV backup (defaults to storage/app/reencrypt_backup_TIMESTAMP.csv)
     */
    protected $signature = 'reencrypt:file2es {--chunk=100} {--backup} {--backup-file=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wrap legacy File2e.text values with Laravel Crypt (convert legacy Kuro hex -> Crypt-wrapped hex)';

    public function handle()
    {
        $chunk = (int) $this->option('chunk');
        $backup = (bool) $this->option('backup');
        $backupFileOption = $this->option('backup-file');

        $table = (new File2e)->getTable();
        $hasTextEncryptedColumn = Schema::hasColumn($table, 'text_encrypted');
        $hasTextLegacyColumn = Schema::hasColumn($table, 'text_legacy');

        $backupFile = $backupFileOption ?: storage_path('app/reencrypt_backup_' . date('Ymd_His') . '.csv');
        $csvHandle = null;
        if ($backup && ! $hasTextEncryptedColumn && ! $hasTextLegacyColumn) {
            // prepare CSV backup
            if (! is_dir(dirname($backupFile))) {
                mkdir(dirname($backupFile), 0755, true);
            }
            $csvHandle = fopen($backupFile, 'w');
            fputcsv($csvHandle, ['id', 'text']);
            $this->info("Backing up legacy values to {$backupFile}");
        }

        $this->info("Starting re-encryption of table {$table} (chunk={$chunk})");

        $processed = 0;
        $skipped = 0;
        $failed = 0;

        File2e::chunk($chunk, function ($rows) use (&$processed, &$skipped, &$failed, $backup, $hasTextEncryptedColumn, $hasTextLegacyColumn, $csvHandle) {
            foreach ($rows as $row) {
                try {
                    // If already Crypt-wrapped, Crypt::decryptString will succeed.
                    try {
                        Crypt::decryptString($row->text);
                        $this->info("Skipping id={$row->id} (already Crypt-wrapped)");
                        $skipped++;
                        continue;
                    } catch (DecryptException $ex) {
                        // Not Crypt-wrapped: treat as legacy value (hex string)
                    }

                    $legacyValue = $row->text;

                    // Backup legacy value if requested
                    if ($backup) {
                        if ($hasTextEncryptedColumn) {
                            $row->text_encrypted = $legacyValue;
                        } elseif ($hasTextLegacyColumn) {
                            $row->text_legacy = $legacyValue;
                        } else {
                            // CSV backup
                            if (is_resource($csvHandle)) {
                                fputcsv($csvHandle, [$row->id, $legacyValue]);
                            }
                        }
                    }

                    // Wrap legacy hex with Laravel Crypt and save
                    $row->text = Crypt::encryptString($legacyValue);
                    $row->save();

                    $this->info("Re-encrypted id={$row->id}");
                    $processed++;
                } catch (\Exception $e) {
                    $this->error("Failed id={$row->id}: " . $e->getMessage());
                    $failed++;
                    // continue
                }
            }
        });

        if (is_resource($csvHandle)) {
            fclose($csvHandle);
        }

        $this->info("Done. Processed={$processed}, Skipped={$skipped}, Failed={$failed}");

        return 0;
    }
}
