<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::paginate(15);
        return view('items.index', compact('items'));
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:Allowed,Prohibited',
            'description' => 'nullable|string'
        ]);
        
        Item::create($validated);
        
        return redirect()->route('items.index');
    }

    public function show(Item $item)
    {
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:Allowed,Prohibited',
            'description' => 'nullable|string'
        ]);
        
        $item->update($validated);
        
        return redirect()->route('items.index')
            ->with('success', 'Элемент успешно обновлен');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        
        return redirect()->route('items.index')
            ->with('success', 'Элемент успешно удален');
    }

    public function generate()
    {
        $count = 1000;
        $statuses = ['Allowed', 'Prohibited'];

        $firstId = Item::max('id') + 1;
        $itemsToInsert = [];

        for ($i = 0; $i < $count; $i++) {
            $itemsToInsert[] = [
                'name' => 'Item ' . ($firstId + $i),
                'status' => $statuses[$i % 2],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        Item::insert($itemsToInsert);
        
        return redirect()->route('items.index')
            ->with('success', "Сгенерировано $count элементов");
    }

    public function clear()
    {
        Item::truncate();
        
        return redirect()->route('items.index')
            ->with('success', 'Таблица очищена');
    }

    public function setSheet(Request $request)
    {
        $validated = $request->validate([
            'sheet_url' => 'required|url'
        ]);
        
        $url = $validated['sheet_url'];
        preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $url, $matches);
        $sheetId = $matches[1] ?? null;
        
        if ($sheetId) {           

            // Обновляем .env файл
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);
            
            if (str_contains($envContent, 'GOOGLE_SHEET_ID=')) {
                $envContent = preg_replace(
                    '/GOOGLE_SHEET_ID=.*/',
                    'GOOGLE_SHEET_ID='.$sheetId,
                    $envContent
                );
            } else {
                $envContent .= "\nGOOGLE_SHEET_ID=".$sheetId;
            }
            
            file_put_contents($envPath, $envContent);
            
            config(['services.google.sheet_id' => $sheetId]);
            Artisan::call('config:clear');

            return redirect()->route('items.index')
                ->with('success', 'Google Таблица настроена');
        }
        
        return redirect()->route('items.index')
            ->with('error', 'Неверный URL Google Таблицы');
    }
}
