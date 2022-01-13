ifndef BUILD_ENV
BUILD_ENV=8.0
endif

PHPSTAN_OUTPUT=
PSALM_OUTPUT=
define start
endef
define end
endef
ifdef GITHUB_ACTIONS
define start
echo ::group::$(1) in ${CURDIR}
endef
define end
echo ::endgroup::
endef
PHPSTAN_OUTPUT=--error-format=github
PSALM_OUTPUT=--output-format=github
endif

ifndef TTY
TTY:=$(shell [ -t 0 ] && echo --tty)
endif

ifndef DOCQA_DOCKER_COMMAND
DOCQA_DOCKER_IMAGE=dkarlovi/docqa:latest
DOCQA_DOCKER_COMMAND=docker run --init --interactive ${TTY} --rm --env HOME=/tmp --user "$(shell id -u):$(shell id -g)"  --volume "$(shell pwd)/${MAKEFILE_ROOT}/docs:/config" --volume "$(shell pwd)/var/tmp/docqa:/.cache" --volume "$(shell pwd):/project" --workdir /project ${DOCQA_DOCKER_IMAGE}
endif

ifndef PHPQA_DOCKER_COMMAND
PHPQA_DOCKER_IMAGE=jakzal/phpqa:1.64.0-php${BUILD_ENV}-alpine
PHPQA_DOCKER_COMMAND=docker run --init --interactive ${TTY} --rm --env "COMPOSER_CACHE_DIR=/composer/cache" --user "$(shell id -u):$(shell id -g)" --volume "$(shell pwd)/var/tmp/phpqa:/tmp" --volume "$(shell pwd):/project" --volume "${HOME}/.composer:/composer" --workdir /project ${PHPQA_DOCKER_IMAGE}
endif

dist: composer-normalize cs check test docs
check: composer-normalize-check cs-check phpstan psalm
# test: infection
test: phpunit-coverage
docs: markdownlint vale

.SILENT:

define environment
	$(shell test -f ${BUILD_ENV}-${1} && echo -n ${BUILD_ENV}-${1} || echo ${1})
endef

composer/dev:
	symfony php /usr/bin/composer install
composer/prod:
	symfony php /usr/bin/composer install -a --no-dev --no-scripts

start/dev: composer/dev
	APP_ENV=dev APP_DEBUG=1 symfony serve --no-tls
start/prod: composer/prod clean
	APP_ENV=prod APP_DEBUG=0 symfony serve --no-tls

deploy: composer/prod clean
	symfony php bin/console -e prod cache:clear
	serverless deploy

composer-validate: ensure composer-normalize-check
	sh -c "${PHPQA_DOCKER_COMMAND} composer validate --no-check-lock"
composer-install: ensure
	$(call start,Composer install)
	sh -c "${PHPQA_DOCKER_COMMAND} composer upgrade"
	$(call end)
composer-install-lowest: ensure
	$(call start,Composer install)
	sh -c "${PHPQA_DOCKER_COMMAND} composer upgrade --with-all-dependencies --prefer-lowest"
	$(call end)
composer-normalize: ensure
	sh -c "${PHPQA_DOCKER_COMMAND} composer normalize --no-check-lock"
composer-normalize-check: ensure
	$(call start,Composer normalize)
	sh -c "${PHPQA_DOCKER_COMMAND} composer normalize --no-check-lock --dry-run"
	$(call end)

cs: ensure
	sh -c "${PHPQA_DOCKER_COMMAND} php-cs-fixer fix --diff -vvv"
cs-check: ensure
	$(call start,PHP CS Fixer)
	sh -c "${PHPQA_DOCKER_COMMAND} php-cs-fixer fix --dry-run --diff -vvv"
	$(call end)

phpstan: ensure
	$(call start,PHPStan)
	sh -c "${PHPQA_DOCKER_COMMAND} phpstan analyse ${PHPSTAN_OUTPUT} --configuration $(call environment,phpstan.neon.dist)"
	$(call end)

psalm: ensure
	$(call start,Psalm)
	sh -c "${PHPQA_DOCKER_COMMAND} psalm --php-version=${BUILD_ENV} ${PSALM_OUTPUT} --config $(call environment,psalm.xml.dist)"
	$(call end)

phpunit:
	$(call start,PHPUnit)
	sh -c "${PHPQA_DOCKER_COMMAND} vendor/bin/phpunit --verbose"
	$(call end)
phpunit-coverage: ensure
	$(call start,PHPUnit)
	sh -c "${PHPQA_DOCKER_COMMAND} php -d pcov.enabled=1 vendor/bin/phpunit --verbose --coverage-text --log-junit=var/junit.xml --coverage-xml var/coverage-xml/"
	$(call end)

infection: phpunit-coverage
	$(call start,Infection)
	sh -c "${PHPQA_DOCKER_COMMAND} infection run --verbose --show-mutations --no-interaction --only-covered --coverage var/ --threads 4"
	$(call end)

markdownlint: ensure
	$(call start,Markdownlint)
	sh -c "${DOCQA_DOCKER_COMMAND} markdownlint README.md"
	$(call end)
vale: ensure
	$(call start,Vale)
	sh -c "${DOCQA_DOCKER_COMMAND} vale --config /config/.vale.ini.dist README.md"
	$(call end)

ensure: clean
	mkdir -p ${HOME}/.composer var/tmp/docqa var/tmp/phpqa
clean:
	rm -rf var/cache/* var/phpstan var/psalm
