<?php

namespace LaFourchette\HydraApiBundle\Tests\API;

use LaFourchette\HydraApiBundle\API\AbstractHydraResourceApi;

class TestHydraResourceApi extends AbstractHydraResourceApi
{
    /**
     * @param string $pluralResourceName
     */
    public function __construct($pluralResourceName)
    {
        $this->pluralResourceName = $pluralResourceName;
    }
}

class AbstractHydraResourceApiTest extends \PHPUnit_Framework_TestCase
{
    public function testGetResourceURI()
    {
        $api = new TestHydraResourceApi('tests');
        $result = $api->getResourceURI(1);
        $this->assertEquals('/tests/1', $result);
    }
}
