<?php

namespace LaFourchette\HydraApiBundle\Tests\Authentication;

use CommerceGuys\Guzzle\Plugin\Oauth2\Oauth2Plugin;
use Guzzle\Common\Event;
use LaFourchette\HydraApiBundle\Authentication\AbstractOauth2Authentication;
use LaFourchette\HydraApiBundle\Business\TokenBusinessInterface;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class Oauth2Authentication extends AbstractOauth2Authentication
{
    public function getName()
    {
        return 'name';
    }

    public function onRequestBeforeSend(Event $event)
    {
    }
};

class AbstractOauth2AuthenticationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TokenBusinessInterface|ObjectProphecy
     */
    private $tokenBusinessMock;

    /**
     * @var LoggerInterface|ObjectProphecy
     */
    private $loggerMock;

    /**
     * @var Oauth2Authentication
     */
    private $oauthAuthentification;

    public function setUp()
    {
        parent::setUp();

        $this->tokenBusinessMock = $this->getTokenBusinessInterfaceMock();
        $this->loggerMock = $this->getLoggerMock();

        $this->oauthAuthentification = new Oauth2Authentication(
            $this->loggerMock->reveal(),
            $this->tokenBusinessMock->reveal(),
            array('client_id' => 12345, 'client_secret' => '$3cr4t'),
            array('base_url' => 'http://erb.api/')
        );
    }

    public function testOnRequestError()
    {
        $eventMock = $this->getEventMock();

        $pluginMock = $this->getOauth2PluginMock();
        $pluginMock
            ->onRequestError($eventMock)
            ->shouldBeCalledTimes(1);

        $pluginProperty = new \ReflectionProperty($this->oauthAuthentification, 'plugin');
        $pluginProperty->setAccessible(true);
        $pluginProperty->setValue($this->oauthAuthentification, $pluginMock->reveal());

        $this->oauthAuthentification->onRequestError($eventMock->reveal());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Oauth2Plugin not created, cannot handle request error
     */
    public function testOnRequestErrorException()
    {
        $eventMock = $this->getEventMock();

        $this->oauthAuthentification->onRequestError($eventMock->reveal());
    }

    /**
     * @return ObjectProphecy|Logger
     */
    private function getLoggerMock()
    {
        return $this->prophesize('Symfony\Bridge\Monolog\Logger');
    }

    /**
     * @return ObjectProphecy|TokenBusinessInterface
     */
    private function getTokenBusinessInterfaceMock()
    {
        return $this->prophesize('LaFourchette\HydraApiBundle\Business\TokenBusinessInterface');
    }

    /**
     * @return ObjectProphecy|Oauth2Plugin
     */
    private function getOauth2PluginMock()
    {
        return $this->prophesize('CommerceGuys\Guzzle\Plugin\Oauth2\Oauth2Plugin');
    }

    /**
     * @return ObjectProphecy|Event
     */
    private function getEventMock()
    {
        return $this->prophesize('Guzzle\Common\Event');
    }
}
