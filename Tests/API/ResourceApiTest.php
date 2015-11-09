<?php

namespace LaFourchette\HydraApiBundle\Tests\API;

use LaFourchette\HydraApiBundle\API\AbstractResourceApi;

class TestResourceApi extends AbstractResourceApi
{
    public function get($uri, array $options = array())
    {
        return $this->_get($uri, $options);
    }

    public function create($uri, array $data, array $options = array())
    {
        return $this->_create($uri, $data, $options);
    }

    public function update($uri, array $data, array $options = array())
    {
        return $this->_update($uri, $data, $options);
    }

    public function delete($uri, array $options = array())
    {
        return $this->_delete($uri, $options);
    }
}

class ResourceApiTest extends AbstractResourceApiTester
{
    public function createResourceApi()
    {
        return new TestResourceApi();
    }

    public function testGet()
    {
        $uri = '/resource/1';
        $result = array('property' => 'value');
        $this->_getTest($uri, $result, $this->authentificationMock);

        $response = $this->api->get($uri);
        $this->assertEquals($result, $response);
    }

    public function testUpdate()
    {
        $uri = '/resource/1';
        $data = array('property' => 'value');
        $requestHeaders = array('Content-Type' => 'application/json');
        $result = '{"resource" : 1}';

        $this->_updateTest($uri, $data, $requestHeaders, $result, $this->authentificationMock);

        $response = $this->api->update($uri, $data);
        $this->assertEquals($result, $response);
    }

    public function testCreate()
    {
        $uri = '/resource';
        $data = array('property' => 'value');
        $requestHeaders = array('Content-Type' => 'application/json');
        $result = '{"resource" : 1}';

        $this->_createTest($uri, $data, $requestHeaders, $result, $this->authentificationMock);

        $response = $this->api->create($uri, $data);
        $this->assertEquals($result, $response);
    }

    public function testDelete()
    {
        $uri = '/resource/1';
        $result = true;

        $this->_deleteTest($uri, $this->authentificationMock, $result);

        $response = $this->api->delete($uri);
        $this->assertEquals($result, $response);
    }
}
