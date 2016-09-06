<?php

namespace LaFourchette\HydraApiBundle\Tests\Authentication;

use CommerceGuys\Guzzle\Plugin\Oauth2\GrantType\PasswordCredentials;
use CommerceGuys\Guzzle\Plugin\Oauth2\Oauth2Plugin;
use Guzzle\Common\Event;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use LaFourchette\HydraApiBundle\Authentication\Oauth2SimplePasswordAuthentication;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class Oauth2SimplePasswordAuthenticationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LoggerInterface|ObjectProphecy
     */
    private $loggerMock;

    /**
     * @var Oauth2Plugin|ObjectProphecy
     */
    private $oauth2pluginMock;

    /**
     * @var PasswordCredentials|ObjectProphecy
     */
    private $passwordCrendentialsMock;

    /**
     * @var oauth2SimplePasswordAuthentication
     */
    private $oauth2SimplePasswordAuthentication;

    public function setUp()
    {
        parent::setUp();

        $this->loggerMock = $this->getLoggerMock();

        $this->oauth2SimplePasswordAuthentication = new Oauth2SimplePasswordAuthentication(
            $this->loggerMock->reveal(),
            array('username' => 'hello', 'password' => 'world'),
            array('client_id' => 12345, 'client_secret' => '$3cr4t'),
            array('base_url' => 'http://erb.api/oauth/')
        );
    }

    public function testOnRequestBeforeSend()
    {
        $this->loggerMock->info('oauth2_simple_password authenticate')->shouldBeCalledTimes(1);

        $expires = time() + 999999;
        $responseMock = $this->getResponseMock();
        $responseMock
            ->json()
            ->shouldBeCalledTimes(1)
            ->willReturn(array(
                'access_token' => 'access_token_value',
                'refresh_token' => 'refresh_token_value',
                'expires_in' => $expires,
            ));

        $requestMockOauth = $this->getRequestMock();
        $requestMockOauth->setAuth(12345, '$3cr4t')->shouldBeCalledTimes(1);
        $requestMockOauth->send()->willReturn($responseMock->reveal())->shouldBeCalledTimes(1);

        $requestMock = $this->getRequestMock();
        $requestMock->setHeader('Authorization', 'Bearer access_token_value')->shouldBeCalledTimes(1);

        $eventMock = $this->getEventMock();
        $eventMock->offsetGet('request')->willReturn($requestMock)->shouldBeCalledTimes(1);

        $clientMock = $this->getClientMock();
        $clientMock->post(
                null,
                array(),
                array(
                    'grant_type' => 'password',
                    'username' => 'hello',
                    'password' => 'world',
                )
            )
            ->willReturn($requestMockOauth->reveal())
            ->shouldBeCalledTimes(1);

        $reflection = new \ReflectionProperty(get_class($this->oauth2SimplePasswordAuthentication), 'client');
        $reflection->setAccessible(true);
        $reflection->setValue($this->oauth2SimplePasswordAuthentication, $clientMock->reveal());

        $this->oauth2SimplePasswordAuthentication->onRequestBeforeSend($eventMock->reveal());

        $this->assertEquals($this->oauth2SimplePasswordAuthentication->getOauthAccessToken(), 'access_token_value');
        $this->assertEquals($this->oauth2SimplePasswordAuthentication->getOauthRefreshToken(), 'refresh_token_value');
        $this->assertEquals($this->oauth2SimplePasswordAuthentication->getOauthExpiresIn(), $expires);
    }

    public function testGenerateToken()
    {
        $responseMock = $this->getResponseMock();
        $responseMock
            ->json()
            ->shouldBeCalledTimes(1)
            ->willReturn(array(
                'access_token' => 'access_token_value',
                'refresh_token' => 'refresh_token_value',
                'expires_in' => 99,
            ));

        $requestMock = $this->getRequestMock();
        $requestMock
            ->setAuth(12345, '$3cr4t')
            ->shouldBeCalledTimes(1);

        $requestMock
            ->send()
            ->willReturn($responseMock->reveal())
            ->shouldBeCalledTimes(1);

        $clientMock = $this->getClientMock();
        $clientMock->post(
                null,
                array(),
                array(
                    'grant_type' => 'password',
                    'username' => 'hello',
                    'password' => 'world',
                )
            )
            ->willReturn($requestMock->reveal())
            ->shouldBeCalledTimes(1);

        $reflection = new \ReflectionProperty(get_class($this->oauth2SimplePasswordAuthentication), 'client');
        $reflection->setAccessible(true);
        $reflection->setValue($this->oauth2SimplePasswordAuthentication, $clientMock->reveal());

        $result = $this->oauth2SimplePasswordAuthentication->generateToken('hello', 'world');
        $expect = array(
            'access_token' => 'access_token_value',
            'expires_in' => 99,
            'refresh_token' => 'refresh_token_value',
        );

        $this->assertEquals($expect, $result);
    }

    public function testGetName()
    {
        $this->assertEquals('oauth2_simple_password', $this->oauth2SimplePasswordAuthentication->getName());
    }

    /**
     * @return ObjectProphecy|Logger
     */
    private function getLoggerMock()
    {
        return $this->prophesize('Symfony\Bridge\Monolog\Logger');
    }

    /**
     * @return ObjectProphecy|Client
     */
    private function getClientMock()
    {
        return $this->prophesize('Guzzle\Http\Client');
    }

    /**
     * @return ObjectProphecy|Response
     */
    private function getResponseMock()
    {
        return $this->prophesize('Guzzle\Http\Message\Response');
    }

    /**
     * @return ObjectProphecy|Request
     */
    private function getRequestMock()
    {
        return $this->prophesize('Guzzle\Http\Message\Request');
    }

    /**
     * @return Event|ObjectProphecy
     */
    private function getEventMock()
    {
        return $this->prophesize('Guzzle\Common\Event');
    }
}
