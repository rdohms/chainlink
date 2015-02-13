<?php

namespace Symbid\Chainlink;

use Mockery\MockInterface;

class ContextTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Context
     */
    protected $target;

    /**
     * Setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->target = new Context();
    }

    public function testAddHandler()
    {
        $handler = \Mockery::mock('Symbid\Chainlink\Handler\HandlerInterface');

        $this->target->addHandler($handler);

        $property = new \ReflectionProperty($this->target, 'handlers');
        $property->setAccessible(true);
        $registeredHandlers = $property->getValue($this->target);

        $this->assertCount(1, $registeredHandlers);

        $registeredHandlers = $property->getValue($this->target);
        $this->target->addHandler($handler);

        $this->assertCount(1, $registeredHandlers);
    }

    public function testGetHandlerFor()
    {
        $input = new \stdClass();

        $handlers = $this->buildHandlers(false, true, false, $input);

        $handler = $this->target->getHandlerFor($input);

        $this->assertEquals($handlers[1], $handler);
        $this->assertNotEquals($handlers[0], $handler);
    }

    public function testHandle()
    {
        $input = new \stdClass();
        $return = uniqid();

        $handlers = $this->buildHandlers(false, true, false, $input);
        $handlers[1]->shouldReceive('handle')->with($input)->andReturn($return);
        $handlers[0]->shouldReceive('handle')->never();
        $handlers[2]->shouldReceive('handle')->never();

        $this->assertEquals($return, $this->target->handle($input));
    }

    public function testGetAllHandlersFor()
    {
        $input = new \stdClass();

        $handlers = $this->buildHandlers(false, true, true, $input);

        $returnHandlers = $this->target->getAllHandlersFor($input);

        $this->assertCount(2, $returnHandlers);
        $this->assertContains($handlers[1], $returnHandlers);
        $this->assertContains($handlers[2], $returnHandlers);
    }

    /**
     * @throws NoHandlerException
     * @expectedException \Symbid\Chainlink\NoHandlerException
     */
    public function testGetAllHandlersForWithNoCompatibleHandler()
    {
        $input = new \stdClass();

        $handlers = $this->buildHandlers(false, false, false, $input);

        $returnHandlers = $this->target->getAllHandlersFor($input);
    }

    /**
     * @param bool $one
     * @param bool $two
     * @param bool $three
     * @param mixed $input
     * @return MockInterface []
     */
    public function buildHandlers($one, $two, $three, $input)
    {
        $handler1 = \Mockery::mock('Symbid\Chainlink\Handler\HandlerInterface');
        $handler1->shouldReceive('handles')->with($input)->andReturn($one);
        $handler2 = \Mockery::mock('Symbid\Chainlink\Handler\HandlerInterface');
        $handler2->shouldReceive('handles')->with($input)->andReturn($two);
        $handler3 = \Mockery::mock('Symbid\Chainlink\Handler\HandlerInterface');
        $handler3->shouldReceive('handles')->with($input)->andReturn($three);

        $this->target->addHandler($handler1);
        $this->target->addHandler($handler2);
        $this->target->addHandler($handler3);

        return [$handler1, $handler2, $handler3];
    }
}
