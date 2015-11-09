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
        return '/'.$this->pluralResourceName.'/'.$input;
    }
}
