<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
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

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
    ->schema([
        TextInput::make('name')->required()->label('Nama Lengkap'),
        
        TextInput::make('phone')
            ->tel()
            ->required()
            ->label('Nomor WA'),
            
        TextInput::make('nik_ktp')->label('NIK KTP'),
        
        FileUpload::make('photo_ktp')
            ->image()
            ->directory('ktp')
            ->label('Foto KTP'),
            
        Select::make('is_blacklisted')
            ->options([
                0 => 'Aman',
                1 => 'Blacklist',
            ])
            ->default(0)
            ->label('Status Pelanggan'),
    ]);
    }

    public static function table(Table $table): Table
    {
        return $table
    ->columns([
        TextColumn::make('name')->searchable(),
        TextColumn::make('phone')->label('WhatsApp'),
        TextColumn::make('nik_ktp')->label('NIK'),
        TextColumn::make('is_blacklisted')
            ->badge()
            ->color(fn ($state) => $state ? 'danger' : 'success')
            ->formatStateUsing(fn ($state) => $state ? 'Blacklist' : 'Aman'),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
