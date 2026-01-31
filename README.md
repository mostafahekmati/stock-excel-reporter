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


### Cache invalidation after imports (revision key)

To keep the API fast, the stock change endpoints are cached for a short time (2 minutes).
However, after importing a new Excel file, cached responses must be invalidated.

We solve this by using a per-company cache revision key:

- Revision key: `stock:rev:{company_id}`
- After every successful import, the job increments and stores this revision key forever.
- API cache keys include the revision number, e.g.:
    - `stock_change:v{rev}:{companyId}:{from}:{to}`
    - `stock_period_changes:v{rev}:{companyId}:{latestDate}`

So when a new import happens, the revision increases and the API automatically generates new cache keys,
making old cached responses effectively obsolete (no manual cache clearing needed).


## Postman Collection

A ready-to-use Postman collection is included in this repository.

Location: 

   postman/Stock-Excel-Reporter.postman_collection.json
   postman/Stock-Excel-Reporter.postman_environment.json


### How to use

1. Open Postman
2. Import both files (Collection and Environment)
3. Select the environment: **Stock Excel Reporter Local**
4. For the `stock-imports` request, choose a real `.xlsx` file from your system
5. Run the requests in order
