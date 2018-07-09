<?php

namespace BeyondCode\DumpServer;

use Illuminate\Console\Command;

use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Server\DumpServer;
use Symfony\Component\VarDumper\Command\Descriptor\CliDescriptor;

class DumpServerCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'dump-server';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the dump server to collect dump information.';

    private $server;

    public function __construct(DumpServer $server)
    {
        $this->server = $server;

        parent::__construct();
    }

    public function handle()
    {
        $descriptor = new CliDescriptor(new CliDumper());

        $this->server->start();

        $this->output->title('Laravel Var Dump Server');
        $this->output->success(sprintf('Server listening on %s', $this->server->getHost()));
        $this->output->comment('Quit the server with CONTROL-C.');

        $this->server->listen(function (Data $data, array $context, int $clientId) use ($descriptor) {
            $descriptor->describe($this->output, $data, $context, $clientId);
        });
    }

}