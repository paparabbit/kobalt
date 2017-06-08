Publish admin js, css and images
````
php artisan vendor:publish --provider="Hoppermagic\Kobalt\KobaltServiceProvider" --tag=public --force
````
Create model, admin controller, form and request

Will create RabbitController etc

````
php artisan make:ko-resources Rabbit
````
