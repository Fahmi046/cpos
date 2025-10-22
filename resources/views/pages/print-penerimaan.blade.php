<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penerimaan Barang - Apotek Sahabat</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 10px;
            font-size: 11pt;
            line-height: 1.2;
            background: #f9f9f9;
            color: #333;
        }

        .invoice-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px;
            background: #fff;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header-section .pharmacy-info,
        .header-section .supplier-info {
            line-height: 1.2;
            font-size: 12pt;
        }

        .header-section p {
            margin: 1px 0;
        }

        .title {
            font-size: 14pt;
            font-weight: bold;
            margin: 10px 0;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 10px;
        }

        .item-table th,
        .item-table td {
            border: 1px solid #333;
            padding: 3px 5px;
        }

        .item-table th {
            background: #f0f0f0;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-top: 10px;
        }

        .summary-table td {
            border: 1px solid #333;
            padding: 5px;
        }

        .summary-table .label {
            text-align: right;
            width: 80%;
        }

        .summary-table .value {
            text-align: right;
            width: 20%;
        }

        .grand-total {
            font-weight: bold;
            border-top: 2px solid #333;
        }

        .signature-area {
            margin-top: 35px;
            display: flex;
            justify-content: space-between;
            font-size: 10pt;
        }

        .signature-box {
            text-align: center;
            line-height: 1.2;
            min-width: 200px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            height: 50px;
            margin-bottom: 5px;
        }

        @media print {
            body {
                background: #fff;
            }

            .invoice-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">

        <div class="header-section">
            <div class="pharmacy-info">
                <p style="font-weight:bold; font-size:14pt;">APOTEK SAHABAT</p>
                <p>Pulau Merah No. 16B, Samarinda</p>
                <p>Telp: 0812-2690-3184</p>
                <p>NIP/P: 09.694.517.5-722.000</p>
            </div>
            <div class="supplier-info">
                <p>Supplier: <strong>{{ $penerimaan->kreditur->nama ?? '-' }}</strong></p>
                <p>Nomor / Tanggal Faktur: <strong>{{ $penerimaan->no_faktur }} /
                        {{ \Carbon\Carbon::parse($penerimaan->tanggal)->format('d F Y') }}</strong></p>
                <p>Tanggal Terima: <strong>{{ \Carbon\Carbon::parse($penerimaan->tanggal)->format('d F Y') }}</strong>
                </p>
                <p>Nomor PO: {{ $penerimaan->no_po ?? '-' }}</p>
                <p>Cara Bayar: <strong>{{ $penerimaan->cara_bayar ?? '-' }}</strong></p>
                <p>Jatuh Tempo:
                    <strong>{{ $penerimaan->jatuh_tempo ? \Carbon\Carbon::parse($penerimaan->jatuh_tempo)->format('d F Y') : '-' }}</strong>
                </p>
            </div>
        </div>

        <div class="title">FAKTUR PENERIMAAN BARANG</div>

        <table class="item-table">
            <thead>
                <tr>
                    <th style="width:3%;">NO</th>
                    <th style="width:30%;">NAMA BARANG</th>
                    <th style="width:10%;">BATCH</th>
                    <th style="width:8%;">ED</th>
                    <th style="width:5%;">JUMLAH</th>
                    <th style="width:8%;">HARGA</th>
                    <th style="width:5%;">DISK (%)</th>
                    <th style="width:13%;">JML-HARGA</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($penerimaan->details as $i => $detail)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $detail->obat->nama_obat ?? '-' }}</td>
                        <td>{{ $detail->batch ?? '-' }}</td>
                        <td>{{ $detail->ed ? \Carbon\Carbon::parse($detail->ed)->format('d-m-Y') : '-' }}</td>
                        <td class="text-center">{{ $detail->qty }} {{ $detail->satuan->nama_satuan ?? '-' }}</td>
                        <td class="text-right">{{ number_format($detail->harga, 0, ',', '.') }}</td>
                        <td class="text-right">{{ $detail->disc1 ?? 0 }}%</td>
                        <td class="text-right">{{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $dpp = $penerimaan->dpp ?? 0;
            $ppn = $penerimaan->ppn ?? 0;
            $total = $penerimaan->total ?? 0;
        @endphp

        <!-- TOTAL SUMMARY DIBAGI 2 KOLOM -->
        <table class="summary-table" style="width:100%; border-collapse:collapse; font-size:13px;">
            <tr>
                <!-- Kolom Terbilang -->
                <td style="width:60%; vertical-align:top; padding:8px;">
                    <strong>Terbilang:</strong><br>
                    <span style="font-style:italic;">
                        {{ ucwords(\App\Helpers\Terbilang::angkaTerbilang($total)) }} Rupiah
                    </span>
                </td>

                <!-- Kolom Ringkasan Nominal -->
                <td style="width:40%; vertical-align:top; padding:0;">
                    <table style="width:100%; border-collapse:collapse; font-size:13px;">
                        <tr>
                            <td style="padding:6px; border-bottom:1px solid #000;">SUBTOTAL Rp.</td>
                            <td style="padding:6px; border-bottom:1px solid #000; text-align:right;">
                                {{ number_format($dpp, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:6px; border-bottom:1px solid #000;">PPN Rp.</td>
                            <td style="padding:6px; border-bottom:1px solid #000; text-align:right;">
                                {{ number_format($ppn, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:6px; font-weight:bold;">TOTAL Rp.</td>
                            <td style="padding:6px; font-weight:bold; text-align:right;">
                                {{ number_format($total, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>


        <!-- BAGIAN TANDA TANGAN DI LUAR TABEL -->
        <div class="signature-area"
            style="margin-top:25px; display:flex; justify-content:space-between; font-size:13px;">
            <div class="signature-box" style="text-align:center; width:45%;">
                <p style="margin-bottom:60px;">Penerima Barang,</p>
                <p>.............................</p>
                <p>Nama Terang</p>
            </div>

            <div class="signature-box" style="text-align:center; width:45%;">
                <p style="margin-bottom:60px;">Mengetahui,</p>
                <p style="font-weight:bold;">Apt. Nurlina Muliani, S.Farm, M.Farm</p>
                <p>SIPA 500.16.7/100/SIPA/108.26</p>
            </div>
        </div>


    </div>
</body>

</html>
