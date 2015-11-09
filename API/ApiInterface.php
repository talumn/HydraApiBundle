<?php

namespace LaFourchette\HydraApiBundle\API;

use LaFourchette\HydraApiBundle\Authentication\AuthenticationInterface;
use LaFourchette\HydraApiBundle\Client\ApiClient;

interface ApiInterface
{
    /**
     * @param ApiClient $client
     */
    public function setClient(ApiClient $client);

    /**
     * @param AuthenticationInterface $defaultAuthentication
     */
    public function setDefaultAuthentication(AuthenticationInterface $defaultAuthentication = null);
}
