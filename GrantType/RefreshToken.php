<?php

namespace LaFourchette\HydraApiBundle\GrantType;

use CommerceGuys\Guzzle\Plugin\Oauth2\GrantType\RefreshToken as BaseToken;
use Guzzle\Http\ClientInterface;
use LaFourchette\HydraApiBundle\Event\RefreshTokenEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class RefreshToken extends BaseToken
{
    const GET_TOKEN_DATA_EVENT = 'refresh.get_token_data';

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @param ClientInterface $client
     * @param array           $config
     * @param EventDispatcher $dispatcher
     */
    public function __construct(ClientInterface $client, array $config, EventDispatcher $dispatcher)
    {
        parent::__construct($client, $config);
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenData($refreshToken = null)
    {
        $postBody = array(
            'grant_type' => 'refresh_token',
            // If no refresh token was provided to the method, use the one
            // provided to the constructor.
            'refresh_token' => $refreshToken ?: $this->config['refresh_token'],
        );
        if ($this->config['scope']) {
            $postBody['scope'] = $this->config['scope'];
        }
        $request = $this->client->post('oauth/v2/token', array(), $postBody);
        $request->setAuth($this->config['client_id'], $this->config['client_secret']);
        $response = $request->send();
        $data = $response->json();

        $requiredData = array_flip(array('access_token', 'expires_in', 'refresh_token'));
        $tokenData = array_intersect_key($data, $requiredData);

        $this->dispatcher->dispatch(self::GET_TOKEN_DATA_EVENT, new RefreshTokenEvent($tokenData));

        return $tokenData;
    }
}
