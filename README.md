## GoAPI API Key

Buat API key GoApi.ai dengan mengakses halaman https://dashboard.goapi.ai/key (masuk dengan menggunakan akun GitHub)

## Pengaturan

Copy file .env.example lalu paste dan rename menjadi .env
Isi nama aplikasi pada atribut APP_NAME dan masukkan GoApi API Key pada atribut GOAPI_KEY:
```
APP_NAME=UC Midjourney
GOAPI_KEY=abcdefghijklmnopqrstuvwxyz1234567890
```

## Instalasi

Jalankan aplikasi pada server PHP, buka halaman index.php atau "/" untuk memulai penggunaan

### Instalasi dengan XAMPP, MAMP, dan sejenisnya

Tambahkan atribut BASE_URL pada file .env, dan isi valuenya dengan URL http://localhost/folder
```
BASE_URL=http://localhost/folder
```
Jalankan dengan mengakses URL localhost server

## Struktur folder
- *api:* daftar kode API yang tersedia
- *assets:* gambar, script css, dan script js
- *data:* hasil generate gambar berdasarkan prompt yang diberikan
- *logs:* log pemanggilan API GoAPI Midjourney
- *view:* daftar tampilan website (front-end)