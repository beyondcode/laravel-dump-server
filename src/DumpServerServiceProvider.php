<?php

namespace BeyondCode\DumpServer;

use BeyondCode\DumpServer\FallbackDumper;
use Illuminate\Foundation\Console\CliDumper as LaravelCliDumper;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Symfony\Component\VarDumper\Server\Connection;
use Symfony\Component\VarDumper\Server\DumpServer;
use Symfony\Component\VarDumper\VarDumper;

class DumpServerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('debug-server.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'debug-server');

        $this->app->bind('command.dumpserver', DumpServerCommand::class);

        $this->commands([
            'command.dumpserver',
        ]);

        $host = $this->app['config']->get('debug-server.host');

        $this->app->when(DumpServer::class)->needs('$host')->give($host);

        $this->app->when(FallbackDumper::class)->needs('$basePath')->give(fn () => $this->app->basePath());
        $this->app->when(FallbackDumper::class)->needs('$compiledViewPath')->give(fn () => $this->app['config']->get('view.compiled'));

        $connection = new Connection($host, [
            'request' => new RequestContextProvider($this->app['request']),
            'source' => new SourceContextProvider('utf-8', base_path()),
        ]);

        $fallbackDumper = class_exists(LaravelCliDumper::class)
            ? $this->app->make(FallbackDumper::class)
            : null;

        VarDumper::setHandler(function ($var) use ($connection, $fallbackDumper) {
            $this->app->makeWith(Dumper::class, ['connection' => $connection, 'fallbackDumper' => $fallbackDumper])->dump($var);
        });
    }
}
