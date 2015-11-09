<?php

namespace LaFourchette\HydraApiBundle\Entity;

interface TokenInterface
{
    /**
     * Convert value from OAuth to valid \DateTime.
     *
     * @param int $expiresIn
     */
    public function setExpiresIn($expiresIn);

    /**
     * @return int
     */
    public function getExpiresIn();

    /**
     * @return \DateTime
     */
    public function getExpiredAt();

    /**
     * @param \DateTime $expiredAt
     */
    public function setExpiredAt(\DateTime $expiredAt);

    /**
     * @return string
     */
    public function getRefreshToken();

    /**
     * @param string $refreshToken
     */
    public function setRefreshToken($refreshToken);

    /**
     * @return string
     */
    public function getAccessToken();

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken);
}
