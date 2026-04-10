## Cara Install
### Prasyarat
- PHP >= 8.2 dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`
- Composer >= 2
- MySQL 8+ atau MariaDB 10.6+

### Instalasi
- Clone: `https://github.com/mirulumam/sales-order-api.git`
- Buka terminal, arahkan active directory ke folder repo di atas
- Instal dependencies composer: `composer install`
- Salin atau _rename_ file `.env.example` ke `.env`
- Sesuaikan konfigurasi databse di file `.env`:
  - DB_CONNECTION=\<db_type>
  - DB_HOST=\<db_host>
  - DB_PORT=\<db_connection_port>
  - DB_DATABASE=\<db_name>
  - DB_USERNAME=\<db_user>
  - DB_PASSWORD=\<db_password>
- Generate _app key_: `php artisan key:generate`
- Jalankan migrasi: `php artisan migrate`
- Jalankan seeder (untuk mengisi data ke db): `php artisan db:seed`

## Running Project
### Local
- Untuk menjalankan project di local, gunakan command: `php artisan serve`

### Deploy ke Server
- Siapkan server dengan root directory atur ke directory `public`
- Jalankan langkah-langkah `Instalasi` di atas

## Dokumentasi API
- [Sales API.postman_collection.json](https://github.com/mirulumam/sales-order-api/blob/master/Sales%20API.postman_collection.json)

## Desain Aplikasi
- API memungkinkan _user_ menggunakan aplikasi dengan metode _multi-session_ yang artinya _user_ dapat _login_ dibeberapa perangkat sekaligus dengan _session_ yang berbeda dan punya _session expiration_-nya masing-masing.
- Backend tidak menyimpan _session token_ (JWT). Setiap _request_ yang diterima akan divalidasi dengan _verification signature_ menggunakan `JWT_SECRET` yang ada pada `.env`
- Struktur utama API:
  - Model, dibagi menjadi 3:
    - Data model - /app/Models
    - Request - /app/Http/Requests
    - Response - /app/Http/Resources
  - Controller - /app/Http/Controllers/Api
  - Services, _bussiness logic_ - /app/Services