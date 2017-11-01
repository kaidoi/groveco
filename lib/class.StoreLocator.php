<?php
require_once('config.php');

/**
 *  StoreLocator - Finds the closest store location given lat/long coordinates and a list of store locations
 *
 *  @author BJoe
 */
class StoreLocator {
    private $_storesList = array();
    public $_myLat;
    public $_myLong;

    /**
     *  __construct
     */
    public function __construct($myLat, $myLong){
        $this->_myLat = $myLat;
        $this->_myLong = $myLong;
        $this->loadStores();
    }

    /*
     * loadStores - read store listings from local CSV file and assign to $_storesList
     * @return void
     */
    private function loadStores(){
        $loadFileToString = file_get_contents(STORE_LIST);
        $storesArr = explode("\r", $loadFileToString);
        foreach( $storesArr as $storeCsvRow ){
            $this->_storesList[] = str_getcsv($storeCsvRow);
        }
    }

    /**
     * getStoresList - returns stores lst
     * @return array
     */
    public function getStoresList(){
        return $this->_storesList;
    }

    /**
     * calcDistance - Calculate the distance between two locations given two sets of lat/long, taken from http://www.geodatasource.com/developers/php
     * @param $lat1 [Required]
     * @param $lon1 [Required]
     * @param $lat2 [Required]
     * @param $lon2 [Required]
     * @param $unit [Optional]
     * @return float
     */
    public function calcDistance($lat1, $lon1, $lat2, $lon2, $unit) {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        if ($unit == "km") {
            return ($miles * 1.609344);
        } else {
            return $miles;
        }
    }

    /**
     * findNearestStore - find the nearest store from storesList given myLat/myLong
     * @param $unit [Optional]
     * @return array
     */
    public function findNearestStore( $unit="mi" ){
        $minDistance = FALSE;
        $nearestStore = FALSE;
        foreach( $this->_storesList as $storeKey => $thisStore ){
            if( $storeKey == 0 ){
                continue; // Header row
            }
            $thisStoreLat = $thisStore[6];
            $thisStoreLong = $thisStore[7];
            $thisStoreDistance = $this->calcDistance($this->_myLat, $this->_myLong, $thisStoreLat, $thisStoreLong, $unit);
            if( $minDistance === FALSE || $thisStoreDistance < $minDistance ){
                $minDistance = $thisStoreDistance;
                $nearestStore["result"]["storeName"] = $thisStore[0];
                $nearestStore["result"]["storeLocation"] = $thisStore[1];
                $nearestStore["result"]["address"] = $thisStore[2];
                $nearestStore["result"]["city"] = $thisStore[3];
                $nearestStore["result"]["state"] = $thisStore[4];
                $nearestStore["result"]["zip"] = $thisStore[5];
                $nearestStore["result"]["lat"] = $thisStore[6];
                $nearestStore["result"]["long"] = $thisStore[7];
                $nearestStore["result"]["county"] = $thisStore[8];
                continue;
            }
        }
        $nearestStore["distance"] = number_format($minDistance,4);
        $nearestStore["distanceUnit"] = $unit;
        return $nearestStore;
    }
}
?>