# Backend setup
change directory to backend
cd backend/

## install latest php
sudo apt update
sudo apt install php php-cli php-common php-mysql php-xml php-mbstring php-json php-sqlite3 php-gd

## install and start
sudo apt install zip unzip
sudo curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm -rf composer-setup.php

composer insatall
php artisan vendor:publish --provider="Nuwave\Lighthouse\LighthouseServiceProvider" --tag=config

touch ./database/database.sqlite
php artisan migrate
php artisan db:seed
php artisan cache:clear
php artisan config:cache
composer dump-autoload
php artisan serve

# Frontend setup
change directory to frontend
cd frontend/

## install and start
nvm use v18.12.1
npm install
npm run dev

# Check the web app
Go to http://127.0.0.1/3000
Login using this details blow.
Email: graphql@test.com
Password: secret

# TODO
Will add docker setup for easy local installation
