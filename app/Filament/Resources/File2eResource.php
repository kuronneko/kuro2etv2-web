<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\File2e;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Services\File2eService;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\File2eResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\File2eResource\RelationManagers;

class File2eResource extends Resource
{
    protected static ?string $model = File2e::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'My Files';

    protected static ?string $breadcrumb = 'My Files';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->default(Auth::user()->id),
                TextInput::make('name')
                    ->label('Filename')
                    ->required()
                    ->maxLength(50),
                Textarea::make('text')
                    ->required()
                    ->rows(3)
                    ->autosize()
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('text_encrypted', File2eService::saveTextToHex($state))),
                Textarea::make('text_encrypted')
                    ->disabled()
                    ->rows(3)
                    ->autosize()
                    ->maxLength(65535)
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', Auth::user()->id))
            ->columns([
                TextColumn::make('id')
                ->label('ID')
                ->searchable(),
                TextColumn::make('name')
                    ->label('Filename')
                    ->icon('heroicon-m-lock-closed')
                    ->searchable()
                    ->color('danger')
                    ->limit(50)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->mutateRecordDataUsing(function (array $data): array {
                    if (Auth::user()->id != $data['user_id']) {
                        abort(404);
                    }
                    $data['text_encrypted'] = $data['text'];
                    $data['text'] = File2eService::loadHexToString($data['text']);

                    return $data;
                }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFile2es::route('/'),
            'create' => Pages\CreateFile2e::route('/create'),
            //'view' => Pages\ViewFile2e::route('/{record}'),
            'edit' => Pages\EditFile2e::route('/{record}/edit'),
        ];
    }
}
