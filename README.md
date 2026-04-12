## Cara Install
### Prasyarat
- PHP >= 8.4 dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`
- Composer >= 2
- MySQL 8+ atau MariaDB 10.6+

### Instalasi
- Clone: `https://github.com/mirulumam/sales-order-api.git`
- Buka terminal, arahkan active directory ke folder repo di atas
- Instal dependencies composer: `composer update`
- Salin atau _rename_ file `.env.example` ke `.env`
- Sesuaikan konfigurasi databse di file `.env`:
  - DB_CONNECTION=\<db_type>
  - DB_HOST=\<db_host>
  - DB_PORT=\<db_connection_port>
  - DB_DATABASE=\<db_name>
  - DB_USERNAME=\<db_user>
  - DB_PASSWORD=\<db_password>
- Generate _app key_: `php artisan key:generate`
- Generate JWT Secret: `php artisan jwt:secret`
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

#### `POST /api/auth/login`
Login dan dapatkan Bearer token.

**Request Body:**
```json
{
  "username": "sales01",
  "password": "password"
}
```

**Response `200`:**
```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "token": "1|abc123...",
    "token_type": "Bearer",
    "user": { "id": 2, "username": "sales01", "role": "user" }
  }
}
```

**Response `401`:**
```json
{ "success": false, "message": "Username atau password salah." }
```

---

#### `POST /api/auth/logout`
Logout.

**Response `200`:**
```json
{ "success": true, "message": "Logout berhasil." }
```

#### `GET /api/auth/profile`
Menampilkan data user yang sedang login.

