<?php
/**
 * KML Document generator, it can construct a KML document with geolocated points.
 * @author manud https://gitorious.org/~manud
 */
class KMLDocument{
	/**
	* The document's root node 
	*/
	private $xmlRoot;
	/**
	* DOM document object
	 */
	private $dom;
	/**
	 * 
	 * @var int
	 */
	private $counter = 0;
	
	/**
	 * Default constructor, initialises the object
	 */
	function __construct()
	{
		$this->dom = new DOMDocument('1.0', 'UTF-8');
		
		// Creates the root KML element and appends it to the root document.
		$node = $this->dom->createElementNS('http://earth.google.com/kml/2.1', 'kml');
		$parNode = $this->dom->appendChild($node);
		
		// Creates a KML Document element and append it to the KML element.
		$dnode = $this->dom->createElement('Document');
		$this->xmlRoot = $parNode->appendChild($dnode);
	}
	
	/**
	 * Get the XML string representation of the document
	 */
	public function getXML()
	{		
		return  $this->dom->saveXML();
	}
	
	/**
	 * Add a point feature to the KML document
	 * @param float $lon longitude
	 * @param float $lat latitude
	 * @param array $attrs additional attributes and values (array of keys and values), every key names a child tag, the value is stored inside the tag  
	 */
	public function addPoint($lon,$lat,$attrs)
	{		
		// Creates a Placemark and append it to the Document.
		$node = $this->dom->createElement('Placemark');
		$placeNode = $this->xmlRoot->appendChild($node);
		
		// Creates an id attribute and assign it the value of id column.
		$placeNode->setAttribute('id', 'placemark' . $this->counter);
		$this->counter++;
		
		// Create name, and description elements and assigns them the values of the name and address columns from the results.
		$pointNode = $this->dom->createElement('coordinates',$lon.",".$lat);
		$coordNode = $this->dom->createElement('Point');
		$coordNode->appendChild($pointNode);
		$placeNode->appendChild($coordNode);
		
		foreach($attrs as $k => $v)
		{
			$tempNode = $this->dom->createElement($k,$v);
			$placeNode->appendChild($tempNode);
		}
	}
}
