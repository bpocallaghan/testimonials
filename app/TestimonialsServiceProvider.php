<?php

namespace Bpocallaghan\Testimonials;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Bpocallaghan\Testimonials\Commands\PublishCommand;
use Bpocallaghan\Testimonials\Models\Testimonial;

class TestimonialsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'testimonials');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        
        $this->publishResources();
        $this->registerBladeDirectives();
        $this->registerViewComposers();
        $this->registerCommands();
        $this->registerMacros();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/testimonials.php', 'testimonials');
        
        $this->app->singleton('testimonials', function ($app) {
            return new TestimonialsManager($app);
        });
    }

    /**
     * Publish package resources
     */
    private function publishResources()
    {
        // Publish views
        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/testimonials')
        ], 'testimonials-views');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations')
        ], 'testimonials-migrations');

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/testimonials.php' => config_path('testimonials.php')
        ], 'testimonials-config');

        // Publish assets
        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path('vendor/testimonials')
        ], 'testimonials-assets');
    }

    /**
     * Register Blade directives
     */
    private function registerBladeDirectives()
    {
        // @testimonials directive
        Blade::directive('testimonials', function ($expression) {
            return "<?php echo app('testimonials')->render($expression); ?>";
        });

        // @testimonial directive
        Blade::directive('testimonial', function ($expression) {
            return "<?php echo app('testimonials')->renderSingle($expression); ?>";
        });

        // @testimonialsCount directive
        Blade::directive('testimonialsCount', function () {
            return "<?php echo app('testimonials')->count(); ?>";
        });
    }

    /**
     * Register view composers
     */
    private function registerViewComposers()
    {
        // Share testimonials data with all views
        View::composer('*', function ($view) {
            $view->with('testimonialsCount', Testimonial::count());
        });

        // Share recent testimonials
        View::composer('layouts.app', function ($view) {
            $view->with('recentTestimonials', Testimonial::latest()->take(3)->get());
        });
    }

    /**
     * Register commands
     */
    private function registerCommands()
    {
        $this->registerCommand(PublishCommand::class, 'publish');
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\TestimonialsInstallCommand::class,
                Commands\TestimonialsSeedCommand::class,
                Commands\TestimonialsClearCommand::class,
            ]);
        }
    }

    /**
     * Register macros
     */
    private function registerMacros()
    {
        // Add testimonials macro to Collection
        \Illuminate\Support\Collection::macro('testimonials', function () {
            return $this->filter(function ($item) {
                return $item instanceof Testimonial;
            });
        });

        // Add testimonials macro to Query Builder
        \Illuminate\Database\Eloquent\Builder::macro('published', function () {
            return $this->whereNull('deleted_at');
        });
    }

    /**
     * Register a singleton command
     *
     * @param $class
     * @param $command
     */
    private function registerCommand($class, $command)
    {
        $path = 'bpocallaghan.testimonials.commands.';
        $this->app->singleton($path . $command, function ($app) use ($class) {
            return $app[$class];
        });

        $this->commands($path . $command);
    }
}
