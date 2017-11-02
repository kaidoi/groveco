# groveco
Code challenge submission for GroveCo

This project is a code submission for the GroveCo store locator code challenge described here:
https://github.com/groveco/code-challenge

It is implemented using PHP, with PHPUnit as the testing framework.
It utilizes the following third party services/resources:
* Google's Geocode API: Used for translating and address or zip code to latitude/longitude coordinates
* docopt.php: As required by the code challenge (https://github.com/docopt/docopt.php)
* http://www.geodatasource.com/developers/php: Borrowed an algorithm for calculating distances between two latitude/longitude coordinates.

Summary of Implementation
======================================================================
* find_store.php accepts --address or --zip argument
* The input address or zip value is translated into latitude/longitude coordinates using Googles Geocode API
* The local CSV containing the store locations are read into memory, and then script iterates through each store row from the CSV, determines the distance between the input location and the store location based on latitude/longitude using the algorithm borrowed from http://www.geodatasource.com/developers/php, and selects the location with the lowest value returned from the distance calculator
* The nearest location is output to the screen based on format optionally specified at command execution.

Installation
======================================================================
You will need to have PHP and PHPUnit installed.
When you download the repo, you will need to edit config.php and add your Google API Key.

Running the script from command line
======================================================================

Examples of executing the CLI program::

    php find_store.php --address="1462 Pine St, San Francisco, CA 94109"
    php find_store.php --zip="94117" --units=mi --output=json

You can run unit tests with the following command::

    phpunit --verbose --debug test/

Caveats and Assumptions
======================================================================
* This was my first time utilizing PHPUnit testing framework :-)
* Docopt.php was not recognizing the Usage patterns properly so I recreated the Usage input and had to add additional validation to the optional arguments in the find_store.php wrapper script
* Address and zip input values are not being validated as "real" address values, but utilizes Google's best guess.  For this project we are assuming the address/zip values passed into the command will be valid.
* Performance considerations: For this project, the list of store locations is small (under 2000) which any server nowadays can iterate through very quickly.  The assumption is that the usage of this project is limited to command line execution via the find_store.php wrapper only.  If this were meant for high volume usage and/or the store list count was much greater, then StoreLocator logic would need to be optimized as such.
