<?php

namespace App\Filament\Resources\File2eResource\Pages;

use App\Filament\Resources\File2eResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListFile2es extends ListRecords
{
    protected static string $resource = File2eResource::class;

    protected static ?string $title = 'Files';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New File'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = ['all' => Tab::make('All')->badge($this->getModel()::count())];

        $categories = Category::orderBy('id', 'asc')
            ->withCount('file2es')
            ->get();

        foreach ($categories as $category) {
            $name = $category->name;
            $slug = str($name)->slug()->toString();

            $tabs[$slug] = Tab::make($name)
                ->badge($category->file2es->count())
                ->modifyQueryUsing(function ($query) use ($category) {
                    return $query->where('category_id', $category->id);
                });
        }

        return $tabs;
    }
}
