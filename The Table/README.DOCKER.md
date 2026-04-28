Quick Docker development and production notes

Build images:

  docker compose -f "The Table/docker-compose.yml" build --pull

Run locally (nginx on 8080):

  docker compose -f "The Table/docker-compose.yml" up -d --build

Quick run checklist:

  1. Copy `The Table/.env.docker.example` -> `The Table/.env` and set `APP_KEY`.
  2. Build and run containers (above).
  3. Generate app key and run migrations:
     docker compose -f "The Table/docker-compose.yml" exec app php artisan key:generate
     docker compose -f "The Table/docker-compose.yml" exec app php artisan migrate --force

Notes:
- Set environment variables via `.env` or Docker compose override for production
- Use `docker compose -f "The Table/docker-compose.yml" exec app php artisan migrate --force` to run migrations inside container
