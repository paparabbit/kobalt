Set of admin views and functionality to speed up development of small custom Content Management
Systems built in Laravel.... 

**Steps for using**

If it doesn't auto discover

**Config/app.php**

````
Hoppermagic\Kobalt\KobaltServiceProvider::class

````
Package will automatically add the following but you might want to add these in config.app

!TODO DO We need laravel collective?????

````
// Might need to add tinker too
Laravel\Tinker\TinkerServiceProvider::class,

Kris\LaravelFormBuilder\FormBuilderServiceProvider::class,
Laracasts\Flash\FlashServiceProvider::class,

'FormBuilder' => Kris\LaravelFormBuilder\Facades\FormBuilder::class,
'Flash'	=> Laracasts\Flash\Flash::class,
````

**Setup db preferences**

Run the standard laravel auth generator

````
php artisan make:auth
php artisan migrate
````

Register yourself, disable the auth routes in web.php

Add the non-registration routes from Router.php (auth method) to web.php

Remember to comment out the Auth::routes()

app.blade.php also contains a reference to the register route, so need to comment out

````
//!TODO MAKE SURE YOU USE THE ONES FROM ROUTER.PHP
// Authentication Routes...
$this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
$this->post('login', 'Auth\LoginController@login');
$this->post('logout', 'Auth\LoginController@logout')->name('logout');

// Password Reset Routes...
$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
$this->post('password/reset', 'Auth\ResetPasswordController@reset');
````



**Admin area web routes** 

could put these in their own admin.php file.....

````
Route::group([
        'namespace' => 'Admin',
        'prefix' => 'admin',
        'as' => 'admin.']
    ,function(){

        // STORIES
        Route::get('story/{story}/confirmdel', [
            'as' => 'story.confirmdel',
            'uses' => 'StoriesController@confirmDelete'
        ]);
        Route::resource('/story', 'StoriesController', [
            'except' => ['show'],
        ]);

        // STORY IMAGES
        Route::get('story/{story}/storyimage/{storyimage}/confirmdel', [
            'as' => 'story.{story}.storyimage.{storyimage}.confirmdel',
            'uses' => 'StoryImageController@confirmDelete'
        ]);
        Route::get('/story/{story}/storyimage/createbulk', 'StoryImageController@createBulkUpload')->name('storyimage.createbulk');
        Route::post('/story/{story}/storyimage/storebulk', 'StoryImageController@storeBulkUpload')->name('storyimage.storebulk');
        Route::resource('/story/{story}/storyimage', 'StoryImageController', [
            'except' => ['show','index'],
        ]);
    }
);

````

**Admin area api routes**

A little bit pointless doing this, as we need the web middleware on the group
NOT the api middleware so we could just stick these routes in the web area and
not mess with the api stuff... So if you need proper api stuff on the front end don't do.

RouteServiceProvider

````
protected function mapApiRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
````

Api.php

Use these for ajax image sorting, list sorting

````
// A little bit pointless putting these in here as we need the CSRF token to validate
// So essentially we still need the web middleware on the group

Route::group([
        'namespace' => 'Admin',
        'prefix' => 'admin',
    ]
    ,function(){

        // Stories ajax routes
        Route::post('story/{story}/edit/imageload', 'StoriesController@imageGalleryLoad');
        Route::post('story/{story}/edit/imagesort', 'StoriesController@imageGallerySort');
        Route::post('story/sort', 'StoriesController@overviewSort');
    }
);
````

**Make sure the following are pointing to the admin homepage**

Controllers\Auth\LoginController

Controllers\Auth\RegisterController

Controllers\Auth\ResetPasswordController

Middleware\RedirectIfAuthenticated

````
protected $redirectTo = '/admin/overviewpage'; // Admin landing page
````

**Css and Js links in admin template**

app.blade.php

````
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
<script src="{{ asset('js/admin.js') }}"></script>
````

**View Composer**

FYI, KobaltServiceProvider makes sure the active variable is available in the admin nav partial, its pushing in
segment(2) so make sure this is what you want. Undecided if this is a good thing.
 
**Publish assets** 

admin js,

css

admin images 

admin nav partial

use force to overwrite

after publishing amend the nav partial

````
php artisan vendor:publish --provider="Hoppermagic\Kobalt\KobaltServiceProvider" --tag=default --force
````

Publish all admin views

````
php artisan vendor:publish --provider="Hoppermagic\Kobalt\KobaltServiceProvider" --tag=admin-views --force
````

If you need to recompile the js/css

````
npm run production
````

Create model, admin controller, form and request

Will create RabbitController etc

````
php artisan make:ko-resources Rabbit
php artisan make:ko-controller Rabbit
php artisan make:ko-form Rabbit
````

//!TODO config/app.php

//!TODO Notes about editable, deleteable, addable

Form fields on desktop will be 60% width unless

````
'attr' => [
    'class' => 'form-control full-width'
],
````
Rich text fields

````
'attr' => [
    'class' => 'rich-text'
],
````


