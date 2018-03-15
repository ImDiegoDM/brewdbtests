<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class BeerIdEndPointTest extends TestCase
{
  private $key = "4f59e52fa1c653bb164e390d0822ea8c";

  /**
   * @dataProvider idsProvider
   */
  public function testGetBeerId($id)
  {
    $client = new Client();
    $response = $client->request('GET','http://api.brewerydb.com/v2/beer/'.$id.'/?&key='.$this->key);
    $body = json_decode($response->getBody());
    $this->assertEquals($id,$body->data->id);
  }
  public function idsProvider()
  {
      return [
        ["v1ElI8"],
        ["dCVxfZ"],
        ["NB0OWu"],
        ["nXn2op"]
      ];
  }

  public function testGetBeerWithInvalidId()
  {
    $client = new Client();
    try {
      $response = $client->request('GET','http://api.brewerydb.com/v2/beer/invalidId/?key='.$this->key);
    } catch (GuzzleException $e) {
      $error=$e;
    }
    $this->assertTrue($error!=null);
    $this->assertEquals('failure',json_decode($error->getResponse()->getBody())->status);
    $this->assertEquals(404, $error->getResponse()->getStatusCode());
  }

  /**
   * @dataProvider idsProvider
   */
  public function testGetBeersWithBreweries($id)
  {
    $client = new Client();
    $response = $client->request('GET','http://api.brewerydb.com/v2/beer/'.$id.'/?withBreweries=Y&key='.$this->key);
    $body = json_decode($response->getBody());
    $this->assertTrue(isset($body->data->breweries));
  }
}
