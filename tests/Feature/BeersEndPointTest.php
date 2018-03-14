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

    public function testGetBeerWithInvalidId()
    {
      $client = new Client();
      $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?ids=invalidId&key='.$this->key);
      $body = json_decode($response->getBody());
      $this->assertFalse(isset($body->data));
    }

    /**
     * @dataProvider nameProvider
     */
    public function testGetBeerWithName($name)
    {
      $client = new Client();
      $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?name='.$name.'&key='.$this->key);
      $body = json_decode($response->getBody());
      $this->assertEquals($name,$body->data[0]->name);
    }

    public function nameProvider()
    {
      return[
        ["Amber Ale"],
        ["Arctic Ale"],
        ["Highlander Scottish Ale"]
      ];
    }

    public function testGetBeerWithInvalidName()
    {
      $client = new Client();
      $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?name=InvalidName&key='.$this->key);
      $body = json_decode($response->getBody());
      $this->assertFalse(isset($body->data));
    }

    /**
     * @dataProvider glassIdProvider
     */
    public function testGetBeersWithGlass($id)
    {
      $client = new Client();
      $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?glasswareId='.$id.'&key='.$this->key);
      $body = json_decode($response->getBody());
      $glasses=[];
      for ($i=0; $i < count($body->data); $i++) {
        array_push($glasses,$body->data[$i]->glasswareId);
      }
      for ($i=0; $i < count($glasses); $i++) {
        $this->assertEquals($glasses[$i],$id);
      }
    }

    public function glassIdProvider()
    {
      return [
        [1],
        [2],
        [3]
      ];
    }

    /**
     * @dataProvider srmIdsProvider
     */
    public function testGetBeersWithSrm($id)
    {
      $client = new Client();
      $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?srmId='.$id.'&key='.$this->key);
      $body = json_decode($response->getBody());
      $srms=[];
      for ($i=0; $i < count($body->data); $i++) {
        array_push($srms,$body->data[$i]->srmId);
      }
      for ($i=0; $i < count($srms); $i++) {
        $this->assertEquals($srms[$i],$id);
      }
    }

    public function srmIdsProvider()
    {
      return [
        [1],
        [2],
        [3]
      ];
    }

    /**
     * @dataProvider availableIdsProvider
     */
    public function testGetBeersWithAvailable($id)
    {
      $client = new Client();
      $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?availableId='.$id.'&key='.$this->key);
      $body = json_decode($response->getBody());
      $availables=[];
      for ($i=0; $i < count($body->data); $i++) {
        array_push($availables,$body->data[$i]->availableId);
      }
      for ($i=0; $i < count($availables); $i++) {
        $this->assertEquals($availables[$i],$id);
      }
    }

    public function availableIdsProvider()
    {
      return [
        [1],
        [2],
        [3]
      ];
    }

    /**
     * @dataProvider styleIdsProvider
     */
    public function testGetBeersWithStyle($id)
    {
      $client = new Client();
      $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?styleId='.$id.'&key='.$this->key);
      $body = json_decode($response->getBody());
      $styles=[];
      for ($i=0; $i < count($body->data); $i++) {
        array_push($styles,$body->data[$i]->styleId);
      }
      for ($i=0; $i < count($styles); $i++) {
        $this->assertEquals($styles[$i],$id);
      }
    }

    public function styleIdsProvider()
    {
      return [
        [1],
        [2],
        [3]
      ];
    }
}
