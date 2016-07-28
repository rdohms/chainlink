<?php

namespace DMS\Chainlink;

/**
 * Class NoHandlerException
 * Thrown when a Handler is not found to handle a given input.
 *
 * @package DMS\Chainlink
 */
class NoHandlerException extends \Exception
{

    /**
     * Throws a Handler not found exception
     * @return static
     */
    public static function notFound()
    {
        return new static('Unable to find a handler for provided input');
    }

}
