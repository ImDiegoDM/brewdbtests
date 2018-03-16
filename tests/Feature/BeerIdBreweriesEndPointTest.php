<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class BeerIdBreweriesEndPointTest extends TestCase
{
  private $key = "4f59e52fa1c653bb164e390d0822ea8c";

  /**
   * @dataProvider idsProvider
   */
  public function testGetBeerIdBreweries($id)
  {
    $client = new Client();
    $response = $client->request('GET','http://api.brewerydb.com/v2/beer/'.$id.'/breweries/?&key='.$this->key);
    $body = json_decode($response->getBody());
    $this->assertEquals(200,$response->getStatusCode());
    $this->assertTrue(isset($body->data));
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
}
