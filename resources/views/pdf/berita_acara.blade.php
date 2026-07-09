<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Pemusnahan Rekam Medis</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px double #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            text-transform: uppercase;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0;
            font-size: 9px;
            color: #666;
        }
        .title {
            text-align: center;
            margin-bottom: 20px;
        }
        .title h2 {
            font-size: 13px;
            margin: 0;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .title p {
            margin: 3px 0;
            font-size: 10px;
            font-weight: bold;
        }
        .intro {
            margin-bottom: 15px;
            text-align: justify;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .table-data th, .table-data td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 9px;
        }
        .table-data th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .table-data td {
            text-align: left;
        }
        .table-data .center {
            text-align: center;
        }
        .signatures {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signatures td {
            width: 50%;
            vertical-align: top;
        }
        .signatures .title-sig {
            font-weight: bold;
            margin-bottom: 50px;
            text-align: center;
        }
        .signatures .name-sig {
            font-weight: bold;
            text-decoration: underline;
            text-align: center;
        }
        .signatures .role-sig {
            font-size: 9px;
            color: #666;
            text-align: center;
        }
        .team-members {
            margin-top: 15px;
            font-size: 9px;
        }
        .team-members ol {
            margin: 3px 0 0 15px;
            padding: 0;
        }
    </style>
</head>
<body>

    <!-- Kop Surat -->
    <div class="header">
        <h1>RSU KALIWATES JEMBER</h1>
        <p>Jl. Raya Kaliwates No. 123, Kaliwates, Jember, Jawa Timur</p>
        <p>Telp: (0331) 487123 | Email: info@rsukaliwates.co.id</p>
    </div>

    <!-- Judul Berita Acara -->
    <div class="title">
        <h2>BERITA ACARA PEMUSNAHAN REKAM MEDIS</h2>
        <p>Nomor SK: {{ $pengajuan->no_sk }}</p>
    </div>

    <!-- Pembuka -->
    <div class="intro">
        Pada hari ini, <strong>{{ $hariIni }}</strong> tanggal <strong>{{ $tanggalHariIni }}</strong>, kami yang bertanda tangan di bawah ini Tim Pemusnahan Rekam Medis RSU Kaliwates Jember, telah melaksanakan pemusnahan dokumen Rekam Medis inaktif yang telah habis jangka waktu penyimpanannya (retensi) berdasarkan Surat Keputusan Direktur Nomor <strong>{{ $pengajuan->no_sk }}</strong>, dengan rincian berkas sebagai berikut:
    </div>

    <!-- Tabel Lampiran Berkas -->
    <table class="table-data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">No. RM</th>
                <th width="30%">Nama Pasien</th>
                <th width="15%">Jenis Kelamin</th>
                <th width="20%">Tanggal Retensi</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($berkas as $idx => $item)
                <tr>
                    <td class="center">{{ $idx + 1 }}</td>
                    <td class="center" style="font-weight: bold;">{{ $item->no_rm }}</td>
                    <td>{{ $item->pasien?->nama_pasien ?? '-' }}</td>
                    <td class="center">{{ $item->pasien?->jenis_kelamin ?? '-' }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_retensi)->format('d/m/Y') }}</td>
                    <td class="center" style="color: red; font-weight: bold;">{{ ucfirst($item->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="intro">
        Pemusnahan berkas fisik dilakukan dengan cara dibakar/dicacah/dihancurkan sehingga tidak dapat dikenali lagi isinya, sesuai dengan regulasi pemusnahan arsip rekam medis yang berlaku.
    </div>

    <!-- Tanda Tangan -->
    <table class="signatures">
        <tr>
            <td>
                <div class="title-sig">
                    Mengetahui / Menyetujui,<br>
                    Direktur RSU Kaliwates Jember
                </div>
                <div class="name-sig" style="margin-top: 50px;">
                    ( .................................................. )
                </div>
                <div class="role-sig">Direktur</div>
            </td>
            <td>
                <div class="title-sig">
                    Jember, {{ $tanggalSekarang }}<br>
                    Ketua Tim Pemusnahan
                </div>
                <div class="name-sig" style="margin-top: 50px;">
                    {{ $pengajuan->ketua_tim }}
                </div>
                <div class="role-sig">Ketua Tim</div>
            </td>
        </tr>
    </table>

    <!-- Anggota Tim -->
    <div class="team-members">
        <strong>Anggota Tim Pemusnahan:</strong>
        <ol>
            <li>{{ $pengajuan->anggota_tim_1 }}</li>
            @if($pengajuan->anggota_tim_2 && $pengajuan->anggota_tim_2 !== '-')
                <li>{{ $pengajuan->anggota_tim_2 }}</li>
            @endif
            @if($pengajuan->anggota_tim_3 && $pengajuan->anggota_tim_3 !== '-')
                <li>{{ $pengajuan->anggota_tim_3 }}</li>
            @endif
            @if($pengajuan->anggota_tim_4 && $pengajuan->anggota_tim_4 !== '-')
                <li>{{ $pengajuan->anggota_tim_4 }}</li>
            @endif
            @if($pengajuan->anggota_tim_5 && $pengajuan->anggota_tim_5 !== '-')
                <li>{{ $pengajuan->anggota_tim_5 }}</li>
            @endif
            @if($pengajuan->anggota_tim_6 && $pengajuan->anggota_tim_6 !== '-')
                <li>{{ $pengajuan->anggota_tim_6 }}</li>
            @endif
            @if($pengajuan->anggota_tim_7 && $pengajuan->anggota_tim_7 !== '-')
                <li>{{ $pengajuan->anggota_tim_7 }}</li>
            @endif
            @if($pengajuan->anggota_tim_8 && $pengajuan->anggota_tim_8 !== '-')
                <li>{{ $pengajuan->anggota_tim_8 }}</li>
            @endif
        </ol>
    </div>

</body>
</html>
