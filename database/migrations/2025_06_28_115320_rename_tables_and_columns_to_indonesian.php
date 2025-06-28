<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ubah nama tabel atk_requests menjadi permintaan_atk
        Schema::rename('atk_requests', 'permintaan_atk');
        
        // 2. Ubah nama tabel request_items menjadi detail_permintaan_atk
        Schema::rename('request_items', 'detail_permintaan_atk');
        
        // 3. Ubah nama tabel items menjadi barang
        Schema::rename('items', 'barang');
        
        // 4. Ubah nama tabel categories menjadi kategori
        Schema::rename('categories', 'kategori');
        
        // 5. Ubah nama tabel purchase_requests menjadi pengajuan_pembelian
        Schema::rename('purchase_requests', 'pengajuan_pembelian');
        
        // 6. Ubah nama tabel reports menjadi laporan
        Schema::rename('reports', 'laporan');
        
        // 7. Ubah nama tabel roles menjadi peran
        Schema::rename('roles', 'peran');
        
        // 8. Ubah nama tabel permissions menjadi izin
        Schema::rename('permissions', 'izin');
        
        // 9. Ubah nama tabel role_has_permissions menjadi peran_memiliki_izin
        Schema::rename('role_has_permissions', 'peran_memiliki_izin');
        
        // 10. Ubah nama tabel model_has_roles menjadi model_memiliki_peran
        Schema::rename('model_has_roles', 'model_memiliki_peran');
        
        // 11. Ubah nama tabel model_has_permissions menjadi model_memiliki_izin
        Schema::rename('model_has_permissions', 'model_memiliki_izin');
        
        // 12. Ubah nama tabel users menjadi pengguna
        Schema::rename('users', 'pengguna');
        
        // 13. Ubah nama tabel password_reset_tokens menjadi token_reset_password
        Schema::rename('password_reset_tokens', 'token_reset_password');
        
        // 14. Ubah nama tabel failed_jobs menjadi pekerjaan_gagal
        Schema::rename('failed_jobs', 'pekerjaan_gagal');
        
        // 15. Ubah nama tabel personal_access_tokens menjadi token_akses_pribadi
        Schema::rename('personal_access_tokens', 'token_akses_pribadi');
        
        // 16. Ubah nama tabel migrations menjadi migrasi
        Schema::rename('migrations', 'migrasi');
        
        // Sekarang ubah nama kolom di tabel permintaan_atk (sebelumnya atk_requests)
        Schema::table('permintaan_atk', function (Blueprint $table) {
            $table->renameColumn('user_id', 'id_pengguna');
            $table->renameColumn('notes', 'catatan');
        });
        
        // Ubah nama kolom di tabel detail_permintaan_atk (sebelumnya request_items)
        Schema::table('detail_permintaan_atk', function (Blueprint $table) {
            $table->renameColumn('atk_request_id', 'id_permintaan_atk');
            $table->renameColumn('item_id', 'id_barang');
            $table->renameColumn('quantity', 'jumlah');
        });
        
        // Ubah nama kolom di tabel barang (sebelumnya items) - dengan pengecekan kolom
        if (Schema::hasColumn('barang', 'name')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->renameColumn('name', 'nama');
            });
        }
        
        if (Schema::hasColumn('barang', 'description')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->renameColumn('description', 'deskripsi');
            });
        }
        
        if (Schema::hasColumn('barang', 'stock')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->renameColumn('stock', 'stok');
            });
        }
        
        if (Schema::hasColumn('barang', 'category_id')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->renameColumn('category_id', 'id_kategori');
            });
        }
        
        if (Schema::hasColumn('barang', 'reorder_point')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->renameColumn('reorder_point', 'titik_pesan_ulang');
            });
        }
        
        // Ubah nama kolom di tabel kategori (sebelumnya categories)
        if (Schema::hasColumn('kategori', 'name')) {
            Schema::table('kategori', function (Blueprint $table) {
                $table->renameColumn('name', 'nama');
            });
        }
        
        if (Schema::hasColumn('kategori', 'description')) {
            Schema::table('kategori', function (Blueprint $table) {
                $table->renameColumn('description', 'deskripsi');
            });
        }
        
        // Ubah nama kolom di tabel pengajuan_pembelian (sebelumnya purchase_requests)
        if (Schema::hasColumn('pengajuan_pembelian', 'notes')) {
            Schema::table('pengajuan_pembelian', function (Blueprint $table) {
                $table->renameColumn('notes', 'catatan');
            });
        }
        
        // Ubah nama kolom di tabel laporan (sebelumnya reports)
        if (Schema::hasColumn('laporan', 'title')) {
            Schema::table('laporan', function (Blueprint $table) {
                $table->renameColumn('title', 'judul');
            });
        }
        
        if (Schema::hasColumn('laporan', 'content')) {
            Schema::table('laporan', function (Blueprint $table) {
                $table->renameColumn('content', 'isi');
            });
        }
        
        if (Schema::hasColumn('laporan', 'type')) {
            Schema::table('laporan', function (Blueprint $table) {
                $table->renameColumn('type', 'jenis');
            });
        }
        
        // Ubah nama kolom di tabel peran (sebelumnya roles)
        if (Schema::hasColumn('peran', 'name')) {
            Schema::table('peran', function (Blueprint $table) {
                $table->renameColumn('name', 'nama');
            });
        }
        
        if (Schema::hasColumn('peran', 'guard_name')) {
            Schema::table('peran', function (Blueprint $table) {
                $table->renameColumn('guard_name', 'nama_penjaga');
            });
        }
        
        // Ubah nama kolom di tabel izin (sebelumnya permissions)
        if (Schema::hasColumn('izin', 'name')) {
            Schema::table('izin', function (Blueprint $table) {
                $table->renameColumn('name', 'nama');
            });
        }
        
        if (Schema::hasColumn('izin', 'guard_name')) {
            Schema::table('izin', function (Blueprint $table) {
                $table->renameColumn('guard_name', 'nama_penjaga');
            });
        }
        
        // Ubah nama kolom di tabel pengguna (sebelumnya users)
        if (Schema::hasColumn('pengguna', 'name')) {
            Schema::table('pengguna', function (Blueprint $table) {
                $table->renameColumn('name', 'nama');
            });
        }
        
        if (Schema::hasColumn('pengguna', 'password')) {
            Schema::table('pengguna', function (Blueprint $table) {
                $table->renameColumn('password', 'kata_sandi');
            });
        }
        
        if (Schema::hasColumn('pengguna', 'remember_token')) {
            Schema::table('pengguna', function (Blueprint $table) {
                $table->renameColumn('remember_token', 'token_ingat');
            });
        }
        
        // Ubah nama kolom di tabel pekerjaan_gagal (sebelumnya failed_jobs)
        if (Schema::hasColumn('pekerjaan_gagal', 'queue')) {
            Schema::table('pekerjaan_gagal', function (Blueprint $table) {
                $table->renameColumn('queue', 'antrian');
            });
        }
        
        if (Schema::hasColumn('pekerjaan_gagal', 'payload')) {
            Schema::table('pekerjaan_gagal', function (Blueprint $table) {
                $table->renameColumn('payload', 'muatan');
            });
        }
        
        if (Schema::hasColumn('pekerjaan_gagal', 'exception')) {
            Schema::table('pekerjaan_gagal', function (Blueprint $table) {
                $table->renameColumn('exception', 'pengecualian');
            });
        }
        
        // Ubah nama kolom di tabel token_akses_pribadi (sebelumnya personal_access_tokens)
        if (Schema::hasColumn('token_akses_pribadi', 'name')) {
            Schema::table('token_akses_pribadi', function (Blueprint $table) {
                $table->renameColumn('name', 'nama');
            });
        }
        
        if (Schema::hasColumn('token_akses_pribadi', 'abilities')) {
            Schema::table('token_akses_pribadi', function (Blueprint $table) {
                $table->renameColumn('abilities', 'kemampuan');
            });
        }
        
        if (Schema::hasColumn('token_akses_pribadi', 'last_used_at')) {
            Schema::table('token_akses_pribadi', function (Blueprint $table) {
                $table->renameColumn('last_used_at', 'terakhir_digunakan_pada');
            });
        }
        
        if (Schema::hasColumn('token_akses_pribadi', 'expires_at')) {
            Schema::table('token_akses_pribadi', function (Blueprint $table) {
                $table->renameColumn('expires_at', 'berakhir_pada');
            });
        }
        
        // Ubah nama kolom di tabel migrasi (sebelumnya migrations)
        if (Schema::hasColumn('migrasi', 'migration')) {
            Schema::table('migrasi', function (Blueprint $table) {
                $table->renameColumn('migration', 'migrasi');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback semua perubahan dalam urutan terbalik
        // 1. Kembalikan nama kolom di tabel migrasi
        if (Schema::hasColumn('migrasi', 'migrasi')) {
            Schema::table('migrasi', function (Blueprint $table) {
                $table->renameColumn('migrasi', 'migration');
            });
        }
        
        // 2. Kembalikan nama kolom di tabel token_akses_pribadi
        if (Schema::hasColumn('token_akses_pribadi', 'nama')) {
            Schema::table('token_akses_pribadi', function (Blueprint $table) {
                $table->renameColumn('nama', 'name');
            });
        }
        
        if (Schema::hasColumn('token_akses_pribadi', 'kemampuan')) {
            Schema::table('token_akses_pribadi', function (Blueprint $table) {
                $table->renameColumn('kemampuan', 'abilities');
            });
        }
        
        if (Schema::hasColumn('token_akses_pribadi', 'terakhir_digunakan_pada')) {
            Schema::table('token_akses_pribadi', function (Blueprint $table) {
                $table->renameColumn('terakhir_digunakan_pada', 'last_used_at');
            });
        }
        
        if (Schema::hasColumn('token_akses_pribadi', 'berakhir_pada')) {
            Schema::table('token_akses_pribadi', function (Blueprint $table) {
                $table->renameColumn('berakhir_pada', 'expires_at');
            });
        }
        
        // 3. Kembalikan nama kolom di tabel pekerjaan_gagal
        if (Schema::hasColumn('pekerjaan_gagal', 'antrian')) {
            Schema::table('pekerjaan_gagal', function (Blueprint $table) {
                $table->renameColumn('antrian', 'queue');
            });
        }
        
        if (Schema::hasColumn('pekerjaan_gagal', 'muatan')) {
            Schema::table('pekerjaan_gagal', function (Blueprint $table) {
                $table->renameColumn('muatan', 'payload');
            });
        }
        
        if (Schema::hasColumn('pekerjaan_gagal', 'pengecualian')) {
            Schema::table('pekerjaan_gagal', function (Blueprint $table) {
                $table->renameColumn('pengecualian', 'exception');
            });
        }
        
        // 4. Kembalikan nama kolom di tabel pengguna
        if (Schema::hasColumn('pengguna', 'nama')) {
            Schema::table('pengguna', function (Blueprint $table) {
                $table->renameColumn('nama', 'name');
            });
        }
        
        if (Schema::hasColumn('pengguna', 'kata_sandi')) {
            Schema::table('pengguna', function (Blueprint $table) {
                $table->renameColumn('kata_sandi', 'password');
            });
        }
        
        if (Schema::hasColumn('pengguna', 'token_ingat')) {
            Schema::table('pengguna', function (Blueprint $table) {
                $table->renameColumn('token_ingat', 'remember_token');
            });
        }
        
        // 5. Kembalikan nama kolom di tabel izin
        if (Schema::hasColumn('izin', 'nama')) {
            Schema::table('izin', function (Blueprint $table) {
                $table->renameColumn('nama', 'name');
            });
        }
        
        if (Schema::hasColumn('izin', 'nama_penjaga')) {
            Schema::table('izin', function (Blueprint $table) {
                $table->renameColumn('nama_penjaga', 'guard_name');
            });
        }
        
        // 6. Kembalikan nama kolom di tabel peran
        if (Schema::hasColumn('peran', 'nama')) {
            Schema::table('peran', function (Blueprint $table) {
                $table->renameColumn('nama', 'name');
            });
        }
        
        if (Schema::hasColumn('peran', 'nama_penjaga')) {
            Schema::table('peran', function (Blueprint $table) {
                $table->renameColumn('nama_penjaga', 'guard_name');
            });
        }
        
        // 7. Kembalikan nama kolom di tabel laporan
        if (Schema::hasColumn('laporan', 'judul')) {
            Schema::table('laporan', function (Blueprint $table) {
                $table->renameColumn('judul', 'title');
            });
        }
        
        if (Schema::hasColumn('laporan', 'isi')) {
            Schema::table('laporan', function (Blueprint $table) {
                $table->renameColumn('isi', 'content');
            });
        }
        
        if (Schema::hasColumn('laporan', 'jenis')) {
            Schema::table('laporan', function (Blueprint $table) {
                $table->renameColumn('jenis', 'type');
            });
        }
        
        // 8. Kembalikan nama kolom di tabel pengajuan_pembelian
        if (Schema::hasColumn('pengajuan_pembelian', 'catatan')) {
            Schema::table('pengajuan_pembelian', function (Blueprint $table) {
                $table->renameColumn('catatan', 'notes');
            });
        }
        
        // 9. Kembalikan nama kolom di tabel kategori
        if (Schema::hasColumn('kategori', 'nama')) {
            Schema::table('kategori', function (Blueprint $table) {
                $table->renameColumn('nama', 'name');
            });
        }
        
        if (Schema::hasColumn('kategori', 'deskripsi')) {
            Schema::table('kategori', function (Blueprint $table) {
                $table->renameColumn('deskripsi', 'description');
            });
        }
        
        // 10. Kembalikan nama kolom di tabel barang
        if (Schema::hasColumn('barang', 'nama')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->renameColumn('nama', 'name');
            });
        }
        
        if (Schema::hasColumn('barang', 'deskripsi')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->renameColumn('deskripsi', 'description');
            });
        }
        
        if (Schema::hasColumn('barang', 'stok')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->renameColumn('stok', 'stock');
            });
        }
        
        if (Schema::hasColumn('barang', 'id_kategori')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->renameColumn('id_kategori', 'category_id');
            });
        }
        
        if (Schema::hasColumn('barang', 'titik_pesan_ulang')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->renameColumn('titik_pesan_ulang', 'reorder_point');
            });
        }
        
        // 11. Kembalikan nama kolom di tabel detail_permintaan_atk
        if (Schema::hasColumn('detail_permintaan_atk', 'id_permintaan_atk')) {
            Schema::table('detail_permintaan_atk', function (Blueprint $table) {
                $table->renameColumn('id_permintaan_atk', 'atk_request_id');
            });
        }
        
        if (Schema::hasColumn('detail_permintaan_atk', 'id_barang')) {
            Schema::table('detail_permintaan_atk', function (Blueprint $table) {
                $table->renameColumn('id_barang', 'item_id');
            });
        }
        
        if (Schema::hasColumn('detail_permintaan_atk', 'jumlah')) {
            Schema::table('detail_permintaan_atk', function (Blueprint $table) {
                $table->renameColumn('jumlah', 'quantity');
            });
        }
        
        // 12. Kembalikan nama kolom di tabel permintaan_atk
        if (Schema::hasColumn('permintaan_atk', 'id_pengguna')) {
            Schema::table('permintaan_atk', function (Blueprint $table) {
                $table->renameColumn('id_pengguna', 'user_id');
            });
        }
        
        if (Schema::hasColumn('permintaan_atk', 'catatan')) {
            Schema::table('permintaan_atk', function (Blueprint $table) {
                $table->renameColumn('catatan', 'notes');
            });
        }
        
        // 13. Kembalikan nama tabel
        Schema::rename('migrasi', 'migrations');
        Schema::rename('token_akses_pribadi', 'personal_access_tokens');
        Schema::rename('pekerjaan_gagal', 'failed_jobs');
        Schema::rename('token_reset_password', 'password_reset_tokens');
        Schema::rename('pengguna', 'users');
        Schema::rename('model_memiliki_izin', 'model_has_permissions');
        Schema::rename('model_memiliki_peran', 'model_has_roles');
        Schema::rename('peran_memiliki_izin', 'role_has_permissions');
        Schema::rename('izin', 'permissions');
        Schema::rename('peran', 'roles');
        Schema::rename('laporan', 'reports');
        Schema::rename('pengajuan_pembelian', 'purchase_requests');
        Schema::rename('kategori', 'categories');
        Schema::rename('barang', 'items');
        Schema::rename('detail_permintaan_atk', 'request_items');
        Schema::rename('permintaan_atk', 'atk_requests');
    }
};
