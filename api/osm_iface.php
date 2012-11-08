<?php

/**
 * Manage user actions on data.
 * @author manud https://gitorious.org/~manud
 */
require_once('../conf.php');

require_once( __DIR__ . '/../osmApi.inc.php');

$action = isset($_REQUEST['action']) && trim($_REQUEST['action']) != '' ? $_REQUEST['action'] : null;

switch ($action)
{
	/**
	 * action "get"
	 */
	case 'get':
		if (isset($_GET['type']) && isset($_GET['id']))
		{
			try
			{
				$elt = $osmApi->getObject($_GET['type'], $_GET['id']);
				echo $elt->asXmlStr();
			}
			catch (Exception $e)
			{
				echo 0;
			}
		}
		break;

	/**
	 * action "set"
	 */
	case 'set':
		if (isset($_GET['type']) && isset($_GET['id']) && isset($_GET['name']))
		{
			$elt = null;
			try
			{
				// search for the object
				$elt = $osmApi->getObject($_GET['type'], $_GET['id']);

				if ($elt == null)
				{
					throw new Exception('Object not found');
				}

				// Iterate all existing name:** tags in the object 
				foreach (array_keys($elt->findTags()) as $existingTag)
				{
					if (strlen($existingTag) == 7 && substr($existingTag, 0, 5) == 'name:' || array_key_exists($existingTag, Conf::$NAME_FIELDS))
					{
						// If this tag is not in the request, it was removed by the user
						if (!array_key_exists($existingTag, $_GET))
						{
							$elt->removeTag($existingTag);
						}
					}
				}

				if ($elt->getTag('name')->getValue() != $_GET['name'])
				{
					$elt->setTag('name', $_GET['name']);
				}

				// set the translations
				foreach ($_GET as $tag_key => $tag_value)
				{
					if (strlen($tag_key) == 7 && substr($tag_key, 0, 5) == 'name:' && strlen($tag_value) > 0
						|| array_key_exists($tag_key, Conf::$NAME_FIELDS) && strlen($tag_value) > 0)
					{
						if ($elt->getTag($tag_key) != null)
						{
							// setTag is used only if the value is different, as is sets the objets dirty
							if ($elt->getTag($tag_key)->getValue() != $tag_value)
							{
								$elt->setTag($tag_key, $tag_value);
							}
						}
						else
						{
							$elt->addTag(new OSM_Objects_Tag($tag_key, $tag_value));
						}
					}
				}

				echo 1;
			}
			catch (Exception $e)
			{
				echo 0;
			}
		}
		break;

	/**
	 * action "list"
	 */
	case 'list':

		$dom = new DOMDocument('1.0', 'UTF-8');
		$dnode = $dom->createElement('Document');
		$dom->appendChild($dnode);

		$loadedObjects = $osmApi->getObjects();

		foreach ($loadedObjects as $osmObject)
		{
			if ($osmObject->isDirty())
			{
				// list of dirty tags
				$dirtyTags = '';
				foreach ($osmObject->findTags() as $tag)
				{
					if ($tag->isDirty())
					{
						$dirtyTags .= ($dirtyTags == '' ? '' : ', ') . $tag->getKey() . ' = ' . $tag->getValue();
					}
				}

				// determine a name and a type
				$name = 'No name';
				if ($osmObject->getTag('name') != null)
				{
					$name = $osmObject->getTag('name')->getValue();
				}

				$type = '';
				if (get_class($osmObject) == 'OSM_Objects_Node')
					$type = 'node';
				else if (get_class($osmObject) == 'OSM_Objects_Way')
					$type = 'way';
				else if (get_class($osmObject) == 'OSM_Objects_Relation')
					$type = 'relation';

				$oNode = $dom->createElement('Object');
				$oNode->setAttribute('name', $name);
				$oNode->setAttribute('osm_id', $osmObject->getId());
				$oNode->setAttribute('osm_type', $type);
				$oNode->setAttribute('dirty_tags', $dirtyTags);
				$dnode->appendChild($oNode);
			}
		}
		header('Content-type: application/xml');
		echo html_entity_decode($dom->saveXML());

		break;

	/**
	 * action "revert"
	 */
	case 'revert':

		$osmApi->removeObject($_GET['type'], $_GET['id']);
		echo 1;

		break;

	/**
	 * action "getXml"
	 */
	case 'getXml':

		header('Content-type: application/xml');
		header('Content-Disposition: attachment; filename="places.osm"');
		echo $osmApi->getXMLDocument();

		break;

	/**
	 * action "save"
	 */
	case 'save':
		// TODO implement commits
		if($osmApi->isAllowedToWriteApi())
			echo $osmApi->saveChanges(Conf::COMMIT_MESSAGE);
		else 
			echo 0;

		break;
		
	/**
	 * Save the preferences
	 */
	case 'savePref':
		if(isset($_GET["k"]) && isset($_GET["v"]))
			$osmApi->setUserPreference($_GET["k"], $_GET["v"]);
		break;

	/**
	 * action "osmOAuth"
	 */
	case 'osmOAuth' :
		
		startOsmAuth();

		break;

	/**
	 * default: Invalid request
	 */
	default:
		echo 'Invalid request';

		break;
}

	