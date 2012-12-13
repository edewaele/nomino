<?php
/**
 * Application configuration file
 * @author manu
 */
class Conf
{
	/**
	 * The application name, as used in the changeset tags
	 * @var string
	 */
	const APP_NAME = 'OpenStreetMap Nomino';
	/**
	 * The comment to send with every changeset 
	 * @var string
	 */
	const COMMIT_MESSAGE = 'Place names translation with Nomino';
	/**
	 * URL of a Nominatim instance
	 * @var string
	 */
	const NOMINATIM_API = 'http://nominatim.openstreetmap.org/';
	/**
	 * Max number of results retrieved in a Nominatim request
	 * @var int
	 */
	const NOMINATIM_NB_RESULTS = 10;
	/**
	 * List of label renderings availables to toolserver.org
	 * @var array<string>
	 */
	public static $TOOLSERVER_LANGUAGES = array('aa','ab','ace','af','ak','als','am','an','ang','ar','arc','arz','as','ast','av','ay','az','ba','bar','bat-smg','bcl','be-x-old','be','bg','bh','bi','bjn','bm','bn','bo','bpy','br','bs','bug','bxr','ca','cbk-zam','cdo','ce','ceb','ch','cho','chr','chy','ckb','co','cr','crh','cs','csb','cu','cv','cy','da','de','diq','dsb','dv','dz','ee','el','eml','en','eo','es','et','eu','ext','fa','ff','fi','fiu-vro','fj','fo','fr','frp','frr','fur','fy','ga','gan','gcf','gd','gl','glk','gn','got','gsw','gu','gv','ha','hak','haw','he','hi','hif','ho','hr','hsb','ht','hu','hy','hz','ia','id','ie','ig','ii','ik','ilo','io','is','it','iu','ja','jbo','jv','ka','kaa','kab','kg','ki','kj','kk','kl','km','kn','ko','koi','kr','krc','ks','ksh','ku','kv','kw','ky','la','lad','lb','lbe','lg','li','lij','lmo','ln','lo','lt','lv','map-bms','mdf','mg','mh','mhr','mi','mk','ml','mn','mo','mr','mrj','ms','mt','mus','mwl','my','myv','mzn','na','nah','nap','nds-nl','nds','ne','new','ng','nl','nn','no','nov','nrm','nv','ny','oc','om','or','os','pa','pag','pam','pap','pcd','pdc','pi','pih','pl','pms','pnb','pnt','ps','pt','qu','rm','rmy','rn','ro','roa-rup','roa-tara','ru','rw','sa','sah','sc','scn','sco','sd','se','sg','sh','si','simple','sk','sl','sm','sn','so','sq','sr','srn','ss','st','stq','su','sv','sw','szl','ta','te','tet','tg','th','ti','tk','tl','tn','to','tpi','tr','ts','tt','tum','tw','ty','udm','ug','uk','ur','uz','ve','vec','vi','vls','vo','wa','war','wo','wuu','xal','xh','yi','yo','za','zea','zh-classical','zh-min-nan','zh-yue','zh','zu');
	/**
	 * List of proposed langagues and their english names
	 * @var array<string=>string>
	 */
	public static $LANGUAGE_CODES = array('aa'=>'Afar','ab'=>'Abkhazian','ae'=>'Avestan','af'=>'Afrikaans','ak'=>'Akan','am'=>'Amharic','an'=>'Aragonese','ar'=>'Arabic','as'=>'Assamese','av'=>'Avaric','ay'=>'Aymara','az'=>'Azerbaijani','ba'=>'Bashkir','be'=>'Belarusian','bg'=>'Bulgarian','bh'=>'Bihari','bi'=>'Bislama','bm'=>'Bambara','bn'=>'Bengali','bo'=>'Tibetan','br'=>'Breton','bs'=>'Bosnian','ca'=>'Catalan','ce'=>'Chechen','ch'=>'Chamorro','co'=>'Corsican','cr'=>'Cree','cs'=>'Czech','cu'=>'Old Church Slavonic','cv'=>'Chuvash','cy'=>'Welsh','da'=>'Danish','de'=>'German','dv'=>'Divehi','dz'=>'Dzongkha','ee'=>'Ewe','el'=>'Greek','en'=>'English','eo'=>'Esperanto','es'=>'Spanish','et'=>'Estonian','eu'=>'Basque','fa'=>'Persian','ff'=>'Fulah','fi'=>'Finnish','fj'=>'Fijian','fo'=>'Faroese','fr'=>'French','fy'=>'Western Frisian','ga'=>'Irish','gd'=>'Scottish Gaelic','gl'=>'Galician','gn'=>'Guarani','gu'=>'Gujarati','gv'=>'Manx','ha'=>'Hausa','he'=>'Hebrew','hi'=>'Hindi','ho'=>'Hiri Motu','hr'=>'Croatian','ht'=>'Haitian','hu'=>'Hungarian','hy'=>'Armenian','hz'=>'Herero','ia'=>'Interlingua','id'=>'Indonesian','ie'=>'Interlingue','ig'=>'Igbo','ii'=>'Sichuan Yi','ik'=>'Inupiaq','io'=>'Ido','is'=>'Icelandic','it'=>'Italian','iu'=>'Inuktitut','ja'=>'Japanese', 'ja_rm'=>'Romanised Japanese', 'ja_kana' => 'Kana Japanese','jv'=>'Javanese','ka'=>'Georgian','kg'=>'Kongo','ki'=>'Kikuyu','kj'=>'Kwanyama','kk'=>'Kazakh','kl'=>'Kalaallisut','km'=>'Khmer','kn'=>'Kannada','ko'=>'Korean','kr'=>'Kanuri','ks'=>'Kashmiri','ku'=>'Kurdish','kv'=>'Komi','kw'=>'Cornish','ky'=>'Kirghiz','la'=>'Latin','lb'=>'Luxembourgish','lg'=>'Ganda','li'=>'Limburgish','ln'=>'Lingala','lo'=>'Lao','lt'=>'Lithuanian','lu'=>'Luba-Katanga','lv'=>'Latvian','mg'=>'Malagasy','mh'=>'Marshallese','mi'=>'Māori','mk'=>'Macedonian','ml'=>'Malayalam','mn'=>'Mongolian','mo'=>'Moldavian','mr'=>'Marathi','ms'=>'Malay','mt'=>'Maltese','my'=>'Burmese','na'=>'Nauru','nb'=>'Norwegian Bokmål','nd'=>'North Ndebele','ne'=>'Nepali','ng'=>'Ndonga','nl'=>'Dutch','nn'=>'Norwegian Nynorsk','no'=>'Norwegian','nr'=>'South Ndebele','nv'=>'Navajo','ny'=>'Chichewa','oc'=>'Occitan','oj'=>'Ojibwa','om'=>'Oromo','or'=>'Oriya','os'=>'Ossetian','pa'=>'Panjabi','pi'=>'Pāli','pl'=>'Polish','ps'=>'Pashto','pt'=>'Portuguese','qu'=>'Quechua','rm'=>'Raeto-Romance','rn'=>'Kirundi','ro'=>'Romanian','ru'=>'Russian','rw'=>'Kinyarwanda','sa'=>'Sanskrit','sc'=>'Sardinian','sd'=>'Sindhi','se'=>'Northern Sami','sg'=>'Sango','si'=>'Sinhalese','sk'=>'Slovak','sl'=>'Slovene','sm'=>'Samoan','sn'=>'Shona','so'=>'Somali','sq'=>'Albanian','sr'=>'Serbian','ss'=>'Swati','st'=>'Sotho','su'=>'Sundanese','sv'=>'Swedish','sw'=>'Swahili','ta'=>'Tamil','te'=>'Telugu','tg'=>'Tajik','th'=>'Thai','ti'=>'Tigrinya','tk'=>'Turkmen','tl'=>'Tagalog','tn'=>'Tswana','to'=>'Tonga','tr'=>'Turkish','ts'=>'Tsonga','tt'=>'Tatar','tw'=>'Twi','ty'=>'Tahitian','ug'=>'Uighur','uk'=>'Ukrainian','ur'=>'Urdu','uz'=>'Uzbek','ve'=>'Venda','vi'=>'Vietnamese','vo'=>'Volapük','wa'=>'Walloon','wo'=>'Wolof','xh'=>'Xhosa','yi'=>'Yiddish','yo'=>'Yoruba','za'=>'Zhuang','zh'=>'Chinese','zu'=>'Zulu');
	/**
	 * List of name-related tags an their labels in english
	 * @var array<string=>string>
	 */
	public static $NAME_FIELDS = array(
		'old_name'=>'Old name',
		'alt_name'=>'Alternative name',
		'official_name'=>'Official name',
		'loc_name'=>'Local name'
		);
	/**
	 * Identifying number for Nomino on api.openstreetmap.org
	 * @var string
	 */
	const OAUTH_CONSUMER_KEY = 'eEn7SBt1FBIF9TfT1o9T63OAcgm8O4A48WFoEMbX';
	/**
	 * Oauth application secret
	 * @var string
	 */
	const OAUTH_CONSUMER_SECRET = 'UfbdRaMeUgf3rqiqocDEN6Z1F6GXexlCrj8WKF7q';
	/**
	 * Keys for user preferences
	 * @var array of string
	 */
	public static $PREF_NAMES = array('map','prefLang');
	/**
	 * List of PHP translations
	 * @var array of string
	 */
	public static $UI_LANGUAGUES = array('fr'=>'fr_FR.utf8');
}
