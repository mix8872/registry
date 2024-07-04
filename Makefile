.PHONY : deploy

setup:
	composer install
	php artisan key:generate
	php artisan october:migrate
deploy:
	composer install
	php artisan migrate --force
	php artisan cache:clear
clear:
	php artisan clear-compiled
	php artisan optimize:clear
	php artisan route:clear
	composer dump-autoload
	php artisan optimize
	php artisan config:publish --all
	php artisan filament:upgrade
