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
     * Registers a new handler in the list
     *
     * @param HandlerInterface $handler
     * @param int $priority the higher the number the higher the priority
     * @throws InvalidArgumentException
     */
    public function addHandler(HandlerInterface $handler, $priority = 0)
    {
        if (filter_var($priority, FILTER_VALIDATE_INT) === false) {
            throw new InvalidArgumentException("Argument 'priority' should be an integer, got '$priority'");
        }

        if ($this->handlerAlreadyRegistered($handler)) {
            return;
        }

        $this->handlers[$priority][] = $handler;
    }

    /**
     * Sorts the priority list but leaves the original insertion order for clashes untouched.
     * This ensures that we adhere to FIFO within each priority level.
     *
     * Priority is sorted from HIGH to LOW
     */
    protected function sortHandlers()
    {
        krsort($this->handlers);
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
        $this->sortHandlers();

        $filteredIterator = new \CallbackFilterIterator(
            $this->getHandlerIterator(),
            function (HandlerInterface $handler) use ($input) {
                return $handler->handles($input);
            }
        );

        $compatibleHandlers = iterator_to_array($filteredIterator, false);

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

    /**
     * Checks if handler is already registered
     *
     * @param HandlerInterface $handler
     * @return bool
     */
    public function handlerAlreadyRegistered(HandlerInterface $handler)
    {
        foreach ($this->getHandlerIterator() as $registeredHandler) {
            if ($registeredHandler === $handler) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sets up and returns a recursive handler iterator.
     *
     * @return \RecursiveIteratorIterator
     */
    protected function getHandlerIterator()
    {
        return new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($this->handlers, \RecursiveArrayIterator::CHILD_ARRAYS_ONLY),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
    }
}
