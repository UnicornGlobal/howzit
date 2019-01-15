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

# Authors

Add yourself here if you contribute.

* [Fergus Strangways-Dixon](https://github.com/fergusdixon)
* [Brian Maiyo](https://github.com/kiproping)
* [Darryn Ten](https://github.com/darrynten)
* [Bamidele Daniel](https://github.com/humanityjs)
* [Unicorn Global et al](https://github.com/UnicornGlobal)
