<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak SP</title>
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

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 5px;
            text-align: left;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <table width="100%">
        <tr>
            <td>
                <b>APOTEK SAHABAT</b><br>
                Jl. Palang Merah No. 16B Samarinda<br>
                Telp : 0812-2590-3164<br>
                NPWP : 09.694.517.5-722.000<br>
                1244000411102
            </td>
            <td>
                <table>
                    <tr>
                        <td>Tanggal Pesanan</td>
                        <td>: {{ $pesanan->tanggal }}</td>
                    </tr>
                    <tr>
                        <td>Kepada</td>
                        <td>: {{ $pesanan->details->first()->pabrik->nama_pabrik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>: {{ $pesanan->details->first()->pabrik->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Telp</td>
                        <td>: {{ $pesanan->details->first()->pabrik->telepon ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="header">
        <h3>PURCHASE ORDER - {{ $pesanan->no_sp }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>NAMA BARANG</th>
                <th>QTY</th>
                <th>SATUAN</th>
                <th>KOMPOSISI</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pesanan->details as $i => $detail)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $detail->obat->nama_obat }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td>{{ $detail->utuhan ? $detail->satuan->nama_satuan : $detail->sediaan->nama_sediaan }}</td>
                    <td>{{ $detail->obat->komposisi->nama_komposisi ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br><br>
    <p>Apoteker Penanggung Jawab</p>
    <br><br><br>
    <b>Apt. Nurlina Muliani, S.Farm, M.Farm</b><br>
    SIPA. 500.16.7/100/SIPA/100.26
</body>

</html>
