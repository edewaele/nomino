<?php
/**
 * OSM/OAPI.class.php
 */

require_once ( __DIR__.'/OAPIResponse.php');

/**
 * Description of OSM_OAPI
 *
 * @author cyrille
 */
class OSM_OAPI {

	public static $DEBUG = 0;

	/**
	 * http://www.overpass-api.de/api/interpreter
	 * http://overpassapi.letuffe.org/api/interpreter
	 */
	const URL = 'http://www.overpass-api.de/api/interpreter';
	/**
	 * http://www.overpass-api.de/api/xapi
	 * http://api.openstreetmap.fr/xapi
	 * deprecated: http://overpassapi.letuffe.org/api/xapi
	 */
	//const XAPI_URL = 'http://www.overpass-api.de/api/xapi';
	const XAPI_URL = 'http://api.openstreetmap.fr/xapi';
	const VERSION = '0.1';
	const USER_AGENT = 'OSM_OAPI-Php http://www.openstreetmap.org/user/Cyrille37';

	const XAPI_REQ_TYPE_NODE = 'node';
	const XAPI_REQ_TYPE_WAY = 'way';
	const XAPI_REQ_TYPE_RELATION = 'relation';
	const XAPI_REQ_TYPE_ANY = '*';

	protected $_settings;

	public function __construct($settings = array()) {

		$this->_settings =& $settings;

		if (isset($settings['cache']))
		{
			if (!method_exists($settings['cache'], 'load'))
				throw new Exception('Cache engine is not compatible, miss method load()');
			if (!method_exists($settings['cache'], 'save'))
				throw new Exception('Cache engine is not compatible, miss method save()');
		}
	}

	public function request_xapi($query, $req_type = OSM_OAPI::XAPI_REQ_TYPE_ANY) {

		$this->_dbg(__METHOD__, $query);

		$result = $this->_cacheLoad(md5($req_type . $query));
		if ($result != null)
			return $result;

		$opts = array('http' =>
			array(
				'method' => 'GET',
				'user_agent' => OSM_OAPI::USER_AGENT . ' ' . OSM_OAPI::VERSION
			)
		);
		$context = stream_context_create($opts);

		$result = file_get_contents(OSM_OAPI::XAPI_URL . '?' . $req_type . urlencode($query), false, $context);

		$this->_cacheSave(md5($req_type . $query), $result);
		return $result;
	}

	public function request($xmlQuery) {

		$this->_dbg(__METHOD__, $xmlQuery);

		$result = $this->_cacheLoad(md5($xmlQuery));
		if ($result != null)
			return $result;

		$postdata = http_build_query(array('data' => $xmlQuery));
		$opts = array('http' =>
			array(
				'method' => 'POST',
				'user_agent' => OSM_OAPI::USER_AGENT . ' ' . OSM_OAPI::VERSION,
				'header' => 'Content-type: application/x-www-form-urlencoded',
				'content' => $postdata
			)
		);
		$context = stream_context_create($opts);

		$result = file_get_contents(OSM_OAPI::URL, false, $context);
		$this->_cacheSave(md5($xmlQuery), $result);
		return $result;
	}

	protected function _cacheLoad($key) {

		if (!isset($this->_settings['cache']))
		{
			$this->_dbg(__METHOD__, 'no cache engine set');
			return null;
		}
		$res = $this->_settings['cache']->load($key);
		if (self::$DEBUG)
		{
			if ($res == null)
				$this->_dbg(__METHOD__, 'not found');
			else
				$this->_dbg(__METHOD__, 'got it');
		}
		return $res;
	}

	protected function _cacheSave($key, $value) {
		
		if (!isset($this->_settings['cache']))
		{
			$this->_dbg(__METHOD__, 'no cache engine set');
			return null;
		}
		$this->_dbg(__METHOD__, 'save');
		$this->_settings['cache']->save($value, $key);
	}

	protected function _dbg($who, $str='') {
		if (self::$DEBUG)
		{
			echo '[dbg][' . $who . '] ' . $str . "\n";
		}
	}

}
