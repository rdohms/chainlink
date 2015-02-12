<?php

namespace Symbid\Chainlink;

use Symbid\Chainlink\Handler\HandlerInterface;

/**
 * Class Context
 * Holds a list of handlers and can find the one responsible to handle a given input
 *
 * @package Symbid\Chainlink
 */
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
        if (in_array($handler, $this->handlers, true)) {
            return;
        }

        $this->handlers[] = $handler;
    }

    /**
     * Gets a single (first) handler compatible with this input
     *
     * @param mixed $input
     * @return HandlerInterface
     * @throws NoHandlerException
     */
    public function getHandlerFor($input)
    {
        $allHandlers = $this->getAllHandlersFor($input);
        return array_shift($allHandlers);
    }

    /**
     * Retrieves all the handlers the can handle this input
     *
     * @param mixed $input
     * @return HandlerInterface[]
     * @throws NoHandlerException
     */
    public function getAllHandlersFor($input)
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

        return $compatibleHandlers;
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
