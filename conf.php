<?php
class Conf
{
	/**
	 * The application name, as used in the changeset tags
	 * @var string
	 */
	const APP_NAME = "OpenStreetMap Nomino";
	/**
	 * The comment to send with every changeset 
	 * @var string
	 */
	const COMMIT_MESSAGE = "Place names translation with Nomino";
	/**
	 * URL of a Nominatim instance
	 * @var string
	 */
	const NOMINATIM_API = "http://nominatim.openstreetmap.org/";
	/**
	 * Max number of results retrieved in a Nominatim request
	 * @var int
	 */
	const NOMINATIM_NB_RESULTS = 10;
}
?>