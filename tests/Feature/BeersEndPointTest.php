<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Carbon\Carbon;

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

    /**
     * @dataProvider organicProvider
     */
    public function testGetBeersWithOrganic($organic)
    {
      $client = new Client();
      $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?styleId=1&isOrganic='.$organic.'&key='.$this->key);
      $body = json_decode($response->getBody());
      $organics=[];
      for ($i=0; $i < count($body->data); $i++) {
        array_push($organics,$body->data[$i]->isOrganic);
      }
      for ($i=0; $i < count($organics); $i++) {
        $this->assertEquals($organics[$i],$organic);
      }
    }

    public function organicProvider()
    {
      return [
        ["Y"],
        ["N"],
      ];
    }

    public function testGetBeersWithLabelY()
    {
      $client = new Client();
      $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?styleId=1&hasLabels=Y&key='.$this->key);
      $body = json_decode($response->getBody());
      for ($i=0; $i < count($body->data); $i++) {
        $this->assertTrue(isset($body->data[$i]->labels));
      }
    }

    public function testGetBeersWithLabelN()
    {
      $client = new Client();
      $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?styleId=1&hasLabels=N&key='.$this->key);
      $body = json_decode($response->getBody());
      for ($i=0; $i < count($body->data); $i++) {
        $this->assertTrue(!isset($body->data[$i]->labels));
      }
    }

    /**
     * @dataProvider yearsProvider
     */
    public function testGetBeersWithYear($year)
    {
      $client = new Client();
      $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?styleId=1&year='.$year.'&key='.$this->key);
      $body = json_decode($response->getBody());
      $beersYears=[];
      for ($i=0; $i < count($body->data); $i++) {
        array_push($beersYears,$body->data[$i]->year);
      }
      for ($i=0; $i < count($beersYears); $i++) {
        $this->assertEquals($beersYears[$i],$year);
      }
    }

    public function yearsProvider()
    {
      return [
        ["2017"],
        ["2013"]
      ];
    }

    /**
     * @dataProvider unixDatesProvider
     */
    public function testGetBeersWithSince($unixDate)
    {
      $client = new Client();
      $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?styleId=2&since='.$unixDate.'&key='.$this->key);
      $body = json_decode($response->getBody());
      $dates=[];
      for ($i=0; $i < count($body->data); $i++) {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $body->data[$i]->updateDate);
        array_push($dates,$date);
      }
      for ($i=0; $i < count($dates); $i++) {
        $this->assertTrue($dates[$i]->timestamp>$unixDate);
      }
    }

    public function unixDatesProvider()
    {
      return [
        [1519862400],
      ];
    }

    /**
     * @dataProvider statusProvider
     */
    public function testGetBeersWithStatus($status)
    {
      $client = new Client();
      $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?styleId=1&status='.$status.'&key='.$this->key);
      $body = json_decode($response->getBody());
      $beersStatus=[];
      for ($i=0; $i < count($body->data); $i++) {
        array_push($beersStatus,$body->data[$i]->status);
      }
      for ($i=0; $i < count($beersStatus); $i++) {
        $this->assertEquals($beersStatus[$i],$status);
      }
    }

    public function statusProvider()
    {
      return [
        ["verified"],
      ];
    }

    /**
     * @dataProvider orderProvider
     */
    public function testOrderBeers($order)
    {
      $client = new Client();
      $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?styleId=1&order='.$order.'&key='.$this->key);
      $body = json_decode($response->getBody());
      $beers=[];
      for ($i=0; $i < count($body->data); $i++) {
        array_push($beers,$body->data[$i]->$order);
      }
      $beersInOrder = $beers;
      natsort($beersInOrder);
      $this->assertTrue($this->IsArraysEquals($beersInOrder,$beers));
    }

    private function IsArraysEquals($array1,$array2)
    {
      if(count($array1)!=count($array2)) return false;
      for ($i=0; $i < count($array1); $i++) {
        if($array1[$i]!=$array2[$i]){
          echo $array1[$i]." is diferent then ".$array2[$i];
          return false;
        }
      }
      return true;
    }

    public function orderProvider()
    {
      return [
        ["name"],
        ["status"],
        ["createDate"]
      ];
    }

    /**
     * @dataProvider orderProvider
     */
    public function testOrderBeersDesc($order)
    {
      $client = new Client();
      $response = $client->request('GET','http://api.brewerydb.com/v2/beers/?styleId=1&sort=DESC&order='.$order.'&key='.$this->key);
      $body = json_decode($response->getBody());
      $beers=[];
      for ($i=0; $i < count($body->data); $i++) {
        array_push($beers,$body->data[$i]->$order);
      }
      $beersInOrder = $beers;
      rsort($beersInOrder,SORT_FLAG_CASE | SORT_STRING);
      $this->assertTrue($this->IsArraysEquals($beersInOrder,$beers));
    }
}
