<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\Request;
use Google\Service\Sheets\ValueRange;
use App\Models\Item;
use Google\Service\Sheets\BatchUpdateValuesRequest;

class GoogleSheetsService
{
    protected $service;
    protected $spreadsheetId;
    protected $modelFields;
    public $commentColumnIndex;
    protected $sheetName;
    protected $sheetId;
    protected $headerRowsCount=1;

    public function __construct()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/credentials.json'));
        $client->addScope(Sheets::SPREADSHEETS);
        $this->service = new Sheets($client);
        
        $this->modelFields = \Schema::getColumnListing((new Item())->getTable());
        $this->commentColumnIndex = count($this->modelFields);
    }

    public function setSpreadsheetId($id)
    {
        $this->spreadsheetId = $id;
    }

    public function syncData($items)
    {
        
        $existingData = $this->getSheetData();

        //Создаем карту ID для быстрого поиска
        $idMap = [];
        foreach ($existingData as $index => $row) {
            if (!empty($row[0])) {
                $idMap[$row[0]] = [
                    'row' => $index + 1 + $this->headerRowsCount,
                    'comment' => $row[$this->commentColumnIndex] ?? null
                ];
            }
        }

        //Подготовка данных для обновления
        $updates = [];
        $newRows = [];
        
        foreach ($items as $item) {
            $rowData = [];
            foreach ($this->modelFields as $field) {
                $rowData[] = $this->formatValue($item->$field);
            }

            if (isset($idMap[$item->id])) {
                // Формируем диапазон обновления
                $range = sprintf(
                    '%s!A%d:%s%d',
                    $this->sheetName,
                    $idMap[$item->id]['row'],
                    $this->getColumnLetter(count($this->modelFields)),
                    $idMap[$item->id]['row']
                );
                
                $updates[] = new ValueRange([
                    'range' => $range,
                    'values' => [$rowData]
                ]);
            } else {
                // Для новых строк добавляем пустой комментарий
                $newRows[] = array_merge($rowData, ['' => null]);
            }
        }
        
        //Обновление существующих строк
        if (!empty($updates)) {
            $batchUpdate = new BatchUpdateValuesRequest([
                'data' => $updates,
                'valueInputOption' => 'RAW'
            ]);
            $this->service->spreadsheets_values->batchUpdate(
                $this->spreadsheetId,
                $batchUpdate
            );
        }

        //Добавление новых строк
        if (!empty($newRows)) {
            $this->service->spreadsheets_values->append(
                $this->spreadsheetId,
                $this->sheetName,
                new ValueRange(['values' => $newRows]),
                ['valueInputOption' => 'RAW']
            );
        }

        //Удаление строк
        $deleteRequests = [];
        $existingIds = array_keys($idMap);
        $currentIds = $items->pluck('id')->toArray();
        $idsToDelete = array_diff($existingIds, $currentIds);
        
        usort($idsToDelete, function($a, $b) use ($idMap) {
            return $idMap[$b]['row'] <=> $idMap[$a]['row'];
        });
        
        foreach ($idsToDelete as $idToDelete) {
            $deleteRequests[] = new Request([
                'deleteDimension' => [
                    'range' => [
                        'sheetId' => $this->sheetId,
                        'dimension' => 'ROWS',
                        'startIndex' => $idMap[$idToDelete]['row'] - 1,
                        'endIndex' => $idMap[$idToDelete]['row']
                    ]
                ]
            ]);
        }
        
        // Пакетное выполнение операций
        if (!empty($deleteRequests)) {
            $batchUpdateRequest = new BatchUpdateSpreadsheetRequest([
                'requests' => $deleteRequests
            ]);
            
            $this->service->spreadsheets->batchUpdate(
                $this->spreadsheetId,
                $batchUpdateRequest
            );
        }
    }

    protected function formatValue($value)
    {
        if ($value === null) {
            return '';
        }
        
        if ($value instanceof \DateTimeInterface) {
            return $value->toDateTimeString();
        }
        
        return $value;
    }

    protected function getColumnLetter($columnNumber)
    {
        $letter = '';
        while ($columnNumber > 0) {
            $temp = ($columnNumber - 1) % 26;
            $letter = chr($temp + 65) . $letter;
            $columnNumber = ($columnNumber - $temp - 1) / 26;
        }
        return $letter;
    }

    public function getSheetData()
    {
        $spreadsheet = $this->service->spreadsheets->get($this->spreadsheetId);
        $sheets = $spreadsheet->getSheets();
        if (empty($sheets)) {
            throw new \Exception("Таблица не содержит ни одного листа");
        }
        
        $this->sheetId = $sheets[0]->getProperties()->getSheetId();
        $this->sheetName = $sheets[0]->getProperties()->getTitle();
        
        $response = $this->service->spreadsheets_values->get(
            $this->spreadsheetId,
            $this->sheetName
        );
        if($response->getValues()){
            $data = $response->getValues();
            return array_slice($data, $this->headerRowsCount);
        }
        else {
            return [];
        }
    }
}