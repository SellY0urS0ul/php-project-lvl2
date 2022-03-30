install:
	composer install
validate:
	composer validate
	composer dump-autoload	
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin
test:
	vendor/bin/phpunit tests 