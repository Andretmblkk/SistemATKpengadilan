<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AtkRequest;
use App\Models\PurchaseRequest;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Laporan';
    protected static ?string $pluralLabel = 'Laporan';
    protected static ?string $navigationGroup = 'Laporan';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'pimpinan']);
    }

    public static function canCreate(): bool
    {
        // Nonaktifkan fitur create manual
        return false;
    }

    public static function canEdit($record): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public static function canDelete($record): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public static function canView($record): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'pimpinan']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'pimpinan']);
    }

    public static function form(Form $form): Form
    {
        // Hilangkan form create/edit manual, return form kosong
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Laporan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Periode Laporan')
                    ->formatStateUsing(fn($state) => $state ? $state : '-')
                    ->limit(40),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Diunggah Oleh')
                    ->searchable()
                    ->sortable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('report_date')
                    ->label('Tanggal Laporan')
                    ->date('d F Y')
                    ->timezone('Asia/Jayapura')
                    ->sortable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'dikirim' => 'warning',
                        'diterima' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match($state) {
                        'dikirim' => 'Menunggu Persetujuan',
                        'diterima' => 'Diterima',
                        default => 'Tidak Diketahui',
                    }),
                \Filament\Tables\Columns\TextColumn::make('file_path')
                    ->label('Aksi')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state) return '-';
                        $url = \Storage::disk('public')->url($state);
                        $lihat = '<a href="' . $url . '" target="_blank" title="Lihat Laporan" class="text-info-600 mr-2"><svg xmlns="http://www.w3.org/2000/svg" class="inline h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg></a>';
                        $unduh = '<a href="' . $url . '" download title="Unduh Laporan" class="text-primary-600"><svg xmlns="http://www.w3.org/2000/svg" class="inline h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" /></svg></a>';
                        return $lihat . $unduh;
                    })
                    ->html(),
            ])
            ->filters([
                Filter::make('periode')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal')->displayFormat('d/m/Y')->timezone('Asia/Jayapura'),
                        DatePicker::make('to')->label('Sampai Tanggal')->displayFormat('d/m/Y')->timezone('Asia/Jayapura'),
                        Select::make('jenis_laporan')->label('Jenis Laporan')->options([
                            'permintaan' => 'Permintaan Barang',
                            'pembelian' => 'Pengajuan Pembelian',
                        ])->required(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['from'])) {
                            $query->whereDate('report_date', '>=', $data['from']);
                        }
                        if (!empty($data['to'])) {
                            $query->whereDate('report_date', '<=', $data['to']);
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Action::make('view_pdf')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => $record->file_path ? \Storage::disk('local')->url($record->file_path) : null, true)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !empty($record->file_path)),
                Action::make('generate_pdf')
                    ->label('Generate PDF')
                    ->action(function ($record, $data) {
                        // Logic generate PDF akan diimplementasikan di halaman khusus
                    })
                    ->visible(fn () => auth()->user()->hasRole('admin')),
                Action::make('kirim_laporan')
                    ->label('Kirim Laporan')
                    ->color('primary')
                    ->action(function ($record) {
                        $record->status = 'dikirim';
                        $record->save();
                        \Filament\Notifications\Notification::make()
                            ->title('Laporan dikirim ke pimpinan')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => auth()->user()->hasRole('admin') && $record->status !== 'dikirim'),
                Action::make('laporan_diterima')
                    ->label('Laporan Diterima')
                    ->color('success')
                    ->action(function ($record) {
                        $record->status = 'diterima';
                        $record->save();
                        \Filament\Notifications\Notification::make()
                            ->title('Laporan telah diterima oleh pimpinan')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => auth()->user()->hasRole('pimpinan') && $record->status === 'dikirim'),
                \Filament\Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->visible(fn () => auth()->user()->hasRole('admin')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
            'view' => Pages\ViewReport::route('/{record}'),
        ];
    }
}