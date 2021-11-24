# e.p.g

## Introduction

This project is a tailored content management system with e-shop features for the independent book publisher [e.p.g](https://epg.works). It is my first project and I am the only developer working on it so far. Even though the code is already being used in production, it was the project I learned Laravel with. I also use this repository to show-off my skills.

For those reasons, it is still far from being perfect, and I still have a lot to learn. Because ofthe project's nature, I do not intend to provide any in-depth documentation.

If you are interested, I'll be glad to answer any question.

## Quick install guide

1. Clone the repository and check you have write permissions on /storage.

```
clone git@github.com:shekenz/epg.git
```

2. Run dependencies installation

```
composer install
npm install
```

3. Copy and edit .env configuration file

```
cp .env.example .env
```

**Notes :**

`APP_LANG` : Used for faker locale, formated as en_US.

`APP_ALLOW_REGISTER` : Determines if any user can create an account, or if only invited users can.

`IMAGE_DRIVER` : Determines which PHP image library to use. Possible choices : gd or imagick.

After edition, don't forget to run `php artisan config:cache` to make the changes available.

4. Generate APP_KEY

```
php artisan key:generate
```

5. Link storage to public folder

```
php artisan storage:link
```

If you get an "Image source not readable" exception, you probably missed this step.

6. Run NPM script

```
npm run prod
```
or
```
npm run dev
```

7. Run migrations

```
php artisan migrate
```

8. Invite user

If `APP_ALLOW_REGISTER` is set to false, you have to invite users to join the backend admin panel with this command :

```
php artisan invite user.adress@mail.com
```

You can now follow the instructions you received by mail to register as the first user.

**Important :** The mail configs need to be correctly set up in the .env file in order for the app to send mails.

9. Login

Go to http://yourdomaine.name/login and login with your freshly created credentials and start publishing.

## Custom artisan commands

`invite user.adress@mail.com` : Invite a new user to register.

`db:seed` : Fills up database with fake books and fake orders (books will be created with existing media from the library).

`db:clean` : Empties books, orders and related pivot tables.

`backup:table table_name`: Creates a backup of a table.

`restore:table table_name`: Restores a backup of a table.


