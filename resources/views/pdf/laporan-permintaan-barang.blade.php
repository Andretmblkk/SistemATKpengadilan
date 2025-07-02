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
    <h2 style="text-align:center;">Laporan Permintaan Barang</h2>
    <p>Periode: {{ $periode }}</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Staff</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $row)
                @foreach($row->requestItems as $item)
                <tr>
                    <td>{{ $loop->parent->iteration }}</td>
                    <td>{{ $row->user->name ?? '-' }}</td>
                    <td>{{ $item->item->name ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html> 