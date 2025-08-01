<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Services\GoogleSheetsService;

class SyncGoogleSheetsCommand extends Command
{
    protected $signature = 'sheets:sync {--count=}';
    protected $description = 'Синхронизирует данные из БД с Google Sheets';

    public function handle()
    {
        $sheets = new GoogleSheetsService();
        $sheets->setSpreadsheetId(config('services.google.sheet_id'));
        
        $items = Item::allowed()->get();
        
        if ($count = $this->option('count')) {
            $items = $items->take($count);
        }
        
        $this->info('Starting synchronization...');
        $bar = $this->output->createProgressBar(count($items));
            
        $sheets->syncData($items);
        $bar->finish();
        
        $this->info("\nSynchronization complete!");
    }
}