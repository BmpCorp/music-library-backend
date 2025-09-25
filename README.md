### Deploy using Docker

- copy `.docker/.env.example` to `.docker/.env` and configure it
    - every variable is required
- copy `backend/.env.example` to `backend/.env` and configure it
    - leave `APP_KEY` empty
    - these vars should be the same (docker's .env -> backend .env):
        - `MYSQL_HOST` = `DB_HOST`
        - `MYSQL_PORT` = `DB_PORT`
        - `MYSQL_DATABASE` = `DB_DATABASE`
        - `MYSQL_USER` = `DB_USERNAME`
        - `MYSQL_USER_PASSWORD` = `DB_PASSWORD`
        - `REDIS_PASSWORD` = `REDIS_PASSWORD`
        - `REDIS_PORT` = `REDIS_PORT`
        - `MEILISEARCH_PASSWORD` = `MEILISEARCH_KEY`
        - `APP_URL` var should be `scheme:// + domain:port` from docker's .env
- start docker containers
    - docker compose example: `cd .docker && docker compose -f docker-compose.dev.yml up -d`
- run `fresh.sh` on first install
