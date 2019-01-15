# Howzit API

A simple API to handle submission of contact information forms
and add custom email response templates.

## Usage
- Register an account
- Submit a new form, specifying expected fields
- Point form responses to this endpoint, we'll forward the response to the specified `owner_email`

## Setup

- `composer install`
- `mysql -u root -e 'create schema howzit;'`

- `cp .env.example .env`
- Populate `.env` with required data.
- `php artisan migrate --seed`
- `php -S localhost:8005 -t ./public`
