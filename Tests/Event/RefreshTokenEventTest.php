<?php

namespace LaFourchette\HydraApiBundle\Tests\Event;

use LaFourchette\HydraApiBundle\Event\RefreshTokenEvent;

class RefreshTokenEventTest extends \PHPUnit_Framework_TestCase
{
    public function testOffsetGet()
    {
        $event = new RefreshTokenEvent(array('foo' => 'bar'));
        $this->assertEquals('bar', $event['foo']);
    }

    public function testOffsetSet()
    {
        $event = new RefreshTokenEvent(array());
        $event['foo'] = 'bar';
        $this->assertEquals('bar', $event['foo']);
    }

    public function testOffsetExists()
    {
        $event = new RefreshTokenEvent(array('foo' => 'bar'));
        $this->assertTrue(isset($event['foo']));
    }

    public function testOffsetUnset()
    {
        $event = new RefreshTokenEvent(array('foo' => 'bar'));
        unset($event['foo']);
        $this->assertNull($event['foo']);
    }
}
