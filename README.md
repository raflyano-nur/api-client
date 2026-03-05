# API Client

API Client adalah aplikasi sederhana berbasis **PHP** untuk menguji request API seperti **GET, POST, PUT, PATCH, dan DELETE**.
Project ini juga bisa menyimpan collection request dalam format **JSON**.

## Fitur

* Test API Endpoint
* Export Collection JSON
* Proxy API request
* Simpan konfigurasi website
* Interface sederhana

## Struktur Folder

```
api-client/
│
├── index.php
├── proxy.php
├── router.php
├── save_json.php
├── settingWeb.php
│
├── json/
│   └── data-settingWebsite.json
│
└── README.md
```

## Persyaratan

* PHP **>= 7.4**
* Web server:

  * Apache (Laragon / XAMPP / dll)
  * atau PHP Built-in Server

## Cara Menjalankan

### 1. Clone Repository

```
git clone https://github.com/username/api-client.git
cd api-client
```

### 2. Jalankan dengan PHP Built-in Server

```
php -S localhost:8000 router.php
```

Lalu buka di browser:

```
http://localhost:8000
```

### 3. Jalankan dengan Apache (Laragon / XAMPP)

1. Pindahkan folder ke:

```
htdocs/
```

atau

```
www/
```

2. Akses melalui browser:

```
http://localhost/api-client
```

## Konfigurasi

Pengaturan aplikasi dapat diubah di file:

```
json/data-settingWebsite.json
```

Contoh:

```json
{
    "app_name": "Raflyano API Client",
    "app_version": "1.0.0",
    "app_description": "Ini API client untuk yang laptopnya kentang",
    "author": "Raflyano",
    "base_url": "http:\/\/localhost:8000",
    "default_timeout": 5000,
    "theme": "dark"
}
```

## Export Collection

Collection API akan disimpan dalam format:

```
appname.collection_name.collection.json
```

Contoh:

```
APIClient.User.collection.json
```

## License

MIT License
