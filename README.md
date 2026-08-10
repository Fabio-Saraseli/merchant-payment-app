# Merchant Payment Application

A small payment-processing application consisting of a vanilla PHP MVC API and a React TypeScript frontend.

You can select the storage mechanism when starting the project. At the moment the app supports and has been tested with PostgreSQL, MySQL and SQLite.

## Technology stack

- PHP 8.3
- Composer
- PDO
- PostgreSQL 17
- MySQL 8
- SQLite
- React
- TypeScript
- Vite
- Docker
- Docker Compose

## Requirements

To run the app, you only need:

- Git
- [Docker Desktop](https://docs.docker.com/get-started/get-docker/) with Docker Compose

PHP, Composer, Node.js, npm and the databases do not need to be installed locally.

## Clone the repository

```bash
git clone https://github.com/Fabio-Saraseli/merchant-payment-app.git
cd merchant-payment-app
```

## Run the application

Before starting the project choose which database you want to use.

The base `docker-compose.yml` contains the application itself and the second compose file provides the selected database configuration.

### PostgreSQL

```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.postgres.yml \
  up --build
```

### MySQL

```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.mysql.yml \
  up --build
```

### SQLite

```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.sqlite.yml \
  up --build
```

After the services start, open:

- Frontend: [http://localhost:5173](http://localhost:5173)
- Backend API: [http://localhost:8080](http://localhost:8080)

The backend health endpoint returns:

```json
{
  "status": "ok",
  "service": "merchant-payment-api"
}
```

## Run in the background

Just add `-d` to whichever database configuration you selected.

For example with PostgreSQL:

```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.postgres.yml \
  up --build -d
```

For MySQL:

```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.mysql.yml \
  up --build -d
```

For SQLite:

```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.sqlite.yml \
  up --build -d
```

## Check the services

Use the same compose files that you used when starting the app.

For example for PostgreSQL:

```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.postgres.yml \
  ps
```

For MySQL:

```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.mysql.yml \
  ps
```

For SQLite:

```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.sqlite.yml \
  ps
```

## Application logs

For example with PostgreSQL:

```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.postgres.yml \
  logs -f
```

same idea for MySQL or SQLite, just use the corresponding compose file.

## Stop the app

Use the same database config that was used to start it.

PostgreSQL:

```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.postgres.yml \
  down --remove-orphans
```

MySQL:

```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.mysql.yml \
  down --remove-orphans
```

SQLite:

```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.sqlite.yml \
  down --remove-orphans
```

If you also want to delete the stored data for that database, add `-v`.

For example:

```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.postgres.yml \
  down -v --remove-orphans
```

Be careful with `-v` because it deletes the Docker volume and therefore the stored database data.

## Email notifications

The assignment mentions that the email delivery mechanism is left open, without specifying exactly how email should be used.

For this implementation I added a notification after a payment has been processed.

When a payment is successful it generates a success email.

A declined payment generates a failed payment email.

The notification is addressed to the email of the currently authenticated merchant.

For development I use Mailpit as the SMTP server. Mailpit captures the outgoing emails locally, so no real emails are sent to external addresses.

The email flow is:

```text
PaymentService
    |
PaymentNotificationService
    |
EmailSenderInterface
    |
SmtpEmailSender
    |
Mailpit
```

This also keeps the payment logic separate from the email delivery mechanism.

### Viewing emails

Mailpit starts together with the app and its inbox can be opened at:

[http://localhost:8025](http://localhost:8025)

After making a payment from the frontend, the generted email should appear in the Mailpit inbox.

The email includes:

- payment amount
- description
- transaction ID
- failure reason when the payment is declined
