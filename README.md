# Stock Excel Reporter

A Laravel application fully Dockerized using **Docker Compose** (PHP-FPM + Nginx + MySQL).

No PHP, Composer, or MySQL installation is required on your system.
Everything runs inside Docker.

---

## Requirements

Before running the project, make sure you have the following installed:

* **Docker** and **Docker Compose**

    * macOS / Windows: Install **Docker Desktop**
    * Linux: Install Docker Engine + Docker Compose plugin
* **make**

    * macOS: already installed
    * Linux: usually installed (`sudo apt install make` if missing)
    * Windows: recommended to use **WSL2 (Ubuntu)**

> ⚠️ Windows users:
> Run this project inside **WSL2 (Ubuntu)** and execute all commands there.

---

## Quick Start (After Clone)

### 1) Clone the repository

```bash
git clone <REPO_URL>
cd stock-excel-reporter
```

### 2) Create environment file

```bash
cp .env.example .env
```

Make sure the database configuration is set to MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

(Optional but recommended)

```env
APP_URL=http://localhost:8080
```

### 3) Start Docker containers

```bash
make up
```

The application will be available at:

```
http://localhost:8080
```

### 4) First-time initialization (run once)

```bash
make init
```

This command will:

* Install Composer dependencies
* Generate the Laravel application key
* Run database migrations
* Enable the `art` shortcut inside the container

---

## Common Commands

```bash
make ps
make logs
make down
make restart
```

---

## Artisan Commands

Run Artisan from host:

```bash
make artisan c="migrate"
make artisan c="cache:clear"
make artisan c="route:list"
```

Enter application container shell:

```bash
make shell
```

Inside the container:

```bash
php artisan migrate
art migrate
art cache:clear
```

---

## Database Access (Local Tools)

* Host: `127.0.0.1`
* Port: `3310`
* Database: `laravel`
* Username: `laravel`
* Password: `laravel`

---

## Reset Database (DANGER)

```bash
make fresh
```

This will delete all data and recreate the database schema.

---

## Troubleshooting

### Ports already in use

Default ports:

* Application: `8080`
* MySQL: `3310`

Change them in `docker-compose.yml` and restart:

```bash
make restart
```

---

## Windows without make

If `make` is not available, you can run the project using Docker Compose directly:

```bash
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app sh -lc 'ln -sf /var/www/art /usr/local/bin/art'
```

---

## Summary

After cloning the repository, only two commands are required:

```bash
make up
make init
```

The application will be fully running with Docker 🚀
