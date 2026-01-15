<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitResource\Pages;
use App\Filament\Resources\UnitResource\RelationManagers;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
    ->schema([
        TextInput::make('name')
            ->required()
            ->label('Nama Unit (Mobil/Motor)'),
            
        TextInput::make('plate_number')
            ->label('Plat Nomor')
            ->unique(ignoreRecord: true), // Cek unik, kecuali punya sendiri saat edit

        TextInput::make('price_per_day')
            ->label('Harga Sewa per Hari')
            ->numeric()
            ->prefix('Rp')
            ->required(),

        Select::make('status')
            ->options([
                'ready' => 'Tersedia',
                'rented' => 'Sedang Disewa',
                'maintenance' => 'Perbaikan',
            ])
            ->default('ready')
            ->required(),

        FileUpload::make('image')
            ->label('Foto Unit')
            ->image() // Validasi harus gambar
            ->directory('units'), // Simpan di folder units
    ]);
    }

    public static function table(Table $table): Table
    {
       
// ... di dalam method table(Table $table) ...
return $table
    ->columns([
        ImageColumn::make('image')->label('Foto'),
        
        TextColumn::make('name')->searchable()->sortable(),
        
        TextColumn::make('plate_number')->label('Plat'),
        
        TextColumn::make('price_per_day')
            ->money('IDR') // Format Rupiah otomatis
            ->label('Harga/Hari'),

        TextColumn::make('status')
            ->badge() // Tampil seperti lencana
            ->color(fn (string $state): string => match ($state) {
                'ready' => 'success', // Hijau
                'rented' => 'warning', // Kuning
                'maintenance' => 'danger', // Merah
            }),
    ])
    ->filters([
        //
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
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
