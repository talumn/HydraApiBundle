<?php

namespace LaFourchette\HydraApiBundle\Authentication;

use CommerceGuys\Guzzle\Plugin\Oauth2\Oauth2Plugin;
use Guzzle\Common\Event;
use Guzzle\Http\Client;
use LaFourchette\HydraApiBundle\Business\TokenBusinessInterface;
use LaFourchette\HydraApiBundle\Entity\TokenInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

abstract class AbstractOauth2Authentication implements AuthenticationInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $credentials;

    /**
     * @var TokenBusinessInterface
     */
    protected $tokenBusiness;

    /**
     * @var TokenInterface
     */
    protected $token;

    /**
     * @var Oauth2Plugin
     */
    protected $plugin;

    /**
     * @param LoggerInterface $logger
     * @param TokenBusinessInterface   $tokenBusiness
     * @param array           $credentials
     * @param array           $options
     */
    public function __construct(
        LoggerInterface $logger,
        TokenBusinessInterface $tokenBusiness,
        array $credentials,
        array $options = array())
    {
        $this->logger = $logger;
        $this->tokenBusiness = $tokenBusiness;

        $this->credentials = $credentials;
        $this->options = $options;

        $this->client = new Client($this->options['base_url'].'oauth/v2/token', $this->options);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function onRequestError(Event $event)
    {
        if (null === $this->plugin) {
            throw new \RuntimeException('Oauth2Plugin not created, cannot handle request error');
        }
        $this->plugin->onRequestError($event);
    }
}
