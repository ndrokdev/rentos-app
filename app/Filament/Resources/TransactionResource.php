<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Unit;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            // Pilih Customer
            Select::make('customer_id')
                ->relationship('customer', 'name') // Ambil nama dari tabel customers
                ->searchable()
                ->preload()
                ->required()
                ->label('Pelanggan'),

            // Pilih Unit (Mobil/Motor)
            Select::make('unit_id')
    ->relationship('unit', 'name', modifyQueryUsing: function (Builder $query) {
        // Hanya tampilkan unit yang statusnya 'ready'
        return $query->where('status', 'ready');
    })
    ->searchable()
    ->preload()
    ->required()
    ->label('Unit yang Disewa')
    ->live()
    ->afterStateUpdated(function (Get $get, Set $set) {
        self::calculateTotal($get, $set);
    }),

            // Tanggal Mulai
            DatePicker::make('start_date')
                ->required()
                ->label('Tanggal Ambil')
                ->live() // PENTING
                ->afterStateUpdated(function (Get $get, Set $set) {
                    self::calculateTotal($get, $set);
                }),

            // Tanggal Selesai
            DatePicker::make('end_date')
                ->required()
                ->label('Tanggal Kembali')
                ->live() // PENTING
                ->afterStateUpdated(function (Get $get, Set $set) {
                    self::calculateTotal($get, $set);
                }),

            // Total Harga (Otomatis)
            TextInput::make('total_price')
                ->readOnly() // Tidak boleh diedit manual
                ->numeric()
                ->prefix('Rp')
                ->label('Total Biaya'),

            // Status & Catatan
            Select::make('status')
                ->options([
                    'booking' => 'Booking (Belum Bayar)',
                    'ongoing' => 'Sedang Jalan (Unit Keluar)',
                    'completed' => 'Selesai (Unit Kembali)',
                    'cancelled' => 'Batal',
                ])
                ->default('booking')
                ->required(),

            Textarea::make('notes')
                ->label('Catatan (Kondisi Barang, dll)')
                ->columnSpanFull(),
        ]);
    }

    // Tambahkan function kustom ini DI BAWAH method form (di dalam class TransactionResource)
public static function calculateTotal(Get $get, Set $set): void
{
    // Ambil data dari form
    $unitId = $get('unit_id');
    $startDate = $get('start_date');
    $endDate = $get('end_date');

    // Cek apakah semua data sudah diisi?
    if ($unitId && $startDate && $endDate) {
        $unit = Unit::find($unitId);
        
        // Hitung selisih hari pakai Carbon
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        // Jika tanggal kembali sebelum tanggal ambil, set harga 0 (biar gak minus)
        if ($end->lt($start)) {
            $set('total_price', 0);
            return;
        }

        $days = $start->diffInDays($end);
        
        // Minimal hitung 1 hari sewa (jika diambil & kembali di hari sama)
        if ($days == 0) $days = 1;

        // Set Total Harga ke form
        $set('total_price', $days * $unit->price_per_day);
    }
}

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('customer.name') // Menampilkan nama customer
                ->label('Pelanggan')
                ->searchable(),
                
            TextColumn::make('unit.name') // Menampilkan nama unit
                ->label('Unit'),
                
            TextColumn::make('start_date')->date(),
            TextColumn::make('end_date')->date(),
            
            TextColumn::make('total_price')
                ->money('IDR')
                ->label('Total'),

            TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'booking' => 'gray',
                    'ongoing' => 'warning', // Kuning (Hati-hati, unit sedang dipinjam)
                    'completed' => 'success',
                    'cancelled' => 'danger',
                }),
        ])
        ->defaultSort('created_at', 'desc'); // Yang terbaru paling atas
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
