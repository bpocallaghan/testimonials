# Testimonials
This will add Testimonials to your laravel project.

## Installation
Update your project's `composer.json` file.

```bash
composer require bpocallaghan/testimonials
```

## Usage

Register the routes in the `routes/vendor.php` file.
- Website
```bash
Route::resource('testimonials', 'Testimonials\Controllers\Website\TestimonialsController');
```
- Admin
```bash
Route::group(['prefix' => 'general', 'namespace' => 'Testimonials\Controllers\Admin'], function () {
    Route::get('testimonials/order', 'OrderController@index');
    Route::post('testimonials/order', 'OrderController@updateOrder');
    Route::resource('testimonials', 'TestimonialsController');
});
```

## Commands
```bash
php artisan testimonials:publish
```
This will copy the `database/seeds` and `database/migrations` to your application.
Remember to add `$this->call(TestimonialsTableSeeder::class);` in the `DatabaseSeeder.php`

```bash
php artisan testimonials:publish --files=all
```
This will copy the `model, views and controller` to their respective directories. 
Please note when you execute the above command. You need to update your `routes`.
- Website
```bash 
Route::get('/testimonials', 'TestimonialsController@index');
```
- Admin
```bash
Route::group(['namespace' => 'Testimonials'], function () {
    Route::get('testimonials/order', 'OrderController@index');
    Route::post('testimonials/order', 'OrderController@updateOrder');
    Route::resource('testimonials', 'TestimonialsController');
});
```

## Demo
Package is being used at [Laravel Admin Starter](https://github.com/bpocallaghan/laravel-admin-starter) project.

### TODO
- add the navigation seeder information (to create the navigation/urls)