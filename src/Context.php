<?php

namespace Symbid\Chainlink;

use InvalidArgumentException;
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
     * @var array of handlers prefixed with their priority
     */
    protected $unsortedHandlers = [];

    /**
     * Registers a new handler in the list
     *
     * @param HandlerInterface $handler
     * @param int $priority
     * @throws InvalidArgumentException
     */
    public function addHandler(HandlerInterface $handler, $priority = 0)
    {
        // make sure priority is an actual number
        if (filter_var($priority, FILTER_VALIDATE_INT) === false) {
            throw new InvalidArgumentException("Argument 'priority' should be an integer, got '$priority'");
        }

        // don't add the same handler twice
        if (in_array($handler, $this->handlers, true)) {
            return;
        }

        // make sure we don't overwrite existing handlers
        $this->unsortedHandlers[$priority][] = $handler;

        $this->handlers = $this->sortHandlers($this->unsortedHandlers);
    }

    /**
     * Sort an array of arrays, where the index key is the priority so we support multiple handlers for 1 priority.
     * This ensures that we adhere to FIFO
     *
     * @param $unsortedHandlers
     * @return array
     */
    private function sortHandlers(array $unsortedHandlers)
    {
        $handlers = [];

        // sort handlers by priority high to low
        krsort($unsortedHandlers);

        // turn the sorted handlers into 1 array
        foreach ($unsortedHandlers as $priorityArray) {
            $handlers = array_merge($handlers, $priorityArray);
        }

        return $handlers;
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
     * Retrieves all the handlers that can handle this input
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
