<?php

namespace LaFourchette\HydraApiBundle\Authentication;

use CommerceGuys\Guzzle\Plugin\Oauth2\GrantType\PasswordCredentials;
use CommerceGuys\Guzzle\Plugin\Oauth2\Oauth2Plugin;
use Guzzle\Common\Event;
use LaFourchette\HydraApiBundle\Business\TokenBusinessInterface;
use LaFourchette\HydraApiBundle\Entity\TokenInterface;
use LaFourchette\HydraApiBundle\Event\RefreshTokenEvent;
use LaFourchette\HydraApiBundle\GrantType\RefreshToken;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContext;

class Oauth2PasswordAuthentication extends AbstractOauth2Authentication
{
    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @var mixed
     */
    private $user;

    /**
     * @param LoggerInterface        $logger
     * @param TokenBusinessInterface $tokenBusiness
     * @param SecurityContext        $securityContext
     * @param array                  $credentials
     * @param array                  $options
     */
    public function __construct(
        LoggerInterface $logger,
        TokenBusinessInterface $tokenBusiness,
        SecurityContext $securityContext,
        array $credentials,
        array $options = array())
    {
        parent::__construct($logger, $tokenBusiness, $credentials, $options);

        $this->securityContext = $securityContext;

        $this->client->getEventDispatcher()->addListener(
            RefreshToken::GET_TOKEN_DATA_EVENT, array($this, 'updateUserOAuth2Token')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'oauth2_password';
    }

    /**
     * {@inheritdoc}
     */
    public function onRequestBeforeSend(Event $event)
    {
        if (null === $this->plugin) {
            $token = $this->getUserOAuth2Token();

            $refreshToken = new RefreshToken($this->client, array(
                'client_id' => $this->credentials['client_id'],
                'client_secret' => $this->credentials['client_secret'],
                'refresh_token' => $token->getRefreshToken(),
            ), $this->client->getEventDispatcher());

            $this->plugin = new Oauth2Plugin(null, $refreshToken);
            $this->plugin->setAccessToken(array(
                'access_token' => $token->getAccessToken(),
                'expires' => $token->getExpiredAt()->getTimestamp(),
            ));
        }

        $this->plugin->onRequestBeforeSend($event);

        $this->logger->info('oauth2_password authenticate', array(
            'user' => $this->getUser(),
        ));
    }

    /**
     * @param RefreshTokenEvent $event
     */
    public function updateUserOAuth2Token(RefreshTokenEvent $event)
    {
        $this->token = $this->tokenBusiness->updateUserToken(
            $this->getUser(),
            $event['access_token'],
            $event['refresh_token'],
            $event['expires_in']
        );
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
            'client_id' => $this->credentials['client_id'],
            'client_secret' => $this->credentials['client_secret'],
        ));

        return $credentials->getTokenData();
    }

    /**
     * @return TokenInterface
     *
     * @throws \RuntimeException
     */
    private function getUserOAuth2Token()
    {
        if (!$this->token && !$this->token = $this->tokenBusiness->getTokenByUser($this->getUser())) {
            // token is supposed to be generated in AuthenticationListener on security login
            throw new \RuntimeException('Unable to find user token');
        }

        return $this->token;
    }

    /**
     * @return mixed
     *
     * @throws \RuntimeException
     */
    private function getUser()
    {
        if (!$this->user) {
            if (!$this->securityContext->getToken() || !is_object($this->securityContext->getToken()->getUser())) {
                throw new \RuntimeException('Unable to retrieve user');
            }
            $this->user = $this->securityContext->getToken()->getUser();
        }

        return $this->user;
    }
}
