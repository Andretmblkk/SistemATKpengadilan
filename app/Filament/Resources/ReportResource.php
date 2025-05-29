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

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Laporan';
    protected static ?string $pluralLabel = 'Laporan';
    protected static ?string $navigationGroup = 'Laporan';

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'pimpinan']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Pengunggah')
                    ->relationship('user', 'name')
                    ->default(auth()->id())
                    ->disabled()
                    ->dehydrated(true),
                Forms\Components\TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->maxLength(65535),
                Forms\Components\FileUpload::make('file_path')
                    ->label('File Laporan (PDF)')
                    ->acceptedFileTypes(['application/pdf'])
                    ->directory('reports')
                    ->maxSize(10240) // 10MB
                    ->enableDownload()
                    ->enableOpen()
                    ->helperText('Hanya file PDF yang diizinkan.'),
                Forms\Components\DatePicker::make('report_date')
                    ->label('Tanggal Laporan')
                    ->required()
                    ->displayFormat('d/m/Y')
                    ->timezone('Asia/Jayapura')
                    ->default(now('Asia/Jayapura')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengunggah')
                    ->searchable()
                    ->sortable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state)
                    ->searchable(),
                Tables\Columns\TextColumn::make('report_date')
                    ->label('Tanggal Laporan')
                    ->date('d/m/Y')
                    ->timezone('Asia/Jayapura')
                    ->sortable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('file_path')
                    ->label('File')
                    ->formatStateUsing(fn ($state) => $state ? '<a href="' . \Illuminate\Support\Facades\Storage::url($state) . '" target="_blank">Lihat/Download PDF</a>' : '-')
                    ->html(),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Pengunggah')
                    ->relationship('user', 'name')
                    ->visible(fn () => auth()->user()->hasRole('admin'))
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['values'])) {
                            Log::info('Filtering by user_id', ['values' => $data['values']]);
                            return $query->whereIn('user_id', $data['values']);
                        }
                        return $query;
                    }),
                Filter::make('title')
                    ->form([
                        Forms\Components\TextInput::make('title_search')
                            ->label('Cari Judul')
                            ->placeholder('Masukkan kata kunci judul'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['title_search'])) {
                            Log::info('Filtering by title', ['search' => $data['title_search']]);
                            return $query->where('title', 'like', '%' . $data['title_search'] . '%');
                        }
                        return $query;
                    }),
                Filter::make('report_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal')
                            ->displayFormat('d/m/Y')
                            ->timezone('Asia/Jayapura'),
                        Forms\Components\DatePicker::make('to')
                            ->label('Sampai Tanggal')
                            ->displayFormat('d/m/Y')
                            ->timezone('Asia/Jayapura'),
                        Forms\Components\Select::make('preset_range')
                            ->label('Rentang Waktu')
                            ->options([
                                'today' => 'Hari Ini',
                                'week' => 'Minggu Ini',
                                'month' => 'Bulan Ini',
                                'year' => 'Tahun Ini',
                            ])
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                try {
                                    $today = Carbon::today('Asia/Jayapura');
                                    switch ($state) {
                                        case 'today':
                                            $set('from', $today->startOfDay()->format('Y-m-d'));
                                            $set('to', $today->endOfDay()->format('Y-m-d'));
                                            break;
                                        case 'week':
                                            $set('from', $today->startOfWeek()->format('Y-m-d'));
                                            $set('to', $today->endOfWeek()->format('Y-m-d'));
                                            break;
                                        case 'month':
                                            $set('from', $today->startOfMonth()->format('Y-m-d'));
                                            $set('to', $today->endOfMonth()->format('Y-m-d'));
                                            break;
                                        case 'year':
                                            $set('from', $today->startOfYear()->format('Y-m-d'));
                                            $set('to', $today->endOfYear()->format('Y-m-d'));
                                            break;
                                    }
                                    Log::info('Preset range applied', ['preset' => $state, 'from' => $set('from'), 'to' => $set('to')]);
                                } catch (\Exception $e) {
                                    Log::error('Error applying preset range', ['error' => $e->getMessage()]);
                                }
                            }),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        try {
                            if (!empty($data['from'])) {
                                $from = Carbon::parse($data['from'], 'Asia/Jayapura')->startOfDay();
                                $query->whereDate('report_date', '>=', $from);
                                Log::info('Filtering from date', ['from' => $from]);
                            }
                            if (!empty($data['to'])) {
                                $to = Carbon::parse($data['to'], 'Asia/Jayapura')->endOfDay();
                                $query->whereDate('report_date', '<=', $to);
                                Log::info('Filtering to date', ['to' => $to]);
                            }
                        } catch (\Exception $e) {
                            Log::error('Error filtering report_date', ['error' => $e->getMessage()]);
                        }
                        return $query;
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if (!empty($data['from'])) {
                            $indicators[] = 'Dari: ' . Carbon::parse($data['from'])->format('d/m/Y');
                        }
                        if (!empty($data['to'])) {
                            $indicators[] = 'Sampai: ' . Carbon::parse($data['to'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
            ])
            ->query(fn () => Report::query()->with('user')->latest())
            ->defaultSort('report_date', 'desc')
            ->defaultPaginationPageOption(10)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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