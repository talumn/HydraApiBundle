<?php

namespace LaFourchette\HydraApiBundle\API;

abstract class AbstractHydraResourceApi extends AbstractResourceApi
{
    /**
     * @var string
     */
    protected $pluralResourceName;

    /**
     * @param mixed $input
     *
     * @return string
     */
    public function getResourceURI($input)
    {
        return $this->pluralResourceName.'/'.$input;
    }

    /**
     * @param $uri
     * @param array $options
     *
     * @return array
     */
    protected function getCall($uri, array $options = array())
    {
        return $this->get($uri, $options);
    }

    /**
     * @param $uri
     * @param array $data
     * @param array $options
     *
     * @return array
     */
    protected function createCall($uri, array $data, array $options = array())
    {
        return $this->create($uri, $data, $options);
    }

    /**
     * @param $uri
     * @param array $data
     * @param array $options
     *
     * @return array
     */
    protected function updateCall($uri, array $data, array $options = array())
    {
        return $this->update($uri, $data, $options);
    }

    /**
     * @param $uri
     * @param array $options
     *
     * @return array
     */
    protected function deleteCall($uri, array $options = array())
    {
        return $this->delete($uri, $options);
    }
}
