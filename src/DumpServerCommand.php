<?php

namespace BeyondCode\DumpServer;

use Illuminate\Console\Command;

use InvalidArgumentException;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Command\Descriptor\HtmlDescriptor;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Server\DumpServer;
use Symfony\Component\VarDumper\Command\Descriptor\CliDescriptor;

class DumpServerCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'dump-server {--format=cli : The output format (cli,html)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the dump server to collect dump information';

    /**
     * @var \Symfony\Component\VarDumper\Server\DumpServer
     */
    private $server;

    public function __construct(DumpServer $server)
    {
        $this->server = $server;

        parent::__construct();
    }

    public function handle()
    {
        switch ($format = $this->option('format')) {
            case 'cli':
                $descriptor = new CliDescriptor(new CliDumper);
                break;
            case 'html':
                $descriptor = new HtmlDescriptor(new HtmlDumper);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Unsupported format "%s".', $format));
        }

        $io = new SymfonyStyle($this->input, $this->output);

        $errorIo = $io->getErrorStyle();
        $errorIo->title('Laravel Var Dump Server');

        $this->server->start();

        $errorIo->success(sprintf('Server listening on %s', $this->server->getHost()));
        $errorIo->comment('Quit the server with CONTROL-C.');

        $this->server->listen(function (Data $data, array $context, int $clientId) use ($descriptor, $io) {
            $descriptor->describe($io, $data, $context, $clientId);
        });
    }
}
