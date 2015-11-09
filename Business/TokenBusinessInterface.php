<?php

namespace LaFourchette\HydraApiBundle\Business;

use LaFourchette\HydraApiBundle\Entity\TokenInterface;

interface TokenBusinessInterface
{
    /**
     * @return TokenInterface|null
     */
    public function getClientToken();

    /**
     * @param string $accessToken
     * @param string $refreshToken
     * @param int    $expiresIn
     *
     * @return TokenInterface
     *
     * @throws \Exception
     */
    public function updateClientToken($accessToken, $refreshToken, $expiresIn);

    /**
     * @param mixed $user
     *
     * @return null|TokenInterface
     */
    public function getTokenByUser($user);

    /**
     * @param mixed  $user
     * @param string $accessToken
     * @param string $refreshToken
     * @param int    $expiresIn
     *
     * @return TokenInterface
     */
    public function updateUserToken($user, $accessToken, $refreshToken, $expiresIn);
}
