install:
	composer install

gendiff:
	./bin/gendiff

lint:
	composer run-script phpcs -- --standard=PSR12 src bin

validate:
	composer validate

dump:
	composer dump-autoload

test:
	composer run-script phpunit tests

test-coverage:
	composer exec --verbose XDEBUG_MODE=coverage phpunit tests -- --coverage-clover build/logs/clover.xml