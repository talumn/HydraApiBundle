<?php

namespace LaFourchette\HydraApiBundle\Client;

use Guzzle\Common\Event;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use LaFourchette\HydraApiBundle\Authentication\AuthenticationInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Log\NullLogger;

class ApiClient extends Client
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AuthenticationInterface|null
     */
    private $authentication;

    /**
     * @var string
     */
    private $apiName;

    /**
     * @param EventDispatcher $dispatcher
     * @param LoggerInterface $logger
     * @param array           $options
     */
    public function __construct(EventDispatcher $dispatcher, LoggerInterface $logger = null, array $options = array())
    {
        parent::__construct(rtrim($options['base_url'], '/'), $options);

        $this->apiName = empty($options['name']) ? 'HydraApiUnknow' : $options['name'];
        $this->logger = $logger ?: new NullLogger();

        $this->setEventDispatcher($dispatcher);
        $this->getEventDispatcher()->addListener('client.create_request', array($this, 'logRequest'));
        $this->getEventDispatcher()->addListener('request.before_send', array($this, 'onRequestBeforeSend'));
        $this->getEventDispatcher()->addListener('request.error', array($this, 'onRequestError'));
    }

    /**
     * @param AuthenticationInterface|null $authentication
     *
     * @return ApiClient
     */
    public function setAuthentication(AuthenticationInterface $authentication = null)
    {
        $this->authentication = $authentication;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiName()
    {
        return $this->apiName;
    }

    /**
     * @param Event $event
     */
    public function logRequest(Event $event)
    {
        /** @var Request $request */
        $request = $event['request'];

        $this->logger->info(
            sprintf('[%s] request call', $this->apiName),
            array(
                'method' => $request->getMethod(),
                'url' => $request->getUrl(),
                'authentication' => $this->authentication ? $this->authentication->getName() : null,
            )
        );
    }

    /**
     * @param Event $event
     */
    public function onRequestBeforeSend(Event $event)
    {
        if (null !== $this->authentication) {
            $this->authentication->onRequestBeforeSend($event);
        }
    }

    /**
     * @param Event $event
     */
    public function onRequestError(Event $event)
    {
        if (null !== $this->authentication) {
            $this->authentication->onRequestError($event);
        }

        /** @var Request $request */
        $request = $event['request'];

        /** @var Response $response */
        $response = $event['response'];

        $this->logger->err(
            sprintf('[%s] request error', $this->apiName),
            array(
                'method' => $request->getMethod(),
                'url' => $request->getUrl(),
                'authentication' => $this->authentication ? $this->authentication->getName() : null,
                'response' => $response->getBody(true),
            )
        );
    }
}
