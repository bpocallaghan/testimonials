<?php

namespace Bpocallaghan\Testimonials;

use Illuminate\Support\ServiceProvider;
use Bpocallaghan\Testimonials\Commands\PublishCommand;

class TestimonialsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views/admin', 'admin.testimonials');
        $this->loadViewsFrom(__DIR__ . '/../resources/views/website', 'website.testimonials');

        $this->registerCommand(PublishCommand::class, 'publish');
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
