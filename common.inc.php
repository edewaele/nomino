<?php

/**
 */
function currentPageURL() {

	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
	{
		$u = 'https';
	}
	else
	{
		$u = 'http';
	}
	$u .= '://';
	
	if ($_SERVER['SERVER_PORT'] != '80')
	{
		$u .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
	}
	else
	{
		$u .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	}
	return $u;
}

function currentPageURLwithoutQuery()
{
	$u = currentPageURL();
	if( $p = strpos($u, '?') !== false )
	{
		$u = substr($u, 0, $p);
	}
	return $u ;
}

/**
 * Language support manager
 */
class LanguageSupport
{
	private $lang = "";
	public function __construct()
	{
		$this->lang= substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	}
	/**
	 * initialise gettext system for PHP strings
	 */
	public function initGettext()
	{
		if(array_key_exists($this->lang, Conf::$UI_LANGUAGUES))
		{
			$lang=Conf::$UI_LANGUAGUES[$this->lang];
			$filename = 'default';
			putenv("LC_ALL=$lang");
			setlocale(LC_ALL, $lang);
			bindtextdomain($filename, './locale');
			bind_textdomain_codeset($filename, "UTF-8");
			textdomain($filename);
		}
	}
	/**
	 * Include the documentation file in "Documentation" tab
	 */
	public function printDoc()
	{
		$lngDir = "default";
		if(array_key_exists($this->lang, Conf::$UI_LANGUAGUES) && file_exists("locale/".Conf::$UI_LANGUAGUES[$this->lang]."/doc.php"))
		{
			$lngDir = Conf::$UI_LANGUAGUES[$this->lang];
		}
		include("locale/".$lngDir."/doc.php");
	} 
}

?>