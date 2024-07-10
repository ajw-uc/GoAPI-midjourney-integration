## GoAPI API Key

Buat API key GoApi.ai dengan mengakses halaman https://dashboard.goapi.ai/key (masuk dengan menggunakan akun GitHub)

## Instalasi

Copy file .env.example lalu paste dan rename menjadi .env
Isi nama aplikasi dan masukkan GoApi API Key pada atribut GOAPI_KEY:
```
APP_NAME=UC Midjourney
GOAPI_KEY=abcdefghijklmnopqrstuvwxyz1234567890
```

## Penggunaan

Jalankan aplikasi pada server PHP, buka halaman index.php untuk memulai penggunaan
atau jalankan perintah berikut di terminal/command prompt
```
php -S localhost:80
```
Aplikasi dapat diakses dengan menggunakan browser dengan mengakses URL http://localhost

## Struktur folder
- *api:* daftar kode API yang tersedia
- *assets:* gambar, script css, dan script js
- *data:* hasil generate gambar berdasarkan prompt yang diberikan
- *logs:* log pemanggilan API GoAPI Midjourney
- *view:* daftar tampilan website (front-end)