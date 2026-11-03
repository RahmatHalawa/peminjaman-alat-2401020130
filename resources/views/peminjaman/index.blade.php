<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Peminjaman Alat Laboratorium</title>
</head>
<body>
    <h1>Sistem Peminjaman Alat Laboratorium</h1>
    <h2>NIM: 2401020130</h2>

    @if(session('success'))
        <p style="color: green; font-weight: bold;">{{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p style="color: red; font-weight: bold;">{{ session('error') }}</p>
    @endif

    <hr>

    <h3>Tambah Peminjaman Baru</h3>
    <form method="POST" action="/peminjaman">
        @csrf
        <table>
            <tr>
                <td>Kode Peminjaman:</td>
                <td>
                    <input type="text" name="kode_peminjaman" value="{{ old('kode_peminjaman') }}" maxlength="10" required>
                    @error('kode_peminjaman')
                        <span style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
            </tr>
            <tr>
                <td>Nama Peminjam:</td>
                <td>
                    <input type="text" name="nama_peminjam" value="{{ old('nama_peminjam') }}" maxlength="100" required>
                    @error('nama_peminjam')
                        <span style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
            </tr>
            <tr>
                <td>Nomor Identitas:</td>
                <td>
                    <input type="text" name="nomor_identitas" value="{{ old('nomor_identitas') }}" maxlength="30" required>
                    @error('nomor_identitas')
                        <span style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
            </tr>
            <tr>
                <td>Jumlah Alat:</td>
                <td>
                    <input type="number" name="jumlah_alat" value="{{ old('jumlah_alat') }}" min="1" max="10" required>
                    @error('jumlah_alat')
                        <span style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
            </tr>
            <tr>
                <td>Alat:</td>
                <td>
                    <select name="alat_id" required>
                        <option value="">-- Pilih Alat --</option>
                        @foreach($alat as $a)
                            <option value="{{ $a->id }}" {{ old('alat_id') == $a->id ? 'selected' : '' }}>
                                {{ $a->nama_alat }}
                            </option>
                        @endforeach
                    </select>
                    @error('alat_id')
                        <span style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit">Tambah Peminjaman</button></td>
            </tr>
        </table>
    </form>

    <hr>

    <h3>Pencarian dan Filter</h3>
    <form method="GET" action="/peminjaman">
        <table>
            <tr>
                <td>Pencarian (Kode/Nama/Identitas):</td>
                <td>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Masukkan kata kunci">
                </td>
            </tr>
            <tr>
                <td>Filter Alat:</td>
                <td>
                    <select name="filter_alat">
                        <option value="">-- Semua Alat --</option>
                        @foreach($alat as $a)
                            <option value="{{ $a->id }}" {{ ($filter_alat ?? '') == $a->id ? 'selected' : '' }}>
                                {{ $a->nama_alat }}
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit">Cari</button>
                    <a href="/peminjaman"><button type="button">Reset</button></a>
                </td>
            </tr>
        </table>
    </form>

    <hr>

    <h3>Daftar Peminjaman Alat</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Peminjaman</th>
                <th>Nama Peminjam</th>
                <th>Nomor Identitas</th>
                <th>Alat</th>
                <th>Jumlah Alat</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if(count($peminjaman) > 0)
                @php $no = 1; @endphp
                @foreach($peminjaman as $p)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $p->kode_peminjaman }}</td>
                        <td>{{ $p->nama_peminjam }}</td>
                        <td>{{ $p->nomor_identitas }}</td>
                        <td>{{ $p->nama_alat }}</td>
                        <td>{{ $p->jumlah_alat }}</td>
                        <td>
                            @if($p->jumlah_alat >= 3)
                                {{ statusJumlah($p->jumlah_alat) }}
                            @else
                                {{ statusJumlah($p->jumlah_alat) }}
                            @endif
                        </td>
                        <td>
                            <a href="/peminjaman/{{ $p->id }}/edit">
                                <button type="button">Edit</button>
                            </a>
                            <form method="POST" action="/peminjaman/{{ $p->id }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data peminjaman</td>
                </tr>
            @endif
        </tbody>
    </table>

    <hr>
    <p>Total data: {{ count($peminjaman) }}</p>
</body>
</html>
