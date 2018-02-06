## Software Stack

Voten is a Laravel application that runs on the following software:

- Ubuntu 16.04.2 LTS
- Nginx 1.10+
- MySQL 5.7+ (to use mariaDB, you must modify `json` type migration columns to `blob` by running `sed -i 's/json(/binary(/g' *` inside the database/migrations/ directory in Linux)
- PHP 7.1+
- Redis 3.0+
- Git 2.8.4+
- [Pusher](https://pusher.com/) (Voten.co uses [laravel-echo-server](https://github.com/tlaverdure/laravel-echo-server) on production server)
- [Algolia Search](https://www.algolia.com/referrals/fb684d54/join/)

To install all the required stack on a server, we recommend an auto-installation service such as [CodePier](https://codepier.io/?ref=voten).

## Installation Steps

Voten's installation is just like any other Laravel project. In case you face any errors, googling it with a "Laravel" keyword will find you an asnwer faster than opening an issue. 

After cloning the repository, first create a .env from the example file:

```
cp .env.example .env
```

Open ".env" file with your desired editor and enter your services info.
Now run below commands:

```
composer install
php artisan key:generate
php artisan migrate
php artisan passport:install
npm install
npm run production
```

### Create admin user

To create an admin user, run the below command from the root of the project

```
php artisan db:seed --class=AdminUserSeeder
```

The login details for the admin user is `admin` and `password`.

After running the seeder, be sure to clear your redis cache, you should now be able to navigate to `/backend`