# Dokumentasi Batasan Akses Berdasarkan Role

## Overview
Sistem manajemen ATK Pengadilan Agama menggunakan sistem role-based access control (RBAC) dengan 3 role utama:

1. **admin** - Administrator sistem
2. **staff** - Staff/petugas 
3. **pimpinan** - Pimpinan/pemimpin

## Detail Batasan Akses per Role

### 1. ADMIN
**Akses Penuh ke Semua Fitur**

#### Resource yang Dapat Diakses:
- ✅ **Barang (ItemResource)**
  - Melihat daftar barang
  - Menambah barang baru
  - Mengedit barang
  - Menghapus barang
  - Mengelola kategori barang

- ✅ **Permintaan Barang (RequestResource)**
  - Melihat semua permintaan
  - Membuat permintaan untuk staff lain
  - Mengedit semua permintaan
  - Menghapus permintaan

- ✅ **Pengajuan Pembelian (PurchaseRequestResource)**
  - Melihat semua pengajuan
  - Membuat pengajuan otomatis
  - Mengedit pengajuan
  - Menghapus pengajuan
  - Menerima barang (tombol "Terima Barang")

- ✅ **Laporan (ReportResource)**
  - Melihat semua laporan
  - Membuat laporan
  - Mengedit semua laporan
  - Menghapus laporan

- ✅ **Manajemen Pengguna (UserResource)**
  - Melihat daftar pengguna
  - Menambah pengguna baru
  - Mengedit pengguna
  - Menghapus pengguna
  - Mengatur role pengguna

- ✅ **Manajemen Role (RoleResource)**
  - Melihat daftar role
  - Menambah role baru
  - Mengedit role
  - Menghapus role

- ✅ **Manajemen Permission (PermissionResource)**
  - Melihat daftar permission
  - Menambah permission baru
  - Mengedit permission
  - Menghapus permission

### 2. STAFF
**Akses Terbatas untuk Operasional**

#### Resource yang Dapat Diakses:
- ✅ **Barang (ItemResource)**
  - Melihat daftar barang (read-only)
  - Tidak dapat menambah/edit/hapus barang

- ✅ **Permintaan Barang (RequestResource)**
  - Melihat semua permintaan
  - Membuat permintaan baru (untuk diri sendiri)
  - Mengedit permintaan sendiri saja
  - Tidak dapat menghapus permintaan

- ✅ **Laporan (ReportResource)**
  - Melihat semua laporan
  - Membuat laporan baru
  - Mengedit laporan sendiri saja
  - Tidak dapat menghapus laporan

#### Resource yang Tidak Dapat Diakses:
- ❌ **Pengajuan Pembelian** - Hanya dapat melihat jika ada akses khusus
- ❌ **Manajemen Pengguna** - Tidak ada akses
- ❌ **Manajemen Role** - Tidak ada akses
- ❌ **Manajemen Permission** - Tidak ada akses

### 3. PIMPINAN
**Akses untuk Persetujuan dan Monitoring**

#### Resource yang Dapat Diakses:
- ✅ **Barang (ItemResource)**
  - Melihat daftar barang (read-only)
  - Tidak dapat menambah/edit/hapus barang

- ✅ **Permintaan Barang (RequestResource)**
  - Melihat semua permintaan
  - Menyetujui/menolak permintaan (tombol "Setujui/Tolak")
  - Tidak dapat membuat permintaan baru
  - Tidak dapat mengedit permintaan
  - Dapat menghapus permintaan

- ✅ **Pengajuan Pembelian (PurchaseRequestResource)**
  - Melihat semua pengajuan (read-only)
  - Tidak dapat membuat/edit/hapus pengajuan
  - Tidak dapat menerima barang

- ✅ **Laporan (ReportResource)**
  - Melihat semua laporan
  - Tidak dapat membuat laporan baru
  - Tidak dapat mengedit laporan
  - Tidak dapat menghapus laporan

#### Resource yang Tidak Dapat Diakses:
- ❌ **Manajemen Pengguna** - Tidak ada akses
- ❌ **Manajemen Role** - Tidak ada akses
- ❌ **Manajemen Permission** - Tidak ada akses

## Implementasi Teknis

### 1. Method Batasan Akses
Setiap resource menggunakan method berikut untuk mengontrol akses:

```php
// Akses umum ke resource
public static function canAccess(): bool

// Kemampuan membuat record baru
public static function canCreate(): bool

// Kemampuan mengedit record
public static function canEdit($record): bool

// Kemampuan menghapus record
public static function canDelete($record): bool

// Kemampuan melihat record
public static function canView($record): bool

// Registrasi di navigasi
public static function shouldRegisterNavigation(): bool
```

### 2. Batasan pada Actions
Setiap action (tombol) juga dibatasi berdasarkan role:

```php
Tables\Actions\EditAction::make()
    ->visible(fn () => auth()->user()->hasRole('admin'))
```

### 3. Batasan pada Form Fields
Field tertentu dapat disembunyikan atau di-disable berdasarkan role:

```php
Forms\Components\Select::make('user_id')
    ->visible(fn () => auth()->user()->hasRole('admin'))
```

## Keamanan

### 1. Server-side Validation
Semua batasan akses diterapkan di level server untuk memastikan keamanan.

### 2. Role-based Middleware
Sistem menggunakan Spatie Permission package untuk manajemen role yang aman.

### 3. Audit Trail
Semua aktivitas penting dicatat untuk audit trail.

## Penggunaan

### Login sebagai Admin:
```
Email: fatma@pengadilan-agama.go.id
Password: password
```

### Login sebagai Pimpinan:
```
Email: darodji@pengadilan-agama.go.id
Password: password
```

### Login sebagai Staff:
```
Email: surmiani@pengadilan-agama.go.id
Password: password
```

## Catatan Penting

1. **Staff hanya dapat mengedit permintaan yang dibuatnya sendiri**
2. **Pimpinan dapat menyetujui/menolak permintaan staff**
3. **Admin memiliki akses penuh ke semua fitur**
4. **Pengajuan pembelian dibuat otomatis oleh sistem**
5. **Hanya admin yang dapat menerima barang dari pengajuan pembelian**

## Troubleshooting

Jika terjadi masalah akses:
1. Pastikan user sudah login
2. Periksa role yang diberikan ke user
3. Clear cache permission: `php artisan permission:cache-reset`
4. Periksa log untuk error detail 