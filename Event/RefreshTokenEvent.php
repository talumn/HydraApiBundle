<?php

namespace LaFourchette\HydraApiBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class RefreshTokenEvent extends Event implements \ArrayAccess
{
    /**
     * @var array
     */
    private $tokenData;

    /**
     * @param array $tokenData
     */
    public function __construct(array $tokenData = array())
    {
        $this->tokenData = $tokenData;
    }

    /**
     * @param string $offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->tokenData[$offset]) ? $this->tokenData[$offset] : null;
    }

    /**
     * @param string $offset
     * @param string $value
     */
    public function offsetSet($offset, $value)
    {
        $this->tokenData[$offset] = $value;
    }

    /**
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->tokenData[$offset]);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->tokenData[$offset]);
    }
}
