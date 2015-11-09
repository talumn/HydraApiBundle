<?php

namespace LaFourchette\HydraApiBundle\Tests\Authentication;

use Guzzle\Common\Event;
use Guzzle\Http\Message\Request;
use LaFourchette\HydraApiBundle\Authentication\Oauth2ClientAuthentication;
use LaFourchette\HydraApiBundle\Business\TokenBusinessInterface;
use LaFourchette\HydraApiBundle\Entity\TokenInterface;
use LaFourchette\HydraApiBundle\Event\RefreshTokenEvent;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class Oauth2ClientAuthenticationTest extends \PHPUnit_Framework_TestCase
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
     * @var Oauth2ClientAuthentication
     */
    private $oauth2ClientAuthentication;

    public function setUp()
    {
        parent::setUp();

        $this->tokenBusinessMock = $this->getTokenBusinessInterfaceMock();
        $this->loggerMock = $this->getLoggerMock();

        $this->oauth2ClientAuthentication = new Oauth2ClientAuthentication(
            $this->loggerMock->reveal(),
            $this->tokenBusinessMock->reveal(),
            array('client_id' => 12345, 'client_secret' => '$3cr4t'),
            array('base_url' => 'http://erb.api/')
        );
    }

    public function testOnRequestBeforeSend()
    {
        $expires = time() + 999999;

        $tokenMock = $this->getTokenMock();
        $tokenMock
            ->getAccessToken()
            ->willReturn('access_token_value')
            ->shouldBeCalledTimes(1);

        /* @var \DateTime $dateTime */
        $dateTimeMock = $this->prophesize('\DateTime');
        $dateTimeMock
            ->getTimestamp()
            ->willReturn($expires)
            ->shouldBeCalledTimes(1);

        $tokenMock
            ->getExpiredAt()
            ->willReturn($dateTimeMock)
            ->shouldBeCalledTimes(1);

        $this->tokenBusinessMock
            ->getClientToken()
            ->willReturn($tokenMock)
            ->shouldBeCalledTimes(1);

        $requestMock = $this->getRequestMock();
        $requestMock
            ->setHeader('Authorization', 'Bearer access_token_value')
            ->shouldBeCalledTimes(1);

        $eventMock = $this->getEventMock();

        $eventMock
            ->offsetGet('request')
            ->willReturn($requestMock)
            ->shouldBeCalledTimes(1);

        $this->oauth2ClientAuthentication->onRequestBeforeSend($eventMock->reveal());
    }

    public function testUpdateUserOAuth2Token()
    {
        $tokenMock = $this->getTokenMock();

        $this->tokenBusinessMock
            ->updateClientToken(
                'access_token_value',
                'refresh_token_value',
                99
            )
            ->willReturn($tokenMock)
            ->shouldBeCalledTimes(1);

        $refreshTokenEventMock = $this->getRefreshTokenEventMock();
        $refreshTokenEventMock
            ->offsetGet('access_token')
            ->willReturn('access_token_value')
            ->shouldBeCalledTimes(1);

        $refreshTokenEventMock
            ->offsetGet('refresh_token')
            ->willReturn('refresh_token_value')
            ->shouldBeCalledTimes(1);

        $refreshTokenEventMock
            ->offsetGet('expires_in')
            ->willReturn(99)
            ->shouldBeCalledTimes(1);

        $this->oauth2ClientAuthentication->updateOAuth2Token($refreshTokenEventMock->reveal());
    }

    public function testGetName()
    {
        $this->assertEquals('oauth2_client', $this->oauth2ClientAuthentication->getName());
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
     * @return ObjectProphecy|Request
     */
    private function getRequestMock()
    {
        return $this->prophesize('Guzzle\Http\Message\Request');
    }

    /**
     * @return TokenInterface|ObjectProphecy
     */
    private function getTokenMock()
    {
        return $this->prophesize('LaFourchette\HydraApiBundle\Entity\TokenInterface');
    }

    /**
     * @return Event|ObjectProphecy
     */
    private function getEventMock()
    {
        return $this->prophesize('Guzzle\Common\Event');
    }

    /**
     * @return RefreshTokenEvent|ObjectProphecy
     */
    private function getRefreshTokenEventMock()
    {
        return $this->prophesize('LaFourchette\HydraApiBundle\Event\RefreshTokenEvent');
    }
}
