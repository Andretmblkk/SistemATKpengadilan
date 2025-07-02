<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AtkRequest;
use App\Models\PurchaseRequest;
use Illuminate\Support\Carbon;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('generate_pdf')
                ->label('Generate Laporan')
                ->form([
                    \Filament\Forms\Components\Select::make('jenis_laporan')
                        ->label('Jenis Laporan')
                        ->options([
                            'permintaan' => 'Permintaan Barang',
                            'pembelian' => 'Pengajuan Pembelian',
                        ])->required(),
                    \Filament\Forms\Components\DatePicker::make('from')->label('Dari Tanggal')->required(),
                    \Filament\Forms\Components\DatePicker::make('to')->label('Sampai Tanggal')->required(),
                ])
                ->action(function (array $data) {
                    $periode = Carbon::parse($data['from'])->format('d/m/Y') . ' - ' . Carbon::parse($data['to'])->format('d/m/Y');
                    $user = auth()->user();
                    $jenis = $data['jenis_laporan'];
                    $now = now('Asia/Jayapura');
                    if ($jenis === 'permintaan') {
                        $records = AtkRequest::with(['user', 'requestItems.item'])
                            ->whereBetween('created_at', [$data['from'], $data['to']])
                            ->get();
                        $pdf = Pdf::loadView('pdf.laporan-permintaan-barang', [
                            'data' => $records,
                            'periode' => $periode,
                        ]);
                        $title = 'Laporan Permintaan Barang';
                    } else {
                        $records = PurchaseRequest::with(['item'])
                            ->whereBetween('created_at', [$data['from'], $data['to']])
                            ->get();
                        $pdf = Pdf::loadView('pdf.laporan-pengajuan-pembelian', [
                            'data' => $records,
                            'periode' => $periode,
                        ]);
                        $title = 'Laporan Pengajuan Pembelian';
                    }
                    // Simpan file PDF ke storage/app/public/reports
                    $filename = $title . ' - ' . $periode . ' - ' . $now->format('YmdHis') . '.pdf';
                    $filename = str_replace(['/', ' '], ['-', '_'], $filename);
                    $path = 'reports/' . $filename;
                    \Storage::disk('public')->put($path, $pdf->output());

                    // Simpan ke tabel reports
                    \App\Models\Report::create([
                        'user_id' => $user->id,
                        'title' => $title,
                        'description' => 'Periode: ' . $periode,
                        'file_path' => $path,
                        'report_date' => $now->toDateString(),
                        'status' => \App\Models\Report::STATUS_DIKIRIM,
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Laporan berhasil dibuat')
                        ->success()
                        ->send();
                })
                ->visible(fn () => auth()->user()->hasRole('admin')),
        ];
    }
}
