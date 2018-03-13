<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class BeersEndPointTest extends TestCase
{

    public function testWihtoutKey()
    {
        //$this->expectException(GuzzleException::class);
        $client = new Client();
        try {
          $response = $client->request('GET','http://api.brewerydb.com/v2/beers');
        } catch (GuzzleException $e) {
          $this->assertEquals('failure',json_decode($e->getResponse()->getBody())->status);
          $this->assertEquals(401, $e->getResponse()->getStatusCode());
        }

    }

    public function testWihtInvalidKey()
    {
        //$this->expectException(GuzzleException::class);
        $client = new Client();
        try {
          $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?key=invalidkey');
        } catch (GuzzleException $e) {
          $this->assertEquals('failure',json_decode($e->getResponse()->getBody())->status);
          $this->assertEquals(401, $e->getResponse()->getStatusCode());
        }

    }
}
