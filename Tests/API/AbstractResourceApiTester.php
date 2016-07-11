<?php

namespace LaFourchette\HydraApiBundle\Tests\API;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use LaFourchette\HydraApiBundle\Authentication\AuthenticationInterface;
use LaFourchette\HydraApiBundle\Client\ApiClient;
use Prophecy\Prophecy\ObjectProphecy;

abstract class AbstractResourceApiTester extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuthenticationInterface|ObjectProphecy
     */
    protected $authentificationMock;

    /**
     * @var ApiClient|ObjectProphecy
     */
    protected $clientMock;

    /**
     * @var TestResourceApi|ObjectProphecy
     */
    protected $api;

    public function setUp()
    {
        parent::setUp();

        $this->authentificationMock =
            $this->prophesize('LaFourchette\HydraApiBundle\Authentication\AuthenticationInterface');

        $this->clientMock = $this->getApiClientMock();

        $this->api = $this->createResourceApi();
        $this->api->setDefaultAuthentication($this->authentificationMock->reveal());
        $this->api->setClient($this->clientMock->reveal());
    }

    abstract public function createResourceApi();

    /**
     * @param string                       $uri
     * @param array                        $result
     * @param AuthenticationInterface|null $authentification
     */
    protected function getTest($uri, $result, $authentification)
    {
        $clientMock = $this->getClientWithAuthenticationMock($authentification);
        $requestMock = $this->getRequestMock();
        $responseMock = $this->getResponseMock();

        $clientMock
            ->get($uri)
            ->shouldBeCalledTimes(1)
            ->willReturn($requestMock);

        $requestMock
            ->send()
            ->shouldBeCalledTimes(1)
            ->willReturn($responseMock);

        $responseMock
            ->json()
            ->shouldBeCalledTimes(1)
            ->willReturn($result);
    }

    /**
     * @param string                       $uri
     * @param array                        $data
     * @param array                        $requestHeaders
     * @param array                        $result
     * @param AuthenticationInterface|null $authentification
     */
    protected function updateTest($uri, $data, $requestHeaders, $result, $authentification)
    {
        $clientMock = $this->getClientWithAuthenticationMock($authentification);
        $requestMock = $this->getRequestMock();
        $responseMock = $this->getResponseMock();

        $encodedData = json_encode($data);

        $clientMock
            ->put($uri, $requestHeaders, $encodedData)
            ->shouldBeCalledTimes(1)
            ->willReturn($requestMock);

        $requestMock
            ->send()
            ->shouldBeCalledTimes(1)
            ->willReturn($responseMock);

        $responseMock
            ->json()
            ->shouldBeCalledTimes(1)
            ->willReturn($result);
    }

    /**
     * @param string                       $uri
     * @param array                        $data
     * @param array                        $requestHeaders
     * @param array                        $result
     * @param AuthenticationInterface|null $authentification
     */
    protected function createTest($uri, $data, $requestHeaders, $result, $authentification)
    {
        $clientMock = $this->getClientWithAuthenticationMock($authentification);
        $requestMock = $this->getRequestMock();
        $responseMock = $this->getResponseMock();

        $encodedData = json_encode($data);

        $clientMock
            ->post($uri, $requestHeaders, $encodedData)
            ->shouldBeCalledTimes(1)
            ->willReturn($requestMock);

        $requestMock
            ->send()
            ->shouldBeCalledTimes(1)
            ->willReturn($responseMock);

        $responseMock
            ->json()
            ->shouldBeCalledTimes(1)
            ->willReturn($result);
    }

    /**
     * @param string                       $uri
     * @param AuthenticationInterface|null $authentification
     * @param bool|true                    $result
     */
    protected function deleteTest($uri, $authentification, $result = true)
    {
        $clientMock = $this->getClientWithAuthenticationMock($authentification);
        $requestMock = $this->getRequestMock();
        $responseMock = $this->getResponseMock();

        $clientMock
            ->delete($uri)
            ->shouldBeCalledTimes(1)
            ->willReturn($requestMock);

        $requestMock
            ->send()
            ->shouldBeCalledTimes(1)
            ->willReturn($responseMock);

        $responseMock
            ->isSuccessful()
            ->shouldBeCalledTimes(1)
            ->willReturn($result);
    }

    /**
     * @param AuthenticationInterface|null $authentication
     *
     * @return ObjectProphecy|ApiClient
     */
    protected function getClientWithAuthenticationMock($authentication = null)
    {
        if ($authentication) {
            $this->clientMock
                ->setAuthentication($authentication)
                ->willReturn($this->clientMock)
                ->shouldBeCalledTimes(1);
        }

        return $this->clientMock;
    }

    /**
     * @return ObjectProphecy|ApiClient
     */
    protected function getApiClientMock()
    {
        return $this->prophesize('LaFourchette\HydraApiBundle\Client\ApiClient');
    }

    /**
     * @return ObjectProphecy|RequestInterface
     */
    protected function getRequestMock()
    {
        return $this->prophesize('Guzzle\Http\Message\RequestInterface');
    }

    /**
     * @return ObjectProphecy|Response
     */
    protected function getResponseMock()
    {
        return $this->prophesize('Guzzle\Http\Message\Response');
    }
}