**Response `200`:**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "username": "sales01",
        "role": "user",
        "created_at": "2026-04-12T04:36:21+00:00"
    }
}
```

**Response `401`:**
```json
{
    "success": false,
    "message": "Unauthenticated. Silakan login terlebih dahulu."
}
```

---

### Products

#### `GET /api/products`
Daftar produk dengan pagination.

**Query Params:**
| Param    | Type   | Default | Keterangan              |
|----------|--------|---------|-------------------------|
| per_page | int    | 15      | Jumlah item per halaman |
| search   | string | –       | Filter nama produk      |

**Response `200`:**
```json
{
  "success": true,
  "data": [
    { "id": 1, "name": "Laptop ASUS VivoBook 14", "price": 7500000, "stock": 20, "created_at": "..." }
  ],
  "meta": { "current_page": 1, "last_page": 1, "per_page": 15, "total": 8 }
}
```

#### `GET /api/products/{id}`
Detail satu produk.

**Response `200`:**
```json
{
    "success": true,
    "data": {
        "id": 5,
        "name": "Headset Sony WH-1000XM5",
        "price": 4500000,
        "stock": 30,
        "created_at": "2026-04-12T04:36:21+00:00"
    }
}
```

---

### Customers

#### `GET /api/customers`
Daftar customer dengan pagination.

**Query Params:**
| Param    | Type   | Default | Keterangan                       |
|----------|--------|---------|----------------------------------|
| per_page | int    | 15      | Jumlah item per halaman          |
| search   | string | –       | Filter nama atau nomor telepon   |

**Response `200`:**
```json
{
    "success": true,
    "data": [
        {
            "id": 3,
            "name": "Budi Santoso",
            "phone": "08113456789",
            "address": "Jl. Kebon Jeruk No. 7, Jakarta Barat",
            "created_at": "2026-04-12T04:36:21+00:00"
        },
        {
            "id": 2,
            "name": "CV Teknologi Nusantara",
            "phone": "02287654321",
            "address": "Jl. Gatot Subroto No. 12, Jakarta Selatan",
            "created_at": "2026-04-12T04:36:21+00:00"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 10,
        "total": 5
    }
}
```

#### `GET /api/customers/{id}`
Detail satu customer.

**Response `200`:**
```json
{
    "success": true,
    "data": {
        "id": 5,
        "name": "PT Solusi Digital",
        "phone": "02155566677",
        "address": "Gedung Menara BCA Lt. 30, Jakarta Pusat",
        "created_at": "2026-04-12T04:36:21+00:00"
    }
}
```

---

### Orders

#### `GET /api/orders`
Daftar order dengan pagination.

**Query Params:**
| Param    | Type      | Default | Keterangan              |
|----------|-----------|---------|-------------------------|
| per_page | int       | 15      | Jumlah item per halaman |
| status   | 1 / 2 / 3 | –       | Filter by status        |

1. Draft
2. Submitted
3. Cancelled

**Response `200`:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "order_no": "ORD-20260412-0001",
            "status": "Draft",
            "total_amount": 9450000,
            "customer": {
                "id": 5,
                "name": "PT Solusi Digital",
                "phone": "02155566677",
                "address": "Gedung Menara BCA Lt. 30, Jakarta Pusat",
                "created_at": "2026-04-12T04:36:21+00:00"
            },
            "created_by": {
                "id": 2,
                "username": "sales01"
            },
            "created_at": "2026-04-12T05:47:58+00:00",
            "updated_at": "2026-04-12T05:47:58+00:00"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 10,
        "total": 1
    }
}
```

#### `GET /api/orders/{id}`
Detail order beserta seluruh item dan info produk.

**Response `200`:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "order_no": "ORD-20260412-0001",
        "status": "Draft",
        "total_amount": 9450000,
        "customer": {
            "id": 5,
            "name": "PT Solusi Digital",
            "phone": "02155566677",
            "address": "Gedung Menara BCA Lt. 30, Jakarta Pusat",
            "created_at": "2026-04-12T04:36:21+00:00"
        },
        "created_by": {
            "id": 2,
            "username": "sales01"
        },
        "items": [
            {
                "id": 1,
                "product": {
                    "id": 5,
                    "name": "Headset Sony WH-1000XM5",
                    "price": 4500000,
                    "stock": 30,
                    "created_at": "2026-04-12T04:36:21+00:00"
                },
                "qty": 2,
                "price": 4500000,
                "subtotal": 9000000
            },
            {
                "id": 2,
                "product": {
                    "id": 3,
                    "name": "Keyboard Mechanical RK61",
                    "price": 450000,
                    "stock": 50,
                    "created_at": "2026-04-12T04:36:21+00:00"
                },
                "qty": 1,
                "price": 450000,
                "subtotal": 450000
            }
        ],
        "created_at": "2026-04-12T05:47:58+00:00",
        "updated_at": "2026-04-12T05:47:58+00:00"
    }
}
```

---

#### `POST /api/orders`
Membuat order baru dengan status **Draft**.

> Stok belum dikurangi pada tahap ini.

**Request Body:**
```json
{
  "customer_id": 1,
  "items": [
    { "product_id": 1, "qty": 2 },
    { "product_id": 2, "qty": 5 }
  ]
}
```

**Response `201`:**
```json
{
  "success": true,
  "message": "Order draft berhasil dibuat.",
  "data": {
    "id": 1,
    "order_no": "ORD-20240115-0001",
    "status": "Draft",
    "total_amount": 16250000,
    "customer": { "id": 1, "name": "PT Maju Bersama", ... },
    "items": [
      { "id": 1, "product": { ... }, "qty": 2, "price": 7500000, "subtotal": 15000000 },
      { "id": 2, "product": { ... }, "qty": 5, "price":  250000, "subtotal":  1250000 }
    ],
    ...
  }
}
```

**Response `422` (validasi gagal):**
```json
{
  "success": false,
  "message": "Data yang dikirim tidak valid.",
  "errors": {
    "customer_id": ["Customer tidak ditemukan."],
    "items.0.qty": ["Qty setiap item harus lebih besar dari 0."]
  }
}
```

---

#### `PATCH /api/orders/{id}/submit`
Submit order dari status **Draft** → **Submitted**.

> Stok dikurangi di sini. Proses dibungkus dalam **database transaction** dengan `SELECT ... FOR UPDATE` untuk mencegah race condition.

**Response `200`:**
```json
{ "success": true, "message": "Order berhasil dibuat.", "data": { ... } }
```

**Response `422` (stok tidak cukup):**
```json
{ "success": false, "message": "Stok produk \"Laptop ASUS VivoBook 14\" tidak mencukupi" }
```

**Response `422` (status tidak valid):**
```json
{ "success": false, "message": "Hanya order berstatus Draft yang dapat dilanjutkan." }
```

---

#### `PATCH /api/orders/{id}/cancel`
Cancel order dari status **Submitted** → **Cancelled**.

> Stok dikembalikan. Juga dibungkus dalam database transaction.

**Response `200`:**
```json
{ "success": true, "message": "Order berhasil dibatalkan. Stok produk telah dikembalikan.", "data": { ... } }
```

**Response `422`:**
```json
{ "success": false, "message": "Hanya order berstatus Submitted yang dapat dibatalkan." }
```

---

### General Error Responses

| HTTP Code | Kondisi                              |
|-----------|--------------------------------------|
| 401       | Token tidak ada / expired            |
| 404       | Resource tidak ditemukan             |
| 422       | Validasi gagal / business rule error |
| 500       | Internal server error                |

---

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
- Submit dan cancel order menggunakan `DB::transaction()` dengan `SELECT ... FOR UPDATE` (`lockForUpdate()`). Ini memastikan bahwa dalam kondisi concurrent requests (misalnya dua sales submit order untuk produk yang sama secara bersamaan), database-level lock akan mencegah race condition dan over-selling.