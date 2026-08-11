# Merchant Payment Application

A small payment-processing application consisting of a vanilla PHP MVC API and a React TypeScript frontend.

The application allows authenticated merchants to:

- log in
- process card payments
- view their previous transactions
- filter transactions by date range
- receive local payment notification emails

You can select the storage mechanism when starting the project. At the moment the app supports and has been tested with PostgreSQL, MySQL and SQLite.

## Technology stack

### Backend

- PHP 8.3
- Composer
- PDO
- PHPUnit
- PHPMailer

### Frontend

- React
- TypeScript
- Vite
- Tailwind CSS
- React Router

### Infrastructure

- Docker
- Docker Compose
- PostgreSQL 17
- MySQL 8
- SQLite
- Mailpit

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

No additional dependency installation is required before starting the application.

## Run the application

Before starting the project choose which database you want to use.

The base `docker-compose.yml` contains the application services and the second compose file provides the selected database configuration.

### PostgreSQL

```bash
docker compose -f docker-compose.yml -f docker-compose.postgres.yml up --build
```

### MySQL

```bash
docker compose -f docker-compose.yml -f docker-compose.mysql.yml up --build
```

### SQLite

```bash
docker compose -f docker-compose.yml -f docker-compose.sqlite.yml up --build
```

On startup the backend automatically:

1. waits until the selected storage mechanism is available
2. runs pending database migrations
3. runs the database seeder
4. starts the PHP API

The frontend and Mailpit are started automatically as well.

After the services start, open:

- Frontend: [http://localhost:5173](http://localhost:5173)
- Backend API: [http://localhost:8080](http://localhost:8080)
- Mailpit: [http://localhost:8025](http://localhost:8025)

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

The same approach can be used with MySQL or SQLite by using the corresponding compose file.

To view only the backend logs:

```bash
docker compose \
  -f docker-compose.yml \
  -f docker-compose.postgres.yml \
  logs -f backend
```

## Stop the app

Use the same database config that was used to start it.

PostgreSQL:

```bash
docker compose -f docker-compose.yml -f docker-compose.postgres.yml down --remove-orphans
```

MySQL:

```bash
docker compose -f docker-compose.yml -f docker-compose.mysql.yml down --remove-orphans
```

SQLite:

```bash
docker compose -f docker-compose.yml -f docker-compose.sqlite.yml down --remove-orphans
```

If you also want to delete the stored data for that database, add `-v`.

For example:

```bash
docker compose -f docker-compose.yml -f docker-compose.postgres.yml down -v --remove-orphans
```

Be careful with `-v` because it deletes the Docker volume and therefore the stored database data.

# Demo merchants

The database seeder creates two merchants that can be used to test the application.

### Merchant 1

```text
Email: demo@merchant.test
Password: password123
```

### Merchant 2

```text
Email: second@merchant.test
Password: password123
```

The second merchant is useful for verifying merchant isolation.

Transactions created by one merchant are not visible to another merchant.

# Using the application

Open:

[http://localhost:5173](http://localhost:5173)

and log in using one of the demo merchant accounts.

After logging in, the merchant can access the payment form and transaction history.

## Making a payment

The payment form contains:

- card number
- expiry date
- CVV
- amount
- description

Card number, expiry and CVV are validated before the payment is sent to the payment provider.

The current fake provider accepts valid card information and returns a simulated payment result.

### Successful test payment

A valid test card can be used, for example:

```text
Card number: 4242424242424242
Expiry:      12/30
CVV:         123
```

Use any positive payment amount and a description.

### Declined payment

The following card is reserved for simulating a declined payment:

```text
4000000000000002
```

This makes it possible to test both successful and failed payment flows without communicating with a real payment provider.

## Card data

Raw card numbers and CVVs are not persisted in the database.

Only the last four digits of the card are stored with the transaction.

For example:

```text
•••• 4242
```

In a production payment system, sensitive card information would normally be tokenized and handled directly by a PCI-compliant payment service provider.

# Transaction history

Authenticated merchants can view their previous payment transactions from the Transaction History page.

Each transaction shows:

- date and time
- description
- last four card digits
- amount
- status

Transactions are isolated by merchant.

A merchant can only retrieve transactions belonging to their own account.

## Date filtering

Transactions can be filtered using:

- From
- To

Both values use the `YYYY-MM-DD` date format.

The `To` date is inclusive.

For example, selecting:

```text
From: 2026-08-01
To:   2026-08-10
```

returns transactions from the beginning of August 1 until the end of August 10.

The frontend then converts the timestamp into the local timezone of the user's browser.

This means the same transaction can be displayed correctly to users in different timezones without hardcoding a particular timezone into the application.

# Authentication

Authentication is handled using opaque Bearer tokens.

When a merchant logs in:

1. the merchant is found by email
2. the supplied password is checked using `password_verify`
3. a cryptographically random token is generated
4. the SHA-256 hash of that token is stored in the database
5. the plaintext token is returned to the client
6. the token expires after one hour

The plaintext token itself is not stored in the database.

Authenticated API requests use:

```http
Authorization: Bearer <token>
```

The frontend stores the token and its expiration time locally and sends the token with protected requests.

# How to interact with the API

The API is available at:

```text
http://localhost:8080
```

## Health check

```http
GET /
```

Example response:

```json
{
  "status": "ok",
  "service": "merchant-payment-api"
}
```

## Login

```http
POST /api/auth/login
```

Request:

```json
{
  "email": "demo@merchant.test",
  "password": "password123"
}
```

A successful login returns an authentication token, expiration time and merchant information.

Example:

```json
{
  "token": "<token>",
  "expires_at": "2026-08-10T19:30:00Z",
  "merchant": {
    "id": "<merchant-id>",
    "name": "Demo Merchant",
    "email": "demo@merchant.test"
  }
}
```

The token must be included in protected requests:

```http
Authorization: Bearer <token>
```

## Process payment

```http
POST /api/payments
```

Headers:

```http
Authorization: Bearer <token>
Content-Type: application/json
```

Example request:

```json
{
  "card_number": "4242424242424242",
  "expiry": "12/30",
  "cvv": "123",
  "amount": 25.50,
  "description": "Test payment"
}
```

The backend validates:

- card number
- Luhn checksum
- expiry date
- CVV
- payment amount

## Retrieve transactions

```http
GET /api/transactions
```

Authentication is required.

Optional query parameters:

```text
from=YYYY-MM-DD
to=YYYY-MM-DD
```

Example:

```http
GET /api/transactions?from=2026-08-01&to=2026-08-10
```

Only transactions belonging to the currently authenticated merchant are returned.

A new payment provider can be added by:

1. creating a class that implements `PaymentProviderInterface`
2. giving it a unique provider name
3. registering it in `config/payment_providers.php`
4. configuring a merchant to use that provider

Merchant-specific payment provider settings are stored separately as provider configuration.

# Email notifications

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

After making a payment from the frontend, the generated email should appear in the Mailpit inbox.

The email includes:

- payment amount
- description
- transaction ID
- failure reason when the payment is declined

Email delivery does not control the result of the payment.

If email delivery fails after a payment has already been processed, the payment and transaction remain valid and the notification failure is logged separately.

# Storage architecture

The application currently supports:

- PostgreSQL
- MySQL
- SQLite

The business logic does not directly depend on any of these database engines.
It has been tested with these storage mechanisms.

# Database migrations

Database migrations are stored under:

```text
backend/database/migrations/
```

The backend keeps track of executed migrations in a `migrations` table.
When the backend container starts it automatically runs:

```text
php bin/migrate.php
```

before starting the API.

The migrations create the required tables for:

- merchants
- API tokens
- transactions
- merchant payment provider configuration

The SQL used by the migrations is intentionally kept compatible with PostgreSQL, MySQL and SQLite where possible.

# Database seeding

Seed data is created automatically when the backend starts.
The seed process creates the demo merchants used to access the application.
The seeder can safely be executed when restarting the application without creating duplicate demo accounts.

# Automated tests

The backend uses PHPUnit for automated testing.
The tests cover the main application layers, including:

- card validation
- fake payment provider behavior
- authentication service
- payment service
- payment notifications
- merchant repositories
- API token repositories
- transaction repositories
- authentication controller
- payment controller
- transaction controller

Repository tests are integration tests and execute against real database engines.

The test setup allows the same PHPUnit suite to run against:

SQLite
PostgreSQL
MySQL

This is used to verify that storage behavior remains compatible between all three supported relational database engines.

## Run the full test matrix

From the project root:

```bash
docker compose -f docker-compose.test.yml up --build --abort-on-container-exit --exit-code-from backend-tests
```

The test container runs the PHPUnit suite sequentially against:

```text
SQLite
PostgreSQL
MySQL
```

A successful run ends with:

```text
ALL DATABASE TESTS PASSED
SQLite      OK
PostgreSQL  OK
MySQL       OK
```

Because the same suite is executed once for each database configuration, the full matrix executes the tests three times.

## Run tests manually with SQLite

First build the test image:

```bash
docker compose -f docker-compose.test.yml build backend-tests
```

Then run:

```bash
docker compose -f docker-compose.test.yml run --rm --no-deps -e TEST_DB_DSN="sqlite::memory:" backend-tests ./vendor/bin/phpunit
```
## Run tests manually with PostgreSQL

Start the PostgreSQL test database:

```bash
docker compose -f docker-compose.test.yml up -d postgres-test
```

Check that it is healthy:

```bash
docker compose -f docker-compose.test.yml ps
```

Then run:

```bash
docker compose -f docker-compose.test.yml run --rm --no-deps -e TEST_DB_DSN="pgsql:host=postgres-test;port=5432;dbname=merchant_payments_test" -e TEST_DB_USER="merchant_test" -e TEST_DB_PASSWORD="merchant_test_password" backend-tests ./vendor/bin/phpunit
```

## Run tests manually with MySQL

Start the MySQL test database:

```bash
docker compose -f docker-compose.test.yml up -d mysql-test
```

Check that it is healthy:

```bash
docker compose -f docker-compose.test.yml ps
```

Then run:

```bash
docker compose -f docker-compose.test.yml run --rm --no-deps -e TEST_DB_DSN="mysql:host=mysql-test;port=3306;dbname=merchant_payments_test;charset=utf8mb4" -e TEST_DB_USER="merchant_test" -e TEST_DB_PASSWORD="merchant_test_password" backend-tests ./vendor/bin/phpunit
```

## Clean up test containers

After manually running the tests:

```bash
docker compose -f docker-compose.test.yml down
```

## Expected SMTP error during tests

One notification test intentionally simulates an unavailable SMTP server to verify that a notification failure does not cause the payment itself to fail.
Because the notification service logs this exception, the following message can appear during the PHPUnit run:

```text
Payment notification email failed: SMTP unavailable
```

# Design decisions

## Vanilla PHP

A small MVC-style structure was implemented directly to keep the assignment lightweight while still maintaining separation between responsibilities.
Controllers handle HTTP concerns.
Services contain business logic.
Repositories handle persistence.
Models represent application data.
Payment provider classes handle PSP-specific behavior.
Notification classes handle email delivery.

## Dependency injection

Dependencies are passed into controllers and services instead of being created inside the business logic.
For example, `PaymentService` receives:

- a transaction repository
- the payment provider resolver
- the payment notification service

This keeps classes easier to test and avoids coupling them directly to specific infrastructure implementations.

## Repository abstraction

Repository interfaces were used so that services do not depend directly on PDO or SQL.
The current repository implementations use PDO because PostgreSQL, MySQL and SQLite are all relational databases.

## Fake payment provider

No external PSP is contacted.
A deterministic fake provider is used so the full payment flow can be tested locally.
The architecture still allows another payment provider to be added without changing the payment service or controllers.

## Email delivery

Mailpit is used rather than an external email service so the project remains completely self-contained.
The email sender itself is behind an interface so another implementation could replace the SMTP sender later.

## UTC timestamps

Transaction instants are stored in UTC.
The API returns explicit timezone-aware ISO 8601 timestamps.
The frontend converts them into the timezone of the current browser.
This avoids coupling persisted transaction data to the timezone of the server or a particular merchant location.

# Security considerations

The project includes several basic security considerations appropriate for the scope of the assignment:

- merchant passwords are hashed
- passwords are verified using `password_verify`
- API tokens are randomly generated
- only token hashes are stored
- authentication tokens expire
- protected endpoints require Bearer authentication
- card numbers are validated before processing
- CVV values are never persisted
- complete PAN values are never persisted
- only the final four card digits are stored
- transaction data is scoped to the authenticated merchant

This is still a demonstration application.

# Assumptions and trade-offs

The following decisions were made for the scope of the assignment.

### Currency

Payments currently use EUR.

### Email

Emails are captured locally by Mailpit rather than being delivered externally.
This makes both successful and failed payment notifications testable without requiring email credentials.

### Relational storage portability

The current PDO repositories support PostgreSQL, MySQL and SQLite.
The application is therefore not tied to one relational database engine.
Supporting a fundamentally different storage model, such as MongoDB, would require another implementation of the repository interfaces.
Docker handles these requirements as part of the build and startup process.

For example:

```bash
git clone https://github.com/Fabio-Saraseli/merchant-payment-app.git
cd merchant-payment-app
```

```bash
docker compose -f docker-compose.yml -f docker-compose.postgres.yml up --build
```

is sufficient to start a fresh PostgreSQL installation of the application.
The startup process initializes the database, executes migrations, seeds the demo merchants and starts the backend, frontend and Mailpit services.

# Test everything

An end-to-end test can be performed after starting the application.

1. Open [http://localhost:5173](http://localhost:5173).
2. Log in as `demo@merchant.test`.
3. Submit a successful payment using `4242424242424242`.
4. Verify that the transaction appears in Transaction History.
5. Verify that its displayed time matches the local browser timezone.
6. Open Mailpit and verify that the success notification was generated.
7. Submit a payment using `4000000000000002`.
8. Verify that the payment is declined.
9. Verify that the failed transaction appears in Transaction History.
10. Verify that the failed payment notification appears in Mailpit.
11. Test the From and To transaction filters.
12. Log out.
13. Log in as `second@merchant.test`.
14. Verify that transactions belonging to the first merchant are not visible.
15. Try again doing the same things for second merchant
