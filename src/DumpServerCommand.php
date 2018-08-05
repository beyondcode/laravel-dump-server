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

    /** @var DumpServer  */
    private $server;

    /**
     * @var \Symfony\Component\VarDumper\Command\Descriptor\DumpDescriptorInterface[]
     */
    private $descriptors;

    public function __construct(DumpServer $server)
    {
        $this->server = $server;

        $this->descriptors = [
            'cli' => new CliDescriptor(new CliDumper()),
            'html' => new HtmlDescriptor(new HtmlDumper()),
        ];

        parent::__construct();
    }

    public function handle()
    {
        $io = new SymfonyStyle($this->input, $this->output);
        $format = $this->option('format');

        if (! $descriptor = $this->descriptors[$format] ?? null) {
            throw new InvalidArgumentException(sprintf('Unsupported format "%s".', $format));
        }

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
