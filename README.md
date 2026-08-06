# Merchant Payment Application

A small payment-processing application consisting of a vanilla PHP MVC API, a React TypeScript frontend, and a PostgreSQL database.

## Technology stack

- PHP 8.3
- Composer
- PostgreSQL 17
- React
- TypeScript
- Vite
- Docker
- Docker Compose

## Requirements

To run the app, you only need:

- Git
- Docker Desktop with Docker Compose

PHP, Composer, Node.js, npm, and PostgreSQL do not need to be installed locally.

## Clone the repository

```bash
git clone https://github.com/Fabio-Saraseli/merchant-payment-app.git
cd merchant-payment-app
```

## Run the application

Build and start the frontend, backend, and database all at once:

```bash
docker compose up --build
```

After the services start, open:

- Frontend: http://localhost:5173
- Backend API: http://localhost:8080

The backend currently returns:

```json
{
  "status": "ok",
  "service": "merchant-payment-api"
}
```

## Run in the background

Start all services without keeping the logs open:

```bash
docker compose up --build -d
```

See the status of the services:

```bash
docker compose ps
```

See the application logs:

```bash
docker compose logs -f
```

## Stop the app

If you want to stop and remove the containers:

```bash
docker compose down
```

To stop the application and delete all stored database data:

```bash
docker compose down -v
```
