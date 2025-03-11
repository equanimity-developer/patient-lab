# Patient Lab Project Installation Guide

---

## 1. Repository Cloning
Clone the repository
```bash
git clone https://github.com/equanimity-developer/patient-lab.git
cd patient-lab
```

## 2. Laravel Sail Installation

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

## 3. Environment Configuration

Create `.env` file
```bash
cp .env.example .env
```

Build the application
```bash
./vendor/bin/sail build
```

Run the application
```bash
./vendor/bin/sail up -d
```

Generate application key
```bash
./vendor/bin/sail artisan key:generate
```

Run migrations
```bash
./vendor/bin/sail artisan migrate
```

Generate JWT token
```bash
./vendor/bin/sail artisan jwt:secret
```

## 4. Frontend Dependencies Installation

Install dependencies
```bash
./vendor/bin/sail npm install
```

Run frontend
```bash
./vendor/bin/sail npm run dev
```

## 5. Using the Application
The application is now ready and available at the default address http://localhost/

Since the database is still empty, you can run the main functionality - import laboratory results.
```bash
./vendor/bin/sail artisan import:csv tests/Files/correct.csv
```

Now you can log in to a patient account, for example with the following data:
```
URL: http://localhost/login
Login: PiotrKowalski
Password: 1983-04-12
```

The application language can be changed through environment variables:
```
VITE_APP_LOCALE - frontend
APP_LOCALE - backend
```
