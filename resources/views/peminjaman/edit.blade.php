<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Peminjaman Alat</title>
</head>
<body>
    <h1>Edit Peminjaman Alat</h1>
    <h2>NIM: 2401020130</h2>

    @if(session('error'))
        <p style="color: red; font-weight: bold;">{{ session('error') }}</p>
    @endif

    <hr>

    <form method="POST" action="/peminjaman/{{ $peminjaman->id }}">
        @csrf
        @method('PUT')
        <table>
            <tr>
                <td>Kode Peminjaman:</td>
                <td>
                    <input type="text" name="kode_peminjaman" value="{{ old('kode_peminjaman', $peminjaman->kode_peminjaman) }}" maxlength="10" required>
                    @error('kode_peminjaman')
                        <span style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
            </tr>
            <tr>
                <td>Nama Peminjam:</td>
                <td>
                    <input type="text" name="nama_peminjam" value="{{ old('nama_peminjam', $peminjaman->nama_peminjam) }}" maxlength="100" required>
                    @error('nama_peminjam')
                        <span style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
            </tr>
            <tr>
                <td>Nomor Identitas:</td>
                <td>
                    <input type="text" name="nomor_identitas" value="{{ old('nomor_identitas', $peminjaman->nomor_identitas) }}" maxlength="30" required>
                    @error('nomor_identitas')
                        <span style="color: red;">{{ $message }}</span>
                    @enderror
                </td>
            </tr>
            <tr>
                <td>Jumlah Alat:</td>
                <td>
                    <input type="number" name="jumlah_alat" value="{{ old('jumlah_alat', $peminjaman->jumlah_alat) }}" min="1" max="10" required>
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
                            <option value="{{ $a->id }}" {{ old('alat_id', $peminjaman->alat_id) == $a->id ? 'selected' : '' }}>
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
                <td>
                    <button type="submit">Update</button>
                    <a href="/peminjaman"><button type="button">Batal</button></a>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
