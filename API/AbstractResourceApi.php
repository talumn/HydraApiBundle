<?php

namespace LaFourchette\HydraApiBundle\API;

use Guzzle\Http\Message\Response;
use LaFourchette\HydraApiBundle\Authentication\AuthenticationInterface;
use LaFourchette\HydraApiBundle\Client\ApiClient;
use LaFourchette\HydraApiBundle\Exception\ApiException;

abstract class AbstractResourceApi implements ApiInterface
{
    /**
     * @var ApiClient
     */
    protected $client;

    /**
     * @var AuthenticationInterface
     */
    protected $defaultAuthentication;

    /**
     * @var string
     */
    protected $defaultRequestContentType = 'application/json';

    /**
     * {@inheritdoc}
     */
    public function setClient(ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param AuthenticationInterface $defaultAuthentication
     */
    public function setDefaultAuthentication(AuthenticationInterface $defaultAuthentication = null)
    {
        $this->defaultAuthentication = $defaultAuthentication;
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    protected function transformUri($uri)
    {
        return $uri;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    protected function transformRequest(array $data)
    {
        return json_encode($data);
    }

    /**
     * @param Response $response
     *
     * @return array
     */
    protected function transformResponse(Response $response)
    {
        return $response->json();
    }

    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'authentication' => $this->defaultAuthentication,
            'requestContentType' => $this->defaultRequestContentType,
        );
    }

    /**
     * @param AuthenticationInterface|null $authentication
     *
     * @return ApiClient
     */
    protected function getAuthenticatedClient(AuthenticationInterface $authentication = null)
    {
        return empty($authentication) ? $this->client : $this->client->setAuthentication($authentication);
    }

    /**
     * @param string $uri
     * @param array  $options
     *
     * @return array
     *
     * @throws ApiException
     */
    protected function get($uri, array $options = array())
    {
        $defaults = $this->getDefaultOptions();
        $options += $defaults;

        $response = $this->getAuthenticatedClient($options['authentication'])
            ->get($this->transformUri($uri))
            ->send();

        return $this->transformResponse($response);
    }

    /**
     * @param string $uri
     * @param array  $data
     * @param array  $options
     *
     * @return array
     *
     * @throws ApiException
     */
    protected function create($uri, array $data, array $options = array())
    {
        $defaults = $this->getDefaultOptions();
        $options += $defaults;

        $response = $this->getAuthenticatedClient($options['authentication'])
            ->post(
                $uri,
                array('Content-Type' => $options['requestContentType']),
                $this->transformRequest($data))
            ->send();

        return $this->transformResponse($response);
    }

    /**
     * @param string $uri
     * @param array  $data
     * @param array  $options
     *
     * @return array
     */
    protected function update($uri, array $data, array $options = array())
    {
        $defaults = $this->getDefaultOptions();
        $options += $defaults;

        $response = $this->getAuthenticatedClient($options['authentication'])
            ->put(
                $this->transformUri($uri),
                array('Content-Type' => $options['requestContentType']),
                $this->transformRequest($data))
            ->send();

        return $this->transformResponse($response);
    }

    /**
     * @param string $uri
     * @param array  $options
     *
     * @return bool
     */
    protected function delete($uri, array $options = array())
    {
        $defaults = $this->getDefaultOptions();
        $options += $defaults;

        return $this->getAuthenticatedClient($options['authentication'])
            ->delete($this->transformUri($uri))
            ->send()
            ->isSuccessful();
    }
}
