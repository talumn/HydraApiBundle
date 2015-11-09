<?php

namespace LaFourchette\HydraApiBundle\Authentication;

use Guzzle\Common\Event;

interface AuthenticationInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param Event $event
     */
    public function onRequestBeforeSend(Event $event);

    /**
     * @param Event $event
     */
    public function onRequestError(Event $event);
}
