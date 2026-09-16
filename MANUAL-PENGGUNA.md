# Manual Pengguna Asset Tagging

**PT Borneo Prima**  
**Sistem Inventaris Aset Berbasis QR**

Dokumen ini menjelaskan penggunaan aplikasi untuk mencatat, menemukan,
memindahkan, memantau, dan memberi label QR pada aset perusahaan.

---

## Daftar Isi

1. [Memulai Penggunaan](#1-memulai-penggunaan)
2. [Dashboard](#2-dashboard)
3. [Mengelola Data Aset](#3-mengelola-data-aset)
4. [Mencetak Label QR](#4-mencetak-label-qr)
5. [Memindai QR Aset](#5-memindai-qr-aset)
6. [Mencatat Perpindahan Aset](#6-mencatat-perpindahan-aset)
7. [Data Referensi](#7-data-referensi)
8. [Prosedur Kerja Ringkas](#8-prosedur-kerja-ringkas)
9. [Pemecahan Masalah](#9-pemecahan-masalah)

---

## 1. Memulai Penggunaan

1. Buka aplikasi melalui peramban yang disediakan perusahaan.
2. Masukkan alamat email dan kata sandi Anda, lalu pilih **Masuk ke Dashboard**.
3. Centang **Ingat saya** hanya pada perangkat pribadi atau perangkat kerja yang aman.
4. Untuk keluar, buka menu akun di kanan atas lalu pilih **Sign out**.
5. Untuk mengganti kata sandi atau memperbarui profil, buka menu akun lalu pilih **Profile**.

> **Keamanan:** jangan membagikan kata sandi. Jika tidak dapat masuk, gunakan
> fasilitas pemulihan kata sandi bila tersedia atau hubungi pengelola sistem.

---

## 2. Dashboard

Dashboard adalah halaman ringkasan kondisi inventaris. Gunakan halaman ini untuk
memantau aset dan membuka pekerjaan yang paling sering dilakukan.

| Bagian | Kegunaan |
|---|---|
| Kartu statistik | Menampilkan jumlah total aset serta jumlah aset menurut status. |
| Diagram status aset | Memperlihatkan komposisi aset yang siaga, dipakai, diperbaiki, rusak, atau hilang. |
| Aset per kategori | Membandingkan jumlah aset pada setiap kategori. |
| Tren aset masuk | Memantau pencatatan aset dalam beberapa bulan terakhir. |
| Aksi cepat | Jalan pintas ke penambahan aset, pemindaian QR, dan daftar aset. |
| Perlu perhatian | Menampilkan aset berstatus Perbaikan, Rusak, atau Hilang. |
| Aktivitas terakhir | Menampilkan pemindaian dan perpindahan aset terbaru. |

---

## 3. Mengelola Data Aset

### 3.1 Melihat dan mencari aset

1. Buka menu **Assets** untuk melihat daftar inventaris.
2. Gunakan kolom **Search** untuk mencari ID aset, nama, merek, kategori,
   lokasi, departemen, pemegang, atau status.
3. Gunakan tombol **Filter** untuk menyaring berdasarkan status, kategori,
   lokasi, atau departemen.
4. Pilih ikon **mata** atau ID aset untuk membuka halaman **Detail Aset**.

Halaman detail menampilkan ID aset, merek, kategori, tipe atau seri, nomor
serial, status, lokasi, departemen, pemegang, foto, label QR, dan riwayat aset.

### 3.2 Menambah aset

1. Pilih **Tambah Aset** dari dashboard atau daftar aset.
2. Isi informasi utama: kategori, merek, tipe atau seri, nomor serial, dan status.
3. ID aset dibuat otomatis oleh sistem. Jangan mengubahnya secara manual.
4. Isi informasi lokasi dan kepemilikan: nomor PR atau PO bila ada, lokasi,
   departemen, dan pemegang.
5. Tambahkan foto aset bila diperlukan untuk memudahkan identifikasi fisik.
6. Pilih **Create** atau **Simpan**.

> Sebelum menambah aset, pastikan data kategori, merek, lokasi, dan departemen
> yang diperlukan sudah tersedia. Pengaturan nomor urut juga harus tersedia agar
> sistem dapat membuat ID aset otomatis.

### 3.3 Mengubah atau menghapus aset

- Pilih ikon **pensil** atau tombol **Edit** untuk memperbarui informasi aset.
- Nama, nomor serial, nomor PR atau PO, status, dan foto dapat diperbarui dari
  form edit.
- **Lokasi, departemen, dan pemegang tidak diubah dari form edit.** Gunakan
  pencatatan perpindahan agar perubahan tersebut tersimpan dalam riwayat.
- Pilih ikon **sampah** untuk menghapus aset yang tercatat keliru atau tidak lagi
  diperlukan. Aset dengan riwayat sebaiknya tidak dihapus; perbarui statusnya
  sesuai kondisi fisik.

### 3.4 Arti status aset

| Status | Arti |
|---|---|
| Siaga | Tersedia dan siap digunakan. |
| Dipakai | Sedang digunakan. |
| Perbaikan | Sedang dalam proses perbaikan. |
| Rusak | Rusak dan tidak dapat digunakan. |
| Hilang | Tidak dapat ditemukan. |

---

## 4. Mencetak Label QR

Setiap aset memiliki label QR yang terhubung dengan ID asetnya.

### Satu aset

1. Buka **Detail Aset**.
2. Pilih **Cetak QR**.
3. Cetak label, potong bila diperlukan, lalu tempel pada bagian aset yang mudah
   terlihat dan tidak mengganggu penggunaan barang.

### Beberapa aset sekaligus

1. Di daftar **Assets**, centang aset yang akan dicetak.
2. Buka **Bulk actions** lalu pilih **Cetak QR Terpilih**.
3. Halaman cetak akan terbuka. Periksa jumlah dan ID label sebelum mencetak.

Jika label rusak atau hilang, cetak ulang label dari detail aset yang sama.

---

## 5. Memindai QR Aset

1. Buka menu **Scan QR Asset** atau pilih aksi cepat **Scan QR**.
2. Izinkan aplikasi menggunakan kamera ketika diminta.
3. Arahkan kamera ke label QR hingga kode terbaca.
4. Jika aset ditemukan, sistem menampilkan notifikasi dan membuka **Detail Aset**.
5. Setiap pemindaian dicatat otomatis pada riwayat aset.

**Tips pemindaian:** gunakan pencahayaan yang cukup, bersihkan lensa kamera dan
label, serta jaga jarak sekitar 10 sampai 30 cm dari label.

---

## 6. Mencatat Perpindahan Aset

Gunakan pencatatan perpindahan setiap kali aset berpindah lokasi, departemen,
atau pemegang. Cara ini menjaga data utama dan riwayat tetap konsisten.

1. Buka **Detail Aset**.
2. Gulir ke bagian **Histories**.
3. Pilih **Catat Perpindahan Baru**.
4. Isi lokasi baru, departemen tujuan, dan pemegang baru sesuai kebutuhan.
5. Simpan pencatatan.

Setelah disimpan, data lokasi, departemen, dan pemegang pada aset diperbarui
otomatis. Riwayat menyimpan perubahan lama dan baru beserta waktu pencatatannya.

> Jangan mengubah lokasi, departemen, atau pemegang dengan cara lain. Perubahan
> yang dicatat melalui riwayat memastikan jejak perpindahan selalu dapat ditelusuri.

---

## 7. Data Referensi

Data referensi perlu tersedia sebelum aset didaftarkan. Kelola melalui menu yang
sesuai apabila menu tersebut tersedia untuk Anda.

| Data | Contoh | Kegunaan |
|---|---|---|
| Merek | Dell, Lenovo, HP | Pilihan merek pada aset. |
| Kategori | Laptop, Monitor, Printer | Pengelompokan jenis aset. |
| Lokasi | Gudang, kantor, ruang kerja | Penanda keberadaan fisik aset. |
| Departemen | IT, Keuangan, Operasional | Penanda unit yang menggunakan aset. |
| Pengaturan nomor urut | Prefix dan format nomor | Membuat ID aset otomatis per departemen. |

Pengaturan nomor urut menentukan pola ID aset, misalnya `IT-2026-0003`.
Jika ID aset tidak dapat dibuat saat menyimpan aset baru, minta pengelola sistem
memeriksa pengaturan nomor urut untuk departemen terkait.

---

## 8. Prosedur Kerja Ringkas

### Aset baru diterima

1. Pastikan kategori, merek, lokasi, dan departemen telah tersedia.
2. Tambahkan aset dan lengkapi data fisiknya.
3. Simpan data hingga ID aset dibuat oleh sistem.
4. Cetak dan tempel label QR.
5. Letakkan aset di lokasi yang sesuai dengan data tercatat.

### Aset berpindah ruangan atau pemegang

1. Buka detail aset.
2. Catat perpindahan baru pada bagian **Histories**.
3. Isi informasi tujuan dan pemegang baru.
4. Simpan dan pastikan perubahan muncul pada detail aset.

### Pemeriksaan fisik atau opname

1. Pindai label QR aset di lokasi pemeriksaan.
2. Cocokkan informasi pada layar dengan kondisi fisik aset.
3. Perbarui status jika kondisi aset berubah.
4. Catat perpindahan bila lokasi atau pemegang tidak sesuai.

### Label rusak

Cetak ulang label dari halaman detail aset, lalu tempelkan label baru pada
permukaan yang bersih, rata, dan mudah terlihat.

---

## 9. Pemecahan Masalah

| Kendala | Tindakan yang disarankan |
|---|---|
| ID aset tidak terbentuk saat menyimpan | Pastikan data departemen dan pengaturan nomor urut tersedia; hubungi pengelola sistem bila perlu. |
| Lokasi, departemen, atau pemegang tidak dapat diedit | Gunakan **Catat Perpindahan Baru** pada bagian Histories. |
| QR tidak terbaca | Tambah pencahayaan, bersihkan label atau lensa, dan atur jarak kamera. |
| Muncul pesan aset tidak terdaftar | Periksa kembali ID pada label dan pastikan aset sudah dicatat dalam sistem. |
| Kamera tidak tampil | Berikan izin kamera pada peramban dan pastikan perangkat memiliki kamera. |
| Data baru belum muncul | Muat ulang halaman, lalu periksa filter atau kata pencarian yang sedang aktif. |
| Lupa kata sandi | Gunakan pemulihan kata sandi bila tersedia atau hubungi pengelola sistem. |

---

*Dokumen versi 1.1. Perbarui dokumen ini apabila alur kerja atau fitur aplikasi berubah.*
