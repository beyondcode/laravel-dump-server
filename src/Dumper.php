<?php

namespace BeyondCode\DumpServer;

use BeyondCode\DumpServer\FallbackDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Server\Connection;

class Dumper
{
    /**
     * The connection.
     *
     * @var \Symfony\Component\VarDumper\Server\Connection|null
     */
    private $connection;

    /**
     * The fallback dumper to use if there is no active connection.
     *
     * @var \BeyondCode\DumpServer\FallbackDumper|null
     */
    private $fallbackDumper;

    /**
     * Dumper constructor.
     *
     * @param  \Symfony\Component\VarDumper\Server\Connection|null  $connection
     * @param  \BeyondCode\DumpServer\FallbackDumper|null  $fallbackDumper
     * @return void
     */
    public function __construct(Connection $connection = null, FallbackDumper $fallbackDumper = null)
    {
        $this->connection = $connection;
        $this->fallbackDumper = $fallbackDumper;
    }

    /**
     * Dump a value with elegance.
     *
     * @param  mixed  $value
     * @return void
     */
    public function dump($value)
    {
        if (class_exists(CliDumper::class)) {
            $data = $this->createVarCloner()->cloneVar($value);

            if ($this->connection === null || $this->connection->write($data) === false) {
                $dumper = $this->fallbackDumper ?? (in_array(PHP_SAPI, ['cli', 'phpdbg']) ? new CliDumper : new HtmlDumper);

                $dumper->dump($data);
            }
        } else {
            var_dump($value);
        }
    }

    /**
     * @return VarCloner
     */
    protected function createVarCloner(): VarCloner
    {
        return new VarCloner();
    }
}
