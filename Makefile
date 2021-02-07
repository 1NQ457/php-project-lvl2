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