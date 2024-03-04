# Huggy

## Decisions / trade-offs

The project was setup to use PHPStan for static analysis at the strictest level, and use Github Actions for CI to check each pull request against coding standards (Laravel Pint), static analysis (PHPStan), and tests (PHPUnit).

There were several conscious design decisions taken in order to keep development simpler.

- a token is generated when a reader is created
- token is being shown when viewing/listing readers
- only requests to mark a book as read are validated against the token
- address is a string instead of several specific fields (street, number, city...)
- cache is only created/updated when marking a book as read and thus, it'll not be available for seeded data
- cache for a reader isn't dropped when a reader is deleted
- once a birthday email is sent, it's not being marked as sent in order to prevent sending duplicate emails if the job is run more than once in the same day (for example, manually executing it)
- lots of errors were default to 404s instead of having meaningful error messages and http responses

I'm aware those would be critical misses for a real application, but again, those were conscious decisions to keep the scope minimal.

## Setup / Running

To install/run the application, it needs to have any modern PHP version, git, composer, and docker installed locally. All other dependencies are handled by the docker setup.

Clone the repository

```sh
git clone git@github.com:marcelo-lipienski/huggy
```

Move into project's root directory

```sh
cd huggy
```

Copy .env.example into .env
```sh
cp .env.example .env

# You'll need to add your own RD_STATION_CRM_TOKEN to create a contact when a reader is created
```

Install depencencies

```sh
composer install
```

Boot the application using docker

```sh
sail up -d
```

Run migrations

```sh
sail artisan migrate
```

**Optional:** Seed database

```sh
sail artisan db:seed
```

Run tests

```sh
sail tests
```

**Optional:** Run static analysis

```sh
sail composer stan
```

**Optional:** Run code style checker

```sh
sail pint
```

For easier manual testing, there's a `huggy-api.json` at project's root directory with requests to all endpoints with some sample data.

Be aware that because I chose to show all errors as 404, trying to delete a publisher that is assigned to a book will throw a 404, that's because before deleting a publisher, all references to that publisher must be deleted from existing books.