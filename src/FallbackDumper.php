<?php

namespace BeyondCode\DumpServer;

use Illuminate\Foundation\Console\CliDumper as LaravelCliDumper;
use Illuminate\Foundation\Http\HtmlDumper as LaravelHtmlDumper;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Dumper\CliDumper as SymfonyCliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper as SymfonyHtmlDumper;

class FallbackDumper
{
    /**
     * The base path of the application.
     *
     * @var string
     */
    protected $basePath;

    /**
     * The compiled view path for the application.
     *
     * @var string
     */
    protected $compiledViewPath;

    /**
     * FallbackDumper constructor.
     *
     * @param  string  $basePath
     * @param  string  $compiledViewPath
     * @return void
     */
    public function __construct(string $basePath, string $compiledViewPath)
    {
        $this->basePath = $basePath;
        $this->compiledViewPath = $compiledViewPath;
    }

    /**
     * Dump a value with elegance.
     *
     * @param  Data  $data
     * @return void
     */
    public function dump(Data $data)
    {
        if (class_exists(LaravelCliDumper::class)) {
            // Laravel 9+
            $dumper = in_array(PHP_SAPI, ['cli', 'phpdbg'])
                ? new LaravelCliDumper(new ConsoleOutput, $this->basePath, $this->compiledViewPath)
                : new LaravelHtmlDumper($this->basePath, $this->compiledViewPath);

            $dumper->dumpWithSource($data);
        } else {
            $dumper = in_array(PHP_SAPI, ['cli', 'phpdbg'])
                ? new SymfonyCliDumper
                : new SymfonyHtmlDumper;

            $dumper->dump($data);
        }
    }
}
