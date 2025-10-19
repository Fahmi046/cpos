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
    <table width="100%" class="no-border" style="vertical-align: top; font-size: 12px; line-height: 1.4;">
        <tr>
            <!-- KOLOM KIRI -->
            <td width="60%" style="vertical-align: top; padding-right: 10px;">
                <b style="font-size: 14px;">GUDANG SAHABAT</b><br>
                Jl. Palang Merah Ind No. 16 A-B-C<br>
                Telp: 0812 5758 6688<br>
                SAMARINDA
            </td>

            <!-- KOLOM KANAN -->
            <td width="40%" style="vertical-align: top;">
                <table class="no-border" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 20%; white-space: nowrap; vertical-align: top;">Kepada Yth</td>
                        <td style="width: 80%; vertical-align: top;">: {{ $penerimaan->kreditur->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="white-space: nowrap; vertical-align: top;">No. Faktur</td>
                        <td style="vertical-align: top;">: {{ $penerimaan->no_faktur }} /
                            {{ \Carbon\Carbon::parse($penerimaan->tanggal)->format('d/m/Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="white-space: nowrap; vertical-align: top;">No. Terima</td>
                        <td style="vertical-align: top;">: {{ $penerimaan->no_penerimaan }}</td>
                    </tr>
                    <tr>
                        <td style="white-space: nowrap; vertical-align: top;">Tanggal Terima</td>
                        <td style="vertical-align: top;">:
                            {{ \Carbon\Carbon::parse($penerimaan->tanggal)->format('d/m/Y') }}</td>
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
                <th>DISKON 1</th>
                <th>DISKON 2</th>
                <th>DISKON 3</th>
                <th>JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penerimaan->details as $i => $detail)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $detail->obat->nama_obat ?? '-' }}</td>
                    <td class="center">{{ $detail->qty }}</td>
                    <td class="center">
                        {{ $detail->utuhan == 1 ? $detail->sediaan->nama_sediaan ?? '-' : $detail->satuan->nama_satuan ?? '-' }}
                    </td>
                    <td class="right">
                        {{ number_format($detail->utuhan == 1 ? $detail->harga * ($detail->obat->isi_obat ?? 1) : $detail->harga, 0, ',', '.') }}
                    </td>

                    <td class="center">{{ $detail->disc1 ?? 0 }}%</td>
                    <td class="center">{{ $detail->disc2 ?? 0 }}%</td>
                    <td class="center">{{ $detail->disc3 ?? 0 }}%</td>
                    <td class="right">{{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="8" class="right"><b>TOTAL</b></td>
                <td class="right"><b>{{ number_format($penerimaan->total ?? 0, 0, ',', '.') }}</b></td>
            </tr>
        </tbody>
    </table>

    <br><br>
    <table width="100%" class="no-border">
        <tr>
            <td width="60%"></td>
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
