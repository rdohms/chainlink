# DMS Chainlink

[![Latest Version](https://img.shields.io/github/release/rdohms/chainlink.svg?style=flat-square)](https://github.com/rdohms/chainlink/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/rdohms/chainlink/master.svg?style=flat-square)](https://travis-ci.org/rdohms/chainlink)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/rdohms/chainlink.svg?style=flat-square)](https://scrutinizer-ci.com/g/rdohms/chainlink/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/rdohms/chainlink.svg?style=flat-square)](https://scrutinizer-ci.com/g/rdohms/chainlink)
[![Total Downloads](https://img.shields.io/packagist/dt/rdohms/chainlink.svg?style=flat-square)](https://packagist.org/packages/rdohms/chainlink)

Chainlink is a drop in implementation of the Chain of Responsibility pattern. Its a very simple library that helps you avoid boiler plate code in order to provide a chain of responsibility to handle a particular task.

## Installation

Chainlink library has been split so that the Context, the class that implements the chain is isolated in this library and adapters and wrapper for popular frameworks are available in separate packages.

If you only need the chain implementation, get chainlink by running:

```sh
composer require dms/chainlink
```

If you use Symfony or other frameworks, check [Packagist](http://packagist.org/packages/dms/) for wrappers and adapters.

## Usage

To use chainlink, all you need to do is implement the `HandlerInterface` on your handlers and register them with a context.

```php
class MyHandler implements HandlerInterface
{
    // ... fulfill interface ...
}

$handler = new MyHandler();

// Create a Context to chain responsibilities
$context = new DMS\Chainlink\Context();
$context->addHandler($handler);

// Pass in an item to be handled
$context->handle($input);

// You can also get the handler as a return value
$handler = $context->getHandlerFor($input);

// You may have need of returning multiple handlers
$handler = $context->getAllHandlersFor($input);
```

Its the handler's responsibility to identify which input it is responsible for, the interface contains a `handles` method that is called for that.

## Order of Chain handling

Sometimes it's useful to influence which handler gets called first. `addHandler` supports an optional second parameter with a priority integer. The highest number in the chain will be called first.

```php
// Create a Context to chain responsibilities
$context = new DMS\Chainlink\Context();
$context->addHandler($handler1, 10);
$context->addHandler($handler2, 1000);
$context->addHandler($handler3);

// Pass in an item to be handled
$context->handle($input);
```

The following handlers will be called in order (provided they can handle the usecase) `$handler2 -> $handler1 -> $handler3`
