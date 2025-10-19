<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Faktur Penerimaan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 4px;
            text-align: left;
        }

        .no-border td {
            border: none;
            padding: 2px 4px;
        }

        .header {
            font-weight: bold;
        }

        .title {
            text-align: center;
            margin: 10px 0;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>
    <table width="100%" class="no-border">
        <tr>
            <td width="60%">
                <b>GUDANG SAHABAT</b><br>
                Jl. Palang Merah Ind No. 16 A-B-C<br>
                Telp: 0812 5758 6688<br>
                SAMARINDA
            </td>
            <td width="40%">
                <table class="no-border">
                    <tr>
                        <td>Kepada Yth</td>
                        <td>: {{ $penerimaan->supplier->nama_supplier ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>No. Faktur</td>
                        <td>: {{ $penerimaan->no_faktur }} /
                            {{ \Carbon\Carbon::parse($penerimaan->tanggal)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td>No. Terima</td>
                        <td>: {{ $penerimaan->no_terima }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Terima</td>
                        <td>: {{ \Carbon\Carbon::parse($penerimaan->tanggal)->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="title">
        <h3>FAKTUR PENERIMAAN BARANG</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>NAMA OBAT</th>
                <th>BELI</th>
                <th>SAT</th>
                <th>HARGA</th>
                <th colspan="3">POTONGAN</th>
                <th>JUMLAH</th>
            </tr>
            <tr>
                <th colspan="5"></th>
                <th>1%</th>
                <th>2%</th>
                <th>3%</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penerimaan->details as $i => $detail)
                @php
                    $harga = $detail->harga;
                    $pot1 = $detail->potongan_1 ?? 0;
                    $pot2 = $detail->potongan_2 ?? 0;
                    $pot3 = $detail->potongan_3 ?? 0;
                    $subtotal = $detail->jumlah;
                @endphp
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $detail->obat->nama_obat }}</td>
                    <td class="center">{{ $detail->qty }}</td>
                    <td class="center">{{ $detail->satuan->nama_satuan ?? '-' }}</td>
                    <td class="right">{{ number_format($harga, 0, ',', '.') }}</td>
                    <td class="center">{{ $pot1 }}</td>
                    <td class="center">{{ $pot2 }}</td>
                    <td class="center">{{ $pot3 }}</td>
                    <td class="right">{{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br><br>
    <table width="100%" class="no-border">
        <tr>
            <td width="70%"></td>
            <td class="center">
                Samarinda, {{ \Carbon\Carbon::parse($penerimaan->tanggal)->translatedFormat('d F Y') }}<br>
                <b>Apoteker Penanggung Jawab</b>
                <br><br><br><br>
                <b>Apt. Nurlina Muliani, S.Farm, M.Farm</b><br>
                SIPA. 500.16.7/100/SIPA/100.26
            </td>
        </tr>
    </table>
</body>

</html>
