<?php

namespace LaFourchette\HydraApiBundle\Authentication;

use CommerceGuys\Guzzle\Plugin\Oauth2\GrantType\ClientCredentials;
use CommerceGuys\Guzzle\Plugin\Oauth2\Oauth2Plugin;
use Guzzle\Common\Event;
use LaFourchette\HydraApiBundle\Business\TokenBusinessInterface;
use LaFourchette\HydraApiBundle\Entity\TokenInterface;
use LaFourchette\HydraApiBundle\Event\RefreshTokenEvent;
use LaFourchette\HydraApiBundle\GrantType\RefreshToken;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class Oauth2ClientAuthentication extends AbstractOauth2Authentication
{
    /**
     * @param LoggerInterface        $logger
     * @param TokenBusinessInterface $tokenBusiness
     * @param array                  $credentials
     * @param array                  $options
     */
    public function __construct(
        LoggerInterface $logger,
        TokenBusinessInterface $tokenBusiness,
        array $credentials,
        array $options = array())
    {
        parent::__construct($logger, $tokenBusiness, $credentials, $options);

        $this->client->getEventDispatcher()->addListener(
            RefreshToken::GET_TOKEN_DATA_EVENT, array($this, 'updateOAuth2Token')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'oauth2_client';
    }

    /**
     * {@inheritdoc}
     */
    public function onRequestBeforeSend(Event $event)
    {
        if (null === $this->plugin) {
            $token = $this->getClientToken();

            $this->plugin = new Oauth2Plugin(
                new ClientCredentials($this->client, $this->credentials)
            );

            if (!empty($token)) {
                $this->plugin->setAccessToken(array(
                    'access_token' => $token->getAccessToken(),
                    'expires' => $token->getExpiredAt()->getTimestamp(),
                ));
            }
        }

        $this->plugin->onRequestBeforeSend($event);
    }

    /**
     * @return TokenInterface
     */
    private function getClientToken()
    {
        if (empty($this->token)) {
            $this->token = $this->tokenBusiness->getClientToken();
        }

        return $this->token;
    }

    /**
     * @param RefreshTokenEvent $event
     */
    public function updateOAuth2Token(RefreshTokenEvent $event)
    {
        $this->token = $this->tokenBusiness->updateClientToken(
            $event['access_token'],
            $event['refresh_token'],
            $event['expires_in']
        );
    }
}
