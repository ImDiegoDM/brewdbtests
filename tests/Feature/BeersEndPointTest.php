<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class BeersEndPointTest extends TestCase
{
    private $key = "4f59e52fa1c653bb164e390d0822ea8c";

    public function testWihtoutKey()
    {
        $client = new Client();
        $error;
        try {
          $response = $client->request('GET','http://api.brewerydb.com/v2/beers');
        } catch (GuzzleException $e) {
          $error=$e;
        }
        $this->assertTrue($error!=null);
        $this->assertEquals('failure',json_decode($e->getResponse()->getBody())->status);
        $this->assertEquals(401, $e->getResponse()->getStatusCode());
    }

    public function testWihtInvalidKey()
    {
        $client = new Client();
        $error;
        try {
          $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?key=invalidkey');
        } catch (GuzzleException $e) {
          $error=$e;
        }
        $this->assertTrue($error!=null);
        $this->assertEquals('failure',json_decode($error->getResponse()->getBody())->status);
        $this->assertEquals(401, $error->getResponse()->getStatusCode());
    }

    public function testWithValidKeyAndNoAtribute()
    {
      $client = new Client();
      $error;
      try {
        $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?key='.$this->key);
      } catch (GuzzleException $e) {
        $error=$e;
      }
      $this->assertTrue($error!=null);
      $this->assertEquals('failure',json_decode($error->getResponse()->getBody())->status);
      $this->assertEquals(400, $error->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider idsProvider
     */
    public function testGetBeerWithId($id)
    {
      $client = new Client();
      $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?ids='.$id.'&key='.$this->key);
      $body = json_decode($response->getBody());
      $this->assertEquals($id,$body->data[0]->id);
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

    /**
     * @dataProvider multipleIdsProvider
     */
    public function testGetBeerWithMultiplesId($ids)
    {
      $client = new Client();
      $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?ids='.$ids.'&key='.$this->key);
      $ids = explode(',',$ids);
      $data = json_decode($response->getBody())->data;
      $idsReturned=[];
      for ($i=0; $i < count($data); $i++) {
        array_push($idsReturned,$data[$i]->id);
      }
      for ($i=0; $i <count($ids) ; $i++) {
        $this->assertContains($ids[$i],$idsReturned);
      }
    }

    public function multipleIdsProvider()
    {
        return [
          ["v1ElI8,dCVxfZ,NB0OWu"],
        ];
    }
}
