<?php

namespace App\Filament\Resources\File2eResource\Pages;

use Filament\Actions;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\File2eResource;
use App\Models\File2e;

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
       // $tabs = ['all' => Tab::make('All')->badge($this->getModel()::count())];

       $tabs = ['all' => Tab::make('All')->badge(File2e::where('user_id', Auth::user()->id)->count())];

        $categories = Category::orderBy('id', 'asc')->where('user_id', Auth::user()->id)
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
