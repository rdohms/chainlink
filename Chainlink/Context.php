<?php

namespace Symbid\Chainlink;

use Symbid\Chainlink\Handler\HandlerInterface;

class Context
{

    /**
     * @var HandlerInterface[]
     */
    protected $handlers = [];

    /**
     * Registers a new handler in the list
     * @param HandlerInterface $handler
     */
    public function addHandler(HandlerInterface $handler)
    {
        if (in_array($handler, $this->handlers)) {
            return;
        }

        $this->handlers[] = $handler;
    }

    /**
     * Gets handler compatible with this input
     *
     * @param mixed $input
     * @return HandlerInterface
     * @throws NoHandlerException
     */
    public function getHandlerFor($input)
    {
        $compatibleHandlers = array_filter(
            $this->handlers,
            function (HandlerInterface $handler) use ($input) {
                return $handler->handles($input);
            }
        );

        if (empty($compatibleHandlers)) {
            throw NoHandlerException::notFound();
        }

        return array_shift($compatibleHandlers);
    }

    /**
     * Handlers input by delegating to the proper handler
     *
     * @param mixed $input
     * @return mixed
     * @throws NoHandlerException
     */
    public function handle($input)
    {
        return $this->getHandlerFor($input)->handle($input);
    }
}
