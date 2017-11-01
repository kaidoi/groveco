<?php
require_once __DIR__.'/../lib/class.Location.php';
require_once __DIR__.'/../lib/class.StoreLocator.php';

class StoreLocatorTest extends \PHPUnit\Framework\TestCase
{
    var $myAddress = "32 Yukon St, San Francisco, CA 94114";
    var $myZip = "94104";
    var $myLat = "37.7926203";
    var $myLong = "-122.4013241";
    var $storeLat = "37.7926203";
    var $storeLong = "-122.4013241";

    public function testStoresListCsvFileExists(){
        $this->assertFileExists(STORE_LIST);
    }

    public function testStoresListCsvFileIsReadable(){
        $this->assertFileIsReadable(STORE_LIST);
    }

    public function testStoresListIsArrayWithAtLeastOneStoreLocation()
    {
        $storeLocator = new StoreLocator($this->myLat, $this->myLong);
        $storesList = $storeLocator->getStoresList();
        $this->assertTrue( is_array($storesList) );
        $this->assertGreaterThan(2, count($storesList));
    }

    public function testCalcDistanceReturnsFloat(){
        $distance = StoreLocator::calcDistance($this->myLat, $this->myLong, $this->storeLat, $this->storeLong, "mi");
        $this->assertStringMatchesFormat('%f', (string) $distance);
    }

    public function testFindNearestStoreReturnsResult(){
        $storeLocator = new StoreLocator($this->myLat, $this->myLong);
        $nearestStore = $storeLocator->findNearestStore();
        $this->assertTrue( is_array($nearestStore) );
        $this->assertArrayHasKey("result", $nearestStore);
    }

}