<?php
/**
 * Find places through the Nominatim API.
 * @author manud https://gitorious.org/~manud
 */

require_once('../conf.php');
require_once('KMLDocument.php');
require_once('HTTP/Request2.php');

/**
 * Find places through the Nominatim API
 */
if(isset($_GET['q']))
{
	// send a request to Nominatim
	$request = new HTTP_Request2(Conf::NOMINATIM_API.'search',HTTP_Request2::METHOD_GET) ;
	$request->getUrl()->setQueryVariables(array(
			'q' => $_GET['q'],
			'limit'=>Conf::NOMINATIM_NB_RESULTS,
			'polygon'=>0, // do not describe the whole geometry
			'format'=>'xml'// a web page is returned by default
			));
	try {
	    $response = $request->send();
	    if (200 == $response->getStatus()) {
			// XML DOM Handler
			$dom = new DomDocument();
			$dom->loadXML($response->getBody());
			$resultsList = $dom->getElementsByTagName('place');
			
			// KML response 
			$kml = new KMLDocument();
			
			// put every answer in the KML response
			for($i = 0; $i < $resultsList->length; $i++)
			{
				$place = $resultsList->item($i);
				$kml->addPoint($place->getAttribute('lon'),$place->getAttribute('lat'),array(
						'name' => $place->getAttribute('display_name'),
						'styleURL' => "#".$place->getAttribute('type'),
						'osm_id' => $place->getAttribute('osm_id'),
						'osm_type' => $place->getAttribute('osm_type'),
						'icon' => $place->getAttribute('icon'),
						'class' => $place->getAttribute('class'),
						'type' => $place->getAttribute('type')
				));
			}
			
			$kmlOutput = $kml->getXML();
			header('Content-type: application/vnd.google-earth.kml+xml');
			echo html_entity_decode($kmlOutput);
	    } else {
	        echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
	             $response->getReasonPhrase();
	    }
	} catch (HTTP_Request2_Exception $e) {
	    echo 'Error: ' . $e->getMessage();
	}	
}
else if(isset($_GET["lon"]) && isset($_GET["lat"]))
{
	// send a request to Nominatim
	$request = new HTTP_Request2(Conf::NOMINATIM_API.'reverse',HTTP_Request2::METHOD_GET) ;
	$request->getUrl()->setQueryVariables(array(
			'lon' => $_GET['lon'],
			'lat' => $_GET['lat'],
			'format'=>'xml'// a web page is returned by default
	));
	try {
		$response = $request->send();
		if (200 == $response->getStatus()) {
			echo $response->getBody();					
		} 
	}
	catch (HTTP_Request2_Exception $e) {
	echo 'Error: ' . $e->getMessage();
	}
}
