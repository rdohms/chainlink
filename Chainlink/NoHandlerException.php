<?php

namespace Symbid\Chainlink;

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
