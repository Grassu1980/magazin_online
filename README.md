# Magazin Online - Laravel E-commerce

Un magazin online complet construit cu Laravel, Blade și MySQL, compatibil cu orice hosting shared (Apache + PHP + MySQL).

## Funcționalități

### Frontend (Client)
- ✅ Home cu bannere, categorii, produse recomandate
- ✅ Listă produse cu filtrare, sortare, căutare
- ✅ Pagină produs cu imagini multiple, descriere, preț, stoc, SKU
- ✅ Coș de cumpărături (add/remove/update)
- ✅ Checkout cu formular date client
- ✅ Autentificare / Înregistrare (email + parolă)
- ✅ Cont utilizator cu istoric comenzi
- ✅ Wishlist
- ✅ Recenzii produse (opțional)

### Backend (Admin Panel)
- ✅ Dashboard cu statistici
- ✅ CRUD complet pentru produse
- ✅ CRUD complet pentru categorii
- ✅ Gestionare comenzi
- ✅ Gestionare utilizatori cu roluri

### API REST
- ✅ Endpoint-uri pentru produse, categorii, comenzi
- ✅ Autentificare cu Sanctum

## Tehnologii
- Laravel 10
- MySQL
- Blade Templates
- TailwindCSS
- Font Awesome

## Instalare Local (Laragon/Windows)

### 1. Clonează proiectul
```bash
cd C:\laragon\www
composer create-project laravel/laravel magazin_online
cd magazin_online
# Copiază fișierele din proiectul tău peste acesta
```

### 2. Instalează dependențele
```bash
composer install
npm install
```

### 3. Configurează baza de date
- Copiază `.env.example` în `.env`
- Editează `.env`:
```env
APP_NAME="Magazin Online"
APP_KEY=base64:... (generează cu php artisan key:generate)
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=magazin_online
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generează cheie aplicație
```bash
php artisan key:generate
```

### 5. Rulează migrările și seeders
```bash
php artisan migrate
php artisan db:seed
```

### 6. Compilează CSS
```bash
npm run build
# sau pentru development:
npm run dev
```

### 7. Pornește serverul
```bash
php artisan serve
# sau în Laragon: click "Start All"
```

Accesează: `http://magazin_online.test` sau `http://localhost:8000`

## Rulare cu Docker
Dacă nu ai PHP instalat local, poți porni aplicația cu Docker.

1. Instalează Docker Desktop.
2. Rulează din folderul proiectului:
```bash
docker compose up --build
```
3. Deschide:
```text
http://localhost:8000
```

Pentru baza de date și seed-uri:
```bash
docker compose exec app php artisan migrate --seed
```

## Instalare pe Hosting Shared

### 1. Upload fișiere
- Upload toate fișierele în directorul `public_html` sau folderul specificat de hosting.

### 2. Configurează .env
- Copiază `.env.example` în `.env`
- Editează datele bazei de date conform hosting-ului tău.

### 3. Instalează dependențe
- Dacă hosting-ul permite SSH: `composer install --no-dev`
- Dacă nu, upload folderul `vendor/` (dacă ai instalat local).

### 4. Rulează migrări
```bash
php artisan migrate --seed
```

### 5. Compilează CSS
```bash
npm run prod
# sau upload fișierul compilat din local
```

### 6. Setări suplimentare
- Asigură-te că `storage/` și `bootstrap/cache/` au permisiuni de scriere.
- Dacă folosești cPanel, setează document root la `public/`.

## Utilizatori de Test
- **Admin**: admin@magazinonline.ro / password
- **User**: user@example.com / password

## Structura Proiect
```
app/
├── Http/Controllers/
│   ├── Api/
│   ├── Backend/
│   └── Frontend/
├── Models/
├── Services/
└── Repositories/

database/
├── migrations/
└── seeders/

public/
├── css/
├── js/
└── uploads/

resources/
├── views/
│   ├── backend/
│   ├── components/
│   ├── frontend/
│   └── layouts/
└── css/

routes/
├── api.php
├── web.php
└── admin.php
```

## Comenzi Utile
```bash
# Curăță cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generează cheie nouă
php artisan key:generate

# Creează simbolic link pentru storage
php artisan storage:link
```

## Suport
Pentru întrebări sau probleme, verifică documentația Laravel sau deschide un issue.