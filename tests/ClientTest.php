<?php

namespace AlfredAppTest;

use AlfredApp\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    const BASE_URL = 'https://httpbin.org';

    public function setUp()
    {
        parent::setUp();

        try {
            file_get_contents(self::BASE_URL);
        } catch (\Exception $e) {
            $this->markTestSkipped('You need an internet connection to test this');
        }
    }

    public function testGet()
    {
        $getURL = self::BASE_URL.'/get';
        $client = new Client($getURL);
        $response = $client->get();
        $object = json_decode($response);

        $this->assertNotEmpty($response);
        $this->assertNotNull($object);
        $this->assertSame($getURL, $object->url);
    }

    public function testPut()
    {
        $putURL = self::BASE_URL.'/put';
        $dataToPut = ['foo' => 'bar'];

        $client = new Client($putURL);
        $response = $client->put($dataToPut);
        $object = json_decode($response);

        $this->assertNotEmpty($response);
        $this->assertNotNull($object);
        $this->assertSame($putURL, $object->url);
        $this->assertSame($dataToPut, (array) $object->json);
    }

    public function testPost()
    {
        $postURL = self::BASE_URL.'/post';
        $dataToPost = ['foo' => 'bar'];

        $client = new Client($postURL);
        $response = $client->post($dataToPost);
        $object = json_decode($response);

        $this->assertNotEmpty($response);
        $this->assertNotNull($object);
        $this->assertSame($postURL, $object->url);
        $this->assertSame($dataToPost, (array) $object->json);
    }

    public function testPost_WithNoData()
    {
        $postURL = self::BASE_URL.'/post';

        $client = new Client($postURL);
        $response = $client->post();
        $object = json_decode($response);

        $this->assertNotEmpty($response);
        $this->assertNotNull($object);
        $this->assertSame($postURL, $object->url);
        $this->assertNull($object->json);
    }

    public function testDelete()
    {
        $deleteURL = self::BASE_URL.'/delete';

        $client = new Client($deleteURL);
        $response = $client->delete();
        $object = json_decode($response);

        $this->assertNotEmpty($response);
        $this->assertNotNull($object);
        $this->assertSame($deleteURL, $object->url);
        $this->assertNull($object->json);
    }
}