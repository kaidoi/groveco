#!/usr/bin/php -f
<?php
require_once 'lib/docopt.php';
require_once 'src/class.Location.php';
require_once 'src/class.StoreLocator.php';

/**
 *  find_store.php - Wrapper script for finding nearest store location based on --zip OR --address input
 *                   Uses local CSV file for list of store locations
 *
 *  @author BJoe
 */

$doc = <<<'DOCOPT'
Find Store
  find_store.php will locate the nearest store (as the vrow flies) from
  store-locations.csv, print the matching store address, as well as
  the distance to that store.

Usage:
  find_store.php (--address=<address> | --zip=<zip>) [--units=<units>] [--output=<output>]

Options:
  -h --help            Show this screen.
  --zip                Find nearest store to this zip code. If there are multiple best-matches, return the first.
  --address            Find nearest store to this address. If there are multiple best-matches, return the first.
  --units=(mi|km)      Display units in miles or kilometers [default: mi]
  --output=(text|json) Output in human-readable text, or in JSON (e.g. machine-readable) [default: text]

DOCOPT;

$args = Docopt::handle($doc);

/**
 * Additional validation on optional args, couldn't figure out DOCOPT PHP (maybe a bug?)
 */
$units = $args->args["--units"];
if( empty($units) ){
    $units = "mi";
}elseif( !in_array($units, array("mi","km")) ){
    die($doc);
}

$output = $args->args["--output"];
if( empty($output) ){
    $output = "text";
}elseif( !in_array($output, array("text","json")) ){
    die($doc);
}

$myAddress = !empty( $args->args["--address"] ) ? $args->args["--address"] : $args->args["--zip"];
$myLocation = new Location($myAddress);

$result = array();
$result["myLat"] = $myLocation->_myLat;
$result["myLong"] = $myLocation->_myLong;

$storeLocator = new StoreLocator($myLocation->_myLat, $myLocation->_myLong);
$result["nearestStore"] = $storeLocator->findNearestStore( $units );

if( $output == "json" ){
    echo json_encode($result) . "\n";
}else{
    $outputText= array();
    $outputText[] = "";
    $outputText[] = "The nearest store location to '$myAddress' is ".$result["nearestStore"]["distance"]." ".$result["nearestStore"]["distanceUnit"]." away at:";
    $outputText[] = "    " . $result["nearestStore"]["result"]["storeName"] . " at " . $result["nearestStore"]["result"]["storeLocation"];
    $outputText[] = "";
    $outputText[] = "Address: ";
    $outputText[] = "    " . $result["nearestStore"]["result"]["address"];
    $outputText[] = "    " . $result["nearestStore"]["result"]["city"].", " . $result["nearestStore"]["result"]["state"] . " " . $result["nearestStore"]["result"]["zip"];
    $outputText[] = "    " . $result["nearestStore"]["result"]["county"];
    echo(implode("\n", $outputText)) . "\n";
}



?>