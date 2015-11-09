<?php

namespace LaFourchette\HydraApiBundle\Entity;

class Token implements TokenInterface
{
    /**
     * @var \DateTime
     */
    private $expiredAt;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * {@inheritdoc}
     */
    public function setExpiresIn($expiresIn)
    {
        $this->setExpiredAt(new \DateTime('now + '.$expiresIn.' seconds'));
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiresIn()
    {
        return $this->getExpiredAt()->getTimestamp() - time();
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiredAt()
    {
        return $this->expiredAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiredAt(\DateTime $expiredAt)
    {
        $this->expiredAt = $expiredAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * {@inheritdoc}
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * {@inheritdoc}
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }
}
