<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; font-size: 12px; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Laporan Pengajuan Pembelian</h2>
    <p>Periode: {{ $periode }}</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Jumlah Dibeli</th>
                <th>Tanggal Pengajuan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $row->item->name ?? '-' }}</td>
                <td>{{ $row->quantity ?? '-' }}</td>
                <td>{{ $row->created_at->format('d/m/Y') }}</td>
                <td>{{ ucfirst($row->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 