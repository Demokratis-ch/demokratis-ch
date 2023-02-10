
# Symfony Server & Docker
```
docker-compose up -d
symfony server:start -d
```

Website `https://localhost:8000`
Database `root:root@localhost:3307`

# Run Tailwind with Webpack Encore

```npm run watch```

# Process mail queue
```php bin/console messenger:consume async```

# Code checks
CS-Fixer ```php vendor/bin/php-cs-fixer fix```

Psalm ```./vendor/bin/psalm```, if needed with ```--no-cache```

TwigCS ```php vendor/bin/twigcs templates```

# Populate the search index
```php bin/console whatwedo:search:populate```

# Production logs
Access the logs on platform.sh with
```symfony log app --tail```
