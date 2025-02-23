# Instrukcja instalacji projektu Patient Lab

---

## 1. Klonowanie Repozytorium
Skopiowanie repozytorium
```bash
git clone https://github.com/equanimity-developer/patient-lab.git
cd patient-lab
```

## 2. Instalacja Laravel Sail

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

## 3. Konfiguracja środowiska

Utworzenie `.env`
```bash
cp .env.example .env
```

Zbudowanie aplikacji
```bash
./vendor/bin/sail build
```

Uruchomienie aplikacji
```bash
./vendor/bin/sail up -d
```

Wygenerowanie klucza aplikacji
```bash
./vendor/bin/sail artisan key:generate
```

Uruchomienie migracji
```bash
./vendor/bin/sail artisan migrate
```

Wygenerowanie tokenu dla JWT
```bash
./vendor/bin/sail artisan jwt:secret
```

## 4. Instalacja zależności Frontend

Instalacja zależności
```bash
./vendor/bin/sail npm install
```

Uruchomienie frontu
```bash
./vendor/bin/sail npm run dev
```

## 5. Użytkowanie aplikacji
Aplikacja jest już gotowa i dostępna pod domyślnym adresem http://localhost/

Ponieważ baza danych jest jeszcze pusta, można uruchomić główną funkcjonalność - import wyników laboratoryjnych.
```bash
./vendor/bin/sail artisan import:csv tests/Files/correct.csv
```

Teraz można zalogować się na konto pacjenta, np. poprzez dane:
```
URL: http://localhost/login
Login: PiotrKowalski
Hasło: 1983-04-12
```

Język aplikacji można zmienić poprzez zmienne w env:
```
VITE_APP_LOCALE - front
APP_LOCALE - backend
```
