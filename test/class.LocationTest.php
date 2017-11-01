<?php
require __DIR__.'/../lib/class.Location.php';

class LocationTest extends \PHPUnit\Framework\TestCase
{
    var $myAddress = "32 Yukon St, San Francisco, CA 94114";
    var $myZip = "94104";

    public function testGoogleApiKeyIsNotEmpty(){
        $myLocation = new Location($this->myAddress);
        $apiKey = $myLocation->getGoogleApiKey();
        $this->assertNotEmpty($apiKey, "Google API key was empty or not set in config.php");
    }

    public function testGoogleApiEndpointIsNotEmpty(){
        $myLocation = new Location($this->myAddress);
        $endpoint = $myLocation->getGoogleApiEndpoint();
        $this->assertNotEmpty($endpoint, "Google API endpoing was empty or not set in config.php");
    }

    public function testGoogleApiReturnsJsonOkFromAddress(){
        $myLocation = new Location($this->myAddress);
        $response = $myLocation->fetchGeocodeFromGoogle($this->myAddress);
        $this->assertEquals("OK", $response->status, "Google Geocode API request failed, return status was not 'OK'");
    }

    public function testGoogleApiReturnsJsonOkResponseFromZip(){
        $myLocation = new Location($this->myZip);
        $response = $myLocation->fetchGeocodeFromGoogle($this->myZip);
        $this->assertEquals("OK", $response->status, "Google Geocode API request failed, return status was not 'OK'");
    }

    public function testGoogleApiReturnsLatLongFromAddress(){
        $myLocation = new Location($this->myAddress);
        $response = $myLocation->fetchLatLongFromAddress($this->myAddress);
        $this->assertTrue( is_array($response) );
        $this->assertArrayHasKey("lat", $response);
        $this->assertArrayHasKey("long", $response);
        $this->assertStringMatchesFormat('%f', (string) $response["lat"]);
        $this->assertStringMatchesFormat('%f', (string) $response["long"]);
    }

    public function testGoogleApiReturnsLatLongFromZip(){
        $myLocation = new Location($this->myZip);
        $response = $myLocation->fetchLatLongFromAddress($this->myZip);
        $this->assertTrue( is_array($response) );
        $this->assertArrayHasKey("lat", $response);
        $this->assertArrayHasKey("long", $response);
        $this->assertStringMatchesFormat('%f', (string) $response["lat"]);
        $this->assertStringMatchesFormat('%f', (string) $response["long"]);
    }

}