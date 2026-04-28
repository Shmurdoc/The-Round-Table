Quick Docker development and production notes

Build images:

  docker compose build

Run locally (nginx on 8080):

  docker compose up -d --build

Notes:
- Set environment variables via .env or Docker compose override for production
- Use `docker exec -it <app> php artisan migrate --force` to run migrations inside container
