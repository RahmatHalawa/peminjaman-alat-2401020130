<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PeminjamanController extends Controller
{
    /**
     * Menampilkan halaman utama dengan daftar peminjaman
     */
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $filter_alat = $request->input('filter_alat');

            // Query dasar dengan JOIN
            $query = "SELECT peminjaman_alat.*, alat.nama_alat 
                      FROM peminjaman_alat 
                      INNER JOIN alat ON peminjaman_alat.alat_id = alat.id 
                      WHERE 1=1";
            
            $params = [];

            // Filter pencarian (kode_peminjaman, nama_peminjam, nomor_identitas)
            if ($search) {
                $query .= " AND (peminjaman_alat.kode_peminjaman LIKE ? 
                            OR peminjaman_alat.nama_peminjam LIKE ? 
                            OR peminjaman_alat.nomor_identitas LIKE ?)";
                $searchParam = '%' . $search . '%';
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
            }

            // Filter berdasarkan alat
            if ($filter_alat) {
                $query .= " AND peminjaman_alat.alat_id = ?";
                $params[] = $filter_alat;
            }

            $query .= " ORDER BY peminjaman_alat.id DESC";

            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $peminjaman = $stmt->fetchAll(\PDO::FETCH_OBJ);

            // Ambil data alat untuk dropdown
            $stmt_alat = $pdo->prepare("SELECT * FROM alat ORDER BY nama_alat ASC");
            $stmt_alat->execute();
            $alat = $stmt_alat->fetchAll(\PDO::FETCH_OBJ);

            return view('peminjaman.index', compact('peminjaman', 'alat', 'search', 'filter_alat'));

        } catch (Exception $e) {
            report($e);
            return back()->with('error', 'Terjadi kesalahan saat mengambil data');
        }
    }

    /**
     * Menyimpan data peminjaman baru
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'kode_peminjaman' => 'required|size:10|regex:/^[A-Za-z0-9]+$/',
            'nama_peminjam' => 'required|min:3|max:100',
            'nomor_identitas' => 'required|min:5|max:30',
            'jumlah_alat' => 'required|integer|min:1|max:10',
            'alat_id' => 'required|exists:alat,id'
        ], [
            'kode_peminjaman.required' => 'Kode peminjaman wajib diisi',
            'kode_peminjaman.size' => 'Kode peminjaman harus tepat 10 karakter',
            'kode_peminjaman.regex' => 'Kode peminjaman hanya boleh alfanumerik',
            'nama_peminjam.required' => 'Nama peminjam wajib diisi',
            'nama_peminjam.min' => 'Nama peminjam minimal 3 karakter',
            'nama_peminjam.max' => 'Nama peminjam maksimal 100 karakter',
            'nomor_identitas.required' => 'Nomor identitas wajib diisi',
            'nomor_identitas.min' => 'Nomor identitas minimal 5 karakter',
            'nomor_identitas.max' => 'Nomor identitas maksimal 30 karakter',
            'jumlah_alat.required' => 'Jumlah alat wajib diisi',
            'jumlah_alat.integer' => 'Jumlah alat harus berupa angka',
            'jumlah_alat.min' => 'Jumlah alat minimal 1',
            'jumlah_alat.max' => 'Jumlah alat maksimal 10',
            'alat_id.required' => 'Alat wajib dipilih',
            'alat_id.exists' => 'Alat tidak valid'
        ]);

        try {
            // Sanitasi input
            $kode_peminjaman = strip_tags(trim($request->kode_peminjaman));
            $nama_peminjam = strip_tags(trim($request->nama_peminjam));
            $nomor_identitas = strip_tags(trim($request->nomor_identitas));
            $jumlah_alat = (int) $request->jumlah_alat;
            $alat_id = (int) $request->alat_id;

            $pdo = DB::connection()->getPdo();

            // Cek duplikat kode_peminjaman
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM peminjaman_alat WHERE kode_peminjaman = ?");
            $stmt->execute([$kode_peminjaman]);
            $result = $stmt->fetch(\PDO::FETCH_OBJ);
            
            if ($result->count > 0) {
                return back()->withInput()->withErrors(['kode_peminjaman' => 'Kode peminjaman sudah digunakan']);
            }

            // Cek duplikat nomor_identitas
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM peminjaman_alat WHERE nomor_identitas = ?");
            $stmt->execute([$nomor_identitas]);
            $result = $stmt->fetch(\PDO::FETCH_OBJ);
            
            if ($result->count > 0) {
                return back()->withInput()->withErrors(['nomor_identitas' => 'Nomor identitas sudah digunakan']);
            }

            // Insert data
            $query = "INSERT INTO peminjaman_alat (kode_peminjaman, nama_peminjam, nomor_identitas, jumlah_alat, alat_id, created_at, updated_at) 
                      VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$kode_peminjaman, $nama_peminjam, $nomor_identitas, $jumlah_alat, $alat_id]);

            return redirect('/peminjaman')->with('success', 'Data peminjaman berhasil ditambahkan');

        } catch (Exception $e) {
            report($e);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data');
        }
    }

    /**
     * Menampilkan form edit
     */
    public function edit($id)
    {
        try {
            $pdo = DB::connection()->getPdo();

            // Ambil data peminjaman
            $stmt = $pdo->prepare("SELECT * FROM peminjaman_alat WHERE id = ?");
            $stmt->execute([$id]);
            $peminjaman = $stmt->fetch(\PDO::FETCH_OBJ);

            if (!$peminjaman) {
                return redirect('/peminjaman')->with('error', 'Data tidak ditemukan');
            }

            // Ambil data alat untuk dropdown
            $stmt_alat = $pdo->prepare("SELECT * FROM alat ORDER BY nama_alat ASC");
            $stmt_alat->execute();
            $alat = $stmt_alat->fetchAll(\PDO::FETCH_OBJ);

            return view('peminjaman.edit', compact('peminjaman', 'alat'));

        } catch (Exception $e) {
            report($e);
            return redirect('/peminjaman')->with('error', 'Terjadi kesalahan saat mengambil data');
        }
    }

    /**
     * Mengupdate data peminjaman
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'kode_peminjaman' => 'required|size:10|regex:/^[A-Za-z0-9]+$/',
            'nama_peminjam' => 'required|min:3|max:100',
            'nomor_identitas' => 'required|min:5|max:30',
            'jumlah_alat' => 'required|integer|min:1|max:10',
            'alat_id' => 'required|exists:alat,id'
        ], [
            'kode_peminjaman.required' => 'Kode peminjaman wajib diisi',
            'kode_peminjaman.size' => 'Kode peminjaman harus tepat 10 karakter',
            'kode_peminjaman.regex' => 'Kode peminjaman hanya boleh alfanumerik',
            'nama_peminjam.required' => 'Nama peminjam wajib diisi',
            'nama_peminjam.min' => 'Nama peminjam minimal 3 karakter',
            'nama_peminjam.max' => 'Nama peminjam maksimal 100 karakter',
            'nomor_identitas.required' => 'Nomor identitas wajib diisi',
            'nomor_identitas.min' => 'Nomor identitas minimal 5 karakter',
            'nomor_identitas.max' => 'Nomor identitas maksimal 30 karakter',
            'jumlah_alat.required' => 'Jumlah alat wajib diisi',
            'jumlah_alat.integer' => 'Jumlah alat harus berupa angka',
            'jumlah_alat.min' => 'Jumlah alat minimal 1',
            'jumlah_alat.max' => 'Jumlah alat maksimal 10',
            'alat_id.required' => 'Alat wajib dipilih',
            'alat_id.exists' => 'Alat tidak valid'
        ]);

        try {
            // Sanitasi input
            $kode_peminjaman = strip_tags(trim($request->kode_peminjaman));
            $nama_peminjam = strip_tags(trim($request->nama_peminjam));
            $nomor_identitas = strip_tags(trim($request->nomor_identitas));
            $jumlah_alat = (int) $request->jumlah_alat;
            $alat_id = (int) $request->alat_id;

            $pdo = DB::connection()->getPdo();

            // Cek duplikat kode_peminjaman (kecuali data sendiri)
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM peminjaman_alat WHERE kode_peminjaman = ? AND id != ?");
            $stmt->execute([$kode_peminjaman, $id]);
            $result = $stmt->fetch(\PDO::FETCH_OBJ);
            
            if ($result->count > 0) {
                return back()->withInput()->withErrors(['kode_peminjaman' => 'Kode peminjaman sudah digunakan']);
            }

            // Cek duplikat nomor_identitas (kecuali data sendiri)
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM peminjaman_alat WHERE nomor_identitas = ? AND id != ?");
            $stmt->execute([$nomor_identitas, $id]);
            $result = $stmt->fetch(\PDO::FETCH_OBJ);
            
            if ($result->count > 0) {
                return back()->withInput()->withErrors(['nomor_identitas' => 'Nomor identitas sudah digunakan']);
            }

            // Update data
            $query = "UPDATE peminjaman_alat 
                      SET kode_peminjaman = ?, nama_peminjam = ?, nomor_identitas = ?, jumlah_alat = ?, alat_id = ?, updated_at = NOW() 
                      WHERE id = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$kode_peminjaman, $nama_peminjam, $nomor_identitas, $jumlah_alat, $alat_id, $id]);

            return redirect('/peminjaman')->with('success', 'Data peminjaman berhasil diupdate');

        } catch (Exception $e) {
            report($e);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat mengupdate data');
        }
    }

    /**
     * Menghapus data peminjaman
     */
    public function destroy($id)
    {
        try {
            $pdo = DB::connection()->getPdo();

            // Cek apakah data ada
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM peminjaman_alat WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(\PDO::FETCH_OBJ);
            
            if ($result->count == 0) {
                return redirect('/peminjaman')->with('error', 'Data tidak ditemukan');
            }

            // Hapus data
            $stmt = $pdo->prepare("DELETE FROM peminjaman_alat WHERE id = ?");
            $stmt->execute([$id]);

            return redirect('/peminjaman')->with('success', 'Data peminjaman berhasil dihapus');

        } catch (Exception $e) {
            report($e);
            return redirect('/peminjaman')->with('error', 'Terjadi kesalahan saat menghapus data');
        }
    }
}
