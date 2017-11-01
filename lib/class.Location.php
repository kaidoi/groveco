<?php
require_once('config.php');

/**
 *  Location - Determine latitude/longitude coordinates of your given location (address or zip)
 *
 *  @author BJoe
 */
class Location
{
    private $_apiKey;
    private $_endpoint;
    public $_myLat;
    public $_myLong;
    public $_myAddress;

    /**
     *  __construct
     */
    public function __construct($address){
        $this->_apiKey = GOOGLE_APIKEY;
        $this->_endpoint = GOOGLE_ENDPOINT;
        $this->_myAddress = $address;
        $this->fetchLatLongFromAddress($this->_myAddress);
    }

    /**
     * fetchGeocodeFromGoogle - Submit HTTPS request to Google Geocode API given address or zip
     * @param $address [Required]
     * @return json object
     */
    public function fetchGeocodeFromGoogle($address){
        $requestQueryStringArr = array();
        $requestQueryStringArr[] = "address=" . urlencode($address);
        if( !empty($this->_apiKey) ){
            $requestQueryStringArr[] = "key=" . $this->_apiKey;
        }
        $requestUrl = $this->_endpoint . "?" . implode("&", $requestQueryStringArr);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl );
        $headerArr = array();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArr);
        curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT,'BJoe Test Agent');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);           //Turn off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);           //Turn off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);                   // Exclude http response headers.
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);              // Set timeouts
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);                     // Set timeouts
        curl_setopt($ch, CURLOPT_ENCODING , "gzip");
        $jsonResponse = curl_exec($ch);

        if (curl_errno($ch)){
            die("Curl Error: ".curl_errno($ch)." - ".curl_error($ch));
        }
        curl_close($ch);

        $response = json_decode($jsonResponse);
        return $response;
    }

    /**
     * xlateAddressToLatLong - translate an address string (or zip code) to lat/long using Google Geocode API
     * @param $address [Required]
     * @return array
     */
    public function fetchLatLongFromAddress($address){
        $response = $this->fetchGeocodeFromGoogle($address);
        if( $response->status <> "OK" ){
            die("Could not determine your geo location based on address/zip value '$address'");
        }
        $searchResult = $response->results[0];
        $this->_myLat = $searchResult->geometry->location->lat;
        $this->_myLong = $searchResult->geometry->location->lng;
        $retArray = array();
        $retArray["lat"] = $this->_myLat;
        $retArray["long"] = $this->_myLong;
        return $retArray;
    }

    /**
     * getGoogleApiKey - returns Google API Key (stored in config.php)
     * @return string
     */
    public function getGoogleApiKey(){
        return $this->_apiKey;
    }

    /**
     * getGoogleApiEndpoint - returns Google API Endpoint URL
     * @return string
     */
    public function getGoogleApiEndpoint(){
        return $this->_endpoint;
    }



}
?>