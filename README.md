## About This Application

This simple scaffolding can be used in part, or in full to send emails using a variety of SMTP servers (together or standalone) with little to no set-up. Simply:
- Populate the .env file with your credentials (mostly pre-populated for Sengrid, Gmail, Outlook)
- Fill in the respective fields (i.e. "From" address) within the Mail classes
- Migrate
- Run ```composer install```
- Populate the UserSeed class in order to pre-populate a User (if using authentication)
  - If using Auth, uncomment route(s) within the route group utilizing authentication middleware as well as the Auth routes themselves
- Run ```php artisan migrate --seed```
  - Don't need to seed if authentication is not being used
- Create a template and submit it to a connected database (or use XAMPP and store locally)
  - Be sure to update the controllers (and environment variables) depending on whether you wish to use S3 for storage of documents/email attachments or if you'd rather use local storage. Right now it is set up for S3
- Submit import file (Excel) corresponding to created template

Composer Packages Used:
- [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet)
  - ```composer require phpoffice/phpspreadsheet```

## Using This Application

Depending on whether you've implemented authentication, you may need to configure routes and controllers to your liking. By default, all you need to do is input your environment variables and configure any domains/mail addresses within the job/mailable classes themselves. These can be channeled through the config files as well but I found it more readable to directly input within the classes.

After this, simply serve the site (```php artisan serve```), start both queues using terminal (```php artisan queue:work``` and ```php artisan queue:work --queue=processing```), and upload an Excel file which has column headers corresponding to the [variables] you've used in your template.

Environment variables to update per smtp server (examples, but pre-plugged into config)
```angular2html
MAIL_DRIVER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=465
MAIL_USERNAME=apikey
MAIL_PASSWORD=
MAIL_ENCRYPTION=ssl
MAIL_FROM_NAME="Application Name"

MAIL_DRIVER_OUTLOOK=outlook
MAIL_HOST_OUTLOOK=smtp.office365.com
MAIL_PORT_OUTLOOK=587
MAIL_USERNAME_OUTLOOK=
MAIL_PASSWORD_OUTLOOK=
MAIL_ENCRYPTION_OUTLOOK=tls
MAIL_FROM_NAME_OUTLOOK="Application Name"

MAIL_DRIVER_GMAIL=gmail
MAIL_HOST_GMAIL=smtp.gmail.com
MAIL_PORT_GMAIL=465
MAIL_USERNAME_GMAIL=
MAIL_PASSWORD_GMAIL=
MAIL_ENCRYPTION_GMAIL=ssl
MAIL_FROM_NAME_GMAIL="Application Name"
```



## Some Notes

This application uses some custom logic within the 'report' method of the global exception handler class.

Currently, beyond the default Laravel security bounds, there is no request sanitization or custom middleware for security.

Some packages I personally like to implement when starting a project like this:

- [Laravel Auditing](https://laravel-auditing.com/)
  - ```composer require owen-it/laravel-auditing```
- [Laravel Permissions (by Spatie)](https://spatie.be/docs/laravel-permission/v6/introduction)
  - ```composer require spatie/laravel-permission```

## Liability

This project is published for demonstrative and educational purposes only. I cannot be held liable for any security vulnerabilities therein or from problems arising from the use of this application.
## License

This project and the underlying Laravel Framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
