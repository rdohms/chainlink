<?php

namespace Symbid\Chainlink\Handler;

interface HandlerInterface
{

    /**
     * Is the handler capable of handling this input
     *
     * @param mixed $input
     * @return boolean
     */
    public function handles($input);

    /**
     * Execute actual handling of given input
     *
     * @param mixed $input
     * @return mixed
     */
    public function handle($input);
}
