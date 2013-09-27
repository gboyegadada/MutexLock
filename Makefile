install:
	composer install

test:
	vendor/bin/phpunit --colors --bootstrap tests/bootstrap.php tests
