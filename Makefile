composer/dev:
	symfony php /usr/bin/composer install
composer/prod:
	symfony php /usr/bin/composer install -a --no-dev --no-scripts

start/dev: composer/dev
	APP_ENV=dev APP_DEBUG=1 symfony serve --no-tls
start/prod: composer/prod cache
	APP_ENV=prod APP_DEBUG=0 symfony serve --no-tls

deploy: composer/prod cache
	symfony php bin/console -e prod cache:clear
	serverless deploy

cache:
	rm -rf var/cache/*
