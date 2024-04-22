# Backend setup
change directory to backend <br>
<code>cd src/backend/ </code>

**Install latest php** <br />
<code>sudo apt update</code><br />
<code>sudo apt install php php-cli php-common php-mysql php-xml php-mbstring php-json php-sqlite3 php-gd </code><br />

**Install and start** <br />
<code>sudo apt install zip unzip</code><br />
<code>sudo curl -sS https://getcomposer.org/installer -o composer-setup.php</code><br />
<code>sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer</code><br />
<code>rm -rf composer-setup.php</code><br />

**Composer insatall**<br />
<code>php artisan vendor:publish --provider="Nuwave\Lighthouse\LighthouseServiceProvider" --tag=config</code><br />

<code>touch ./database/database.sqlite</code><br />
<code>php artisan migrate</code><br />
<code>php artisan db:seed</code><br />
<code>php artisan cache:clear</code><br />
<code>php artisan config:cache</code><br />
<code>composer dump-autoload</code><br />
<code>php artisan serve</code><br />

# Frontend setup
change directory to frontend <br />
<code>cd src/frontend/</code><br />

## install and start
<code>nvm use v18.12.1</code><br />
<code>npm install</code><br />
<code>npm run dev</code><br />

# Check the web app
Go to http://127.0.0.1/3000<br />
Login using this details:<br />
Email: graphql@test.com<br />
Password: secret<br />

You can also try graphql server: http://127.0.0.1/8000/graphql. Just use any graphql client like graphql-playground.<br/>
Check sample queries here src/sample_graphql_queries.

# TODO<br />
Will add docker setup for easy local installation<br />
