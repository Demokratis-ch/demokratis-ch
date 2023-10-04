
# Symfony Server & Docker
```
docker-compose up -d
symfony server:start -d
```

Website `https://localhost:8000`
Database `root:root@localhost:3307`

# Nodejs & Tailwind Frontend

```npm install```

Then run Tailwind with Webpack Encore

```npm run watch```

# Process mail queue
```php bin/console messenger:consume async```

# Code checks
CS-Fixer ```php vendor/bin/php-cs-fixer fix```

Psalm ```./vendor/bin/psalm```, if needed with ```--no-cache```

TwigCS ```php vendor/bin/twigcs templates```

# Populate the search index
```php bin/console araise:search:populate```

# Production logs
Access the logs on platform.sh with
```symfony log app --tail```

# Tests
To run the test suite on your local machine you need to do the following setup:

1. Create the file `.env.test.local` and define the `DATABASE_URL`, similar to your `.env.local`. Use a different database name to keep tests separate from your lokal working environment.
2. `php bin/console --env=test doctrine:database:create`
3. `php bin/console --env=test doctrine:schema:create`

Once you have done that, you can execute the test suite with the command below:

`php bin/phpunit`
