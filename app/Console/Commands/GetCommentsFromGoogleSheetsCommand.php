<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Services\GoogleSheetsService;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class GetCommentsFromGoogleSheetsCommand extends Command
{
    protected $signature = 'sheets:get-comments {--count=}';
    
    protected $description = 'Получает комментарии из Google Sheets и выводит их в консоль';

    protected $sheetsService;
    protected $spreadsheetId;
    protected $sheetName = 'Items';
    protected $commentColumn = 'G'; // Колонка с комментариями

    public function handle()
    {
        try {
            $sheets = new GoogleSheetsService();
            $sheets->setSpreadsheetId(config('services.google.sheet_id'));
        
            //Получаем текущие данные из таблицы
            $rows = $sheets->getSheetData();
            
            $count = $this->option('count');
            if ($count && is_numeric($count)) {
                $rows = array_slice($rows, 0, (int)$count);
            }
            
            if (empty($rows)) {
                $this->info('Таблица не содержит данных');
                return 0;
            }
            
            $commentColumnIndex = $sheets->commentColumnIndex;
            
            $this->info('Получение комментариев из Google Sheets:');
            
            $bar = $this->output->createProgressBar(count($rows));
            $bar->start();
            
            $results = [];
            foreach ($rows as $row) {
                if(!$row[0]) 
                    continue;
                $id = $row[0];
                $comment = $row[$commentColumnIndex] ?? '';
                
                $results[] = [
                    'ID' => $id,
                    'Комментарий' => $comment
                ];
                
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine(2);
            
            $output = "ID\tКомментарий\n";
            $output .= "----------------\n";
            foreach ($results as $row) {
                $output .= $row['ID']."\t".$row['Комментарий']."\n";
            }
            
            // Выводим результат
            $this->line($output);
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Ошибка: '.$e->getMessage());
            return 1;
        }    
    }
}