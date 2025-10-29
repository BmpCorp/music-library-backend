## Deployment

### Step 1: Configure Environment

Clone default Docker .env file:

```
cp .docker/.env.example .docker/.env
```

Set passwords and change domain and ports if needed. Every variable is required.

Clone default Laravel .env file:

```
cp backend/.env.example backend/.env
```

Configure it, leaving `APP_KEY` empty. These vars should be the same:

| backend/.env           | .docker/.env        |
|------------------------|---------------------|
| `MYSQL_HOST`           | `DB_HOST`           |
| `MYSQL_PORT`           | `DB_PORT`           |
| `MYSQL_DATABASE`       | `DB_DATABASE`       |
| `MYSQL_USER`           | `DB_USERNAME`       |
| `MYSQL_USER_PASSWORD`  | `DB_PASSWORD`       |
| `REDIS_PASSWORD`       | `REDIS_PASSWORD`    |
| `REDIS_PORT`           | `REDIS_PORT`        |
| `MEILISEARCH_PASSWORD` | `MEILISEARCH_KEY`   |
| `RABBITMQ_PORT`        | `RABBITMQ_PORT`     |
| `RABBITMQ_USER`        | `RABBITMQ_USER`     |
| `RABBITMQ_PASSWORD`    | `RABBITMQ_PASSWORD` |

### Step 2 (Optional, for local environment): Configure hosts File

If your plan to use something other than localhost in your local environment, your need to configure hosts file.

Find your hosts file (`/etc/hosts` on Unix-based OS, `%SYSTEMROOT%\System32\drivers\etc\hosts` on Windows).
Edit it with admin/root privileges (on Windows, you might need to copy it to some folder you have access to, edit it and copy back).
Place desired domain at the end of the file, on new line. For example:

```
127.0.0.1 backend.music-library.local
```

After that, you can change these backend/.env vars (with corresponding values from .docker/.env):

| variable                    | value                                               | example                                    |
|-----------------------------|-----------------------------------------------------|--------------------------------------------|
| `APP_URL`                   | `scheme:// + NGINX_DOMAIN:NGINX_HTTP_PORT`          | `http://backend.music-library.local:3080`  |
| `SESSION_DOMAIN`            | `NGINX_DOMAIN`                                      | `backend.music-library.local`              |
| `MEILISEARCH_EXTERNAL_HOST` | `scheme:// + NGINX_DOMAIN:MEILISEARCH_PORT`         | `http://backend.music-library.local:7700`  |
| `RABBITMQ_MANAGEMENT_URL`   | `scheme:// + NGINX_DOMAIN:RABBITMQ_MANAGEMENT_PORT` | `http://backend.music-library.local:15672` |

### Step 3: Build and Run Docker Containers

Install Docker and Docker Compose if not installed already (<a href="https://docs.docker.com/engine/install/">docs</a>).

Depending on your environment, start either development or production containers:

```
cd .docker && docker compose -f docker-compose.dev.yml up -d
```
```
cd .docker && docker compose -f docker-compose.prod.yml up -d
```

### Step 4: Install the Project and Seed The Database

On clean install, run `fresh.sh` script to automatically install dependencies, generate Laravel project key,
migrate and seed database.

Running in development environment, you might want to seed database with additional mock data
including countries, artists, albums, users and users' favorites. There are two ways to do this.
Both of them require entering `php` container's shell. Use either `docker exec` command or `php.sh` script.

You can seed database via traditional faker library generation. This is the default seeding option.
To perform it, run this command inside `php` container:

```
php artisan db:mock
```

You can also seed the database using AI services - this provides more plausible mock data
(real band names with real albums), but can be unreliable and time-consuming. This option is provided
mostly for presentation purposes and requires OpenRouter account to work.
To seed the database this way, run inside `php` container:

```
php artisan db:mock --ai
```

### Step 5: Log Into Admin Panel

Open `APP_URL` in browser. You should be redirected to the admin panel, bypassing default Laravel "Hello World" page.
Use default admin credentials (defined in backend/.env file) to log into the admin panel.
You can change admin's Email and password afterward if necessary.
