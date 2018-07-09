<?php

namespace BeyondCode\DumpServer;

use Illuminate\Http\Request;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\ContextProvider\ContextProviderInterface;

class RequestContextProvider implements ContextProviderInterface
{
    /** @var Request */
    private $currentRequest;

    /** @var VarCloner  */
    private $cloner;

    public function __construct(Request $currentRequest = null)
    {
        $this->currentRequest = $currentRequest;
        $this->cloner = new VarCloner();
        $this->cloner->setMaxItems(0);
    }

    public function getContext(): ?array
    {
        if (null === $this->currentRequest) {
            return null;
        }
        $controller = null;
        if ($route = $this->currentRequest->route()) {
            $controller = $route->controller;
        }

        return array(
            'uri' => $this->currentRequest->getUri(),
            'method' => $this->currentRequest->getMethod(),
            'controller' => $controller ? $this->cloner->cloneVar(class_basename($this->currentRequest->route()->controller)) : $controller,
            'identifier' => spl_object_hash($this->currentRequest),
        );
    }
}
