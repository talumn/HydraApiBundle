<?php

namespace LaFourchette\HydraApiBundle\Tests\GrantType;

use LaFourchette\HydraApiBundle\GrantType\RefreshToken;
use Prophecy\Argument;

class RefreshTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTokenData()
    {
        $token = new RefreshToken($this->getClientMock()->reveal(), array(
            'client_id' => 12345,
            'client_secret' => '$3cr4t',
        ), $this->getEventDispatcherMock()->reveal());
        $this->assertEquals(array(
            'access_token' => 'access',
            'expires_in' => 67890,
            'refresh_token' => 'refresh',
        ), $token->getTokenData());
    }

    private function getClientMock()
    {
        $requestMock = $this->getRequestMock();
        $requestMock->setAuth(12345, '$3cr4t')->shouldBeCalledTimes(1);
        $requestMock->send()->willReturn($this->getResponseMock()->reveal())->shouldBeCalledTimes(1);

        $prophecy = $this->prophesize('Guzzle\Http\Client');
        $prophecy->post('oauth/v2/token', array(), array(
            'grant_type' => 'refresh_token',
            'refresh_token' => '',
        ))->willReturn($requestMock->reveal())->shouldBeCalledTimes(1);

        return $prophecy;
    }

    private function getRequestMock()
    {
        return $this->prophesize('Guzzle\Http\Message\Request');
    }

    private function getResponseMock()
    {
        $prophecy = $this->prophesize('Guzzle\Http\Message\Response');
        $prophecy->json()->willReturn(array(
            'access_token' => 'access',
            'refresh_token' => 'refresh',
            'expires_in' => 67890,
        ))->shouldBeCalledTimes(1);

        return $prophecy;
    }

    private function getEventDispatcherMock()
    {
        $prophecy = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcher');
        $prophecy->dispatch(RefreshToken::GET_TOKEN_DATA_EVENT, Argument::type('object'))->shouldBeCalledTimes(1);

        return $prophecy;
    }
}
