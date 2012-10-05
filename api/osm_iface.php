<?php
include('../lib/yapafo/lib/OSM/Api.php');
include('../conf.php');
session_start();

// osm api handler is instantiated if necessary
if(!isset($_SESSION['api']))
{ 
	$api = new OSM_Api(array('appName'=>Conf::APP_NAME,'url'=>OSM_Api::URL_PROD_UK));
	$_SESSION['api'] = $api;
}
else 
{
	$api = $_SESSION['api'];
}

if(isset($_REQUEST['action']))
{
	if($_REQUEST['action'] == 'get' && isset($_GET['type']) && isset($_GET['id']))
	{
		try{			
			$elt = $api->getObject($_GET['type'],$_GET['id']);
			echo $elt->asXmlStr();
		}catch(Exception $e){
			echo 0;
		}
	}
	else if($_GET['action'] == 'set' && isset($_GET['type']) && isset($_GET['id']) && isset($_GET['name']))
	{
		$elt = null;
		try{			
			$elt = $api->getObject($_GET['type'],$_GET['id']);
		}catch(Exception $e){
			echo 0;
		}
		// search for the object
		if($elt != null)
		{
			// Iterate all existing name:** tags in the object 
			foreach(array_keys($elt->findTags()) as $existingTag)
			{
				if(strlen($existingTag)==7 && substr($existingTag,0,5) == 'name:' || array_key_exists($existingTag,Conf::$NAME_FIELDS))
				{
					// If this tag is not in the request, it was removed by the user
					if(! array_key_exists($existingTag,$_GET))
					{
						$elt->removeTag($existingTag);
					}
				}
			}
			
			if($elt->getTag('name')->getValue() != $_GET['name'])
			{
				$elt->setTag('name',$_GET['name']);
			}
			
			// set the translations
			foreach($_GET as $tag_key => $tag_value)
			{
				if(strlen($tag_key)==7 && substr($tag_key,0,5) == 'name:' && strlen($tag_value) > 0 
						|| array_key_exists($tag_key,Conf::$NAME_FIELDS) && strlen($tag_value) > 0)
				{
					if($elt->getTag($tag_key) != null)
					{
						// setTag is used only if the value is different, as is sets the objets dirty
						if($elt->getTag($tag_key)->getValue() != $tag_value)
						{
							$elt->setTag($tag_key,$tag_value);
						}
					}
					else
					{
						$elt->addTag(new OSM_Objects_Tag($tag_key,$tag_value));
					}
				}
			}
			echo 1;
			
		}
		else 
		{
			echo 0;
		}
	}
	else if($_REQUEST['action'] == 'list')
	{
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dnode = $dom->createElement('Document');
		$dom->appendChild($dnode);
		$loadedObjects = $api->getObjects();
		foreach($loadedObjects as $osmObject)
		{
			if($osmObject->isDirty())
			{
				// list of dirty tags
				$dirtyTags = '';
				foreach($osmObject->findTags() as $tag)
				{
					if($tag->isDirty())
					{
						$dirtyTags .= ($dirtyTags==''?'':', ') . $tag->getKey() . ' = ' . $tag->getValue();
					}
				}
				
				// determine a name and a type
				$name = 'No name';
				if($osmObject->getTag('name') != null)
				{
					$name = $osmObject->getTag('name')->getValue();
				}
				
				$type = "";
				if(get_class($osmObject) == 'OSM_Objects_Node')$type = 'node';
				else if(get_class($osmObject) == 'OSM_Objects_Way')$type = 'way';
				else if(get_class($osmObject) == 'OSM_Objects_Relation')$type = 'relation';
				
				$oNode = $dom->createElement('Object');
				$oNode->setAttribute('name',$name);
				$oNode->setAttribute('osm_id',$osmObject->getId());
				$oNode->setAttribute('osm_type',$type);
				$oNode->setAttribute('dirty_tags',$dirtyTags);
				$dnode->appendChild($oNode);
			}
		}
		header('Content-type: application/xml');
		echo html_entity_decode($dom->saveXML());
	}
	else if($_GET['action'] == 'revert' && isset($_GET['type']) && isset($_GET['id']))
	{
		$api->removeObject($_GET['type'],$_GET['id']);;
		echo 1;
	}
	else if($_GET['action'] == 'getXml')
	{
		header('Content-type: application/xml');
		header('Content-Disposition: attachment; filename="places.osm"');
		echo $api->getXMLDocument();
	}
	else if($_GET['action'] == 'save')
	{
		// TODO implement commits
		echo $api->saveChanges(Conf::COMMIT_MESSAGE);
	}
	else 
	{
		echo 'Invalid request';
	}
}
