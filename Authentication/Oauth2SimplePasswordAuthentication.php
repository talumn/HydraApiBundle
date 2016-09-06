<?php

namespace LaFourchette\HydraApiBundle\Authentication;

use CommerceGuys\Guzzle\Plugin\Oauth2\GrantType\PasswordCredentials;
use CommerceGuys\Guzzle\Plugin\Oauth2\Oauth2Plugin;
use Guzzle\Common\Event;
use Guzzle\Http\Client;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class Oauth2SimplePasswordAuthentication implements AuthenticationInterface
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
    protected $userCredentials;

    /**
     * @var array
     */
    protected $credentials;

    /**
     * @var array
     */
    protected $oauthToken;

    /**
     * @param LoggerInterface $logger
     * @param array           $userCredentials
     * @param array           $clientCredentials
     * @param array           $options
     */
    public function __construct(
        LoggerInterface $logger,
        array $userCredentials,
        array $clientCredentials,
        array $options = array())
    {
        $this->logger = $logger;

        $this->userCredentials = $userCredentials;
        $this->clientCredentials = $clientCredentials;
        $this->options = $options;

        $this->client = new Client($this->options['base_url'].'oauth/v2/token', $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'oauth2_simple_password';
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function onRequestError(Event $event)
    {
        throw new \Exception('Error Processing Request', 1);
    }

    /**
     * @return string|null
     */
    public function getOauthAccessToken()
    {
        return isset($this->oauthToken['access_token']) ? $this->oauthToken['access_token'] : null;
    }

    /**
     * @return string|null
     */
    public function getOauthRefreshToken()
    {
        return isset($this->oauthToken['refresh_token']) ? $this->oauthToken['refresh_token'] : null;
    }

    /**
     * @return string|null
     */
    public function getOauthExpiresIn()
    {
        return isset($this->oauthToken['expires_in']) ? $this->oauthToken['expires_in'] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function onRequestBeforeSend(Event $event)
    {
        $this->oauthToken = $this->generateToken($this->userCredentials['username'], $this->userCredentials['password']);

        $plugin = new Oauth2Plugin();
        $plugin->setAccessToken(array(
            'access_token' => $this->getOauthAccessToken(),
            'expires' => time() + $this->getOauthExpiresIn(),
        ));

        $plugin->onRequestBeforeSend($event);

        $this->logger->info('oauth2_simple_password authenticate');
    }

    /**
     * Used by @see AuthenticationListener::handle() during an interactive login.
     *
     * @param string $username
     * @param string $password
     *
     * @return array
     */
    public function generateToken($username, $password)
    {
        $credentials = new PasswordCredentials($this->client, array(
            'username' => $username,
            'password' => $password,
            'client_id' => $this->clientCredentials['client_id'],
            'client_secret' => $this->clientCredentials['client_secret'],
        ));

        return $credentials->getTokenData();
    }
}
