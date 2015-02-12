# Symbid Chainlink

Chainlink is a drop in implementation of the Chain of Responsibility pattern. Its a very simple library that helps you avoid boiler plate code in order to provide a chain of responsibility to handle a particular task.

## Installation

Chainlink library has been split so that the Context, the class that implements the chain is isolated in this library and adapters and wrapper for popular frameworks are available in separate packages.

If you only need the chain implementation, get chainlink by running:

```sh
composer require symbid/chainlink
```

If you use Symfony or other frameworks, check [Packagist](http://pacakgist.org/vendor/symbid) for wrappers and adapters.

## Usage

To use chainlink, all you need to do is implement the `HandlerInterface` on your handlers and register them with a context.

```php
<?php
    class MyHandler implements HandlerInterface
    {
        // ... fulfill interface ...
    }
    
    $handler = new MyHandler();
    
    // Create a Context to chain responsibilities
    $context = new Symbid\Chainlink\Context();
    $context->addHandler($handler);
    
    // Pass in an item to be handled
    $context->handle($input);
    
    // You can also get the handler as a return value
    $handler = $context->getHandlerFor($input);
    
    // You may have need of returning multiple handlers
    $handler = $context->getAllHandlersFor($input);
    
```

Its the handler's responsibility to identify which input it is responsible for, the interface contains a `handles` method that is called for that.