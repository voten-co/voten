# Voten.co

Voten.co is a real-time social bookmarking for the 21st century. It's real-time, beautiful, customizable yet simple. To get a quick taste of what Voten is written with please check our [credits page](https://voten.co/credits).

## Contributing

Thank you for considering contributing to the Voten. To encourage active collaboration, Voten strongly encourages pull requests, not just bug reports. If you have an idea(and not the code for it) you may contact us either with the info@voten.co email address or submit it to [/c/votendev](https://voten.co/c/votendev) channel.

## Coding Style

Voten follows the PSR-2 coding standard and the PSR-4 autoloading standard. Voten also uses [StyleCI](https://styleci.io) for automatically merging any style fixes. So you don't have to worry about your code style much.

## Software Stack

Voten is a Laravel application that runs on the following software:

- Ubuntu 16.04.2 LTS
- Nginx 1.10+
- MySQL 5.7+ (to use mariaDB, you must modify `json` type migration columns to `blob`)
- PHP 7.1+
- Redis 3.0+
- Git 2.8.4+
- [Pusher](https://pusher.com/)

To install all the required stack on a server, we recommend an auto-installation service such as [Codepier](https://codepier.io/).

## Installation Steps

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

## Security Vulnerabilities

If you discover any security vulnerability within Voten's source code, please send an e-mail to Sully Fischer at fischersully@gmail.com instead of opening an issue. All security vulnerabilities will be promptly addressed.

## API

A public API is the next step of Voten's development. In the meanwhile, if you're interested in developing applications on top of our API please contact us at info@voten.co.