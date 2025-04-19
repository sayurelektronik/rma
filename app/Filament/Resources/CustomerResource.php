<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Laravolt\Indonesia\Models\District;


class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationGroup = 'Manajemen Pelanggan';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $label = 'Data Customer';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_category_id')
                    ->label('Kategori Customer')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->default(function () {
                        return \App\Models\CustomerCategory::where('name', 'Pelanggan')->value('id');
                    }),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Lengkap Customer')
                    ->placeholder('Masukkan nama customer'),
                Forms\Components\Select::make('district_id')
                    ->label('Kota/Kecamatan')
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search): array {
                        return District::with('city.province')
                            ->where('name', 'like', "%{$search}%")
                            ->orWhereHas(
                                'city',
                                fn($query) =>
                                $query->where('name', 'like', "%{$search}%")
                            )
                            ->limit(20)
                            ->get()
                            ->mapWithKeys(function ($district) {
                                $label = "{$district->name}, {$district->city->name}, {$district->city->province->name}";
                                return [$district->id => $label]; // ✅ ini format yang benar
                            })
                            ->toArray();
                    })
                    ->getOptionLabelUsing(function ($value): ?string {
                        $district = District::with('city.province')->find($value);
                        return $district
                            ? "{$district->name}, {$district->city->name}, {$district->city->province->name}"
                            : null;
                    })
                    ->required(),
                Forms\Components\TextInput::make('postal_code')
                    ->maxLength(10)
                    ->label('Kode Pos')
                    ->placeholder('Masukkan kode pos'),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(20)
                    ->label('No. HP/Telepon')
                    ->placeholder('Masukkan nomor hp/telepon customer'),
                Forms\Components\TextInput::make('other_contact')
                    ->maxLength(255)
                    ->label('Kontak Lainnya')
                    ->placeholder('Masukkan kontak lainnya (WhatsApp, Line, dll)'),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->label('Email Customer')
                    ->placeholder('Masukkan email customer'),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255)
                    ->label('Alamat Lengkap Customer')
                    ->placeholder('Masukkan alamat customer'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')
                    ->label('Nama Customer')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('category.name')
                    ->label('Kategori Customer')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->badge(fn($record) => $record->category->name) // Badge menggunakan nama kategori
                    ->color(fn($state) => match ($state) {
                        'Pelanggan' => 'success',  // Green
                        'Reseller' => 'info',  // biru
                        'Gold Reseller' => 'warning',  // yellow
                        'Bulk Buyer' => 'danger',  // merah
                        'Dropshipper' => 'gray',  // abu
                    }),
                TextColumn::make('phone')
                    ->label('No. HP/Telepon')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-o-phone'),
                TextColumn::make('address')
                    ->label('Alamat Lengkap')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(function ($record) {
                        return $record->address . ', ' .
                            $record->district->name . ', ' .
                            $record->district->city->name . ', ' .
                            $record->district->city->province->name . ' ' .
                            $record->postal_code;
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
