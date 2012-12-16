<?php
/**
 * Nomino is a place name internationalization application for OpenStreetMap.
 * Here is the main file.
 * @author manud https://gitorious.org/~manud
 */

require_once('conf.php');
require_once('common.inc.php');

$locale = new LanguageSupport();
$locale->initGettext();

require_once('osmApi.inc.php');

try{
	$user = $osmApi->getUserDetails();
}catch( OSM_Exception $e)
{
	$user = null ;
}

?>
<html>
	<head>
		<title>Nomino</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link type="text/css" href="lib/jQuery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
		<script type="text/javascript" src="lib/jQuery/js/jquery-1.7.1.min.js"></script>
		<script src="lib/jQuery/js/jquery-ui-1.8.18.custom.min.js"></script>
		<script src="lib/jQuery/js/jquery.cookie.js"></script>
		<script src="lib/OpenLayers/OpenLayers.js"></script>
		<link href='http://fonts.googleapis.com/css?family=Metamorphous' rel='stylesheet' type='text/css'>
		<link href='css/style.css' rel='stylesheet' type='text/css'>

		<script src="js/ui.js"></script>
		<script src="js/find_places.js"></script>
		<script src="js/edit.js"></script>
		<script src="js/changeset.js"></script>
		<?php
		$langCodeJS = "";
		foreach (Conf::$LANGUAGE_CODES as $k => $v)
		{
			$langCodeJS .= ($langCodeJS == "" ? "" : ",") . "'$k':'$v'";
		}
		$altNames = "";
		foreach (Conf::$NAME_FIELDS as $k => $v)
		{
			$altNames .= ($altNames == "" ? "" : ",") . "'$k':'$v'";
		}
		?> 
		<script type="text/javascript">
			var LANGUAGE_CODES = {<?php echo $langCodeJS; ?>};
			var NAME_FIELDS = {<?php echo $altNames; ?>};
			var ISO639 = [];
			for(var code in LANGUAGE_CODES)ISO639.push(code);
			<?php 
				if($osmApi->getCredentials()->hasAccessToken() && $osmApi->isAllowedToReadPrefs()){
					foreach(Conf::$PREF_NAMES as $key){
						$cookVal = "";
						$userPrefs = $osmApi->getUserPreferences();
                        if(array_key_exists($key,$osmApi->getUserPreferences()))$cookVal = $userPrefs[$key];
						echo '$.cookie("'.$key.'","'.$cookVal.'");';
					}	 
				}?>
		</script>
		<?php if (isset($_GET["osm_type"]) && isset($_GET["osm_id"]))
		{
			?>
			<script>
				$(function(){
					beginEdit('<?php echo $_GET["osm_type"] ?>','<?php echo $_GET["osm_id"] ?>');
				});
			</script>
<?php } ?>
		<?php require("jsstrings.php");?>
	</head>
	<body>

		<!-- Dialogs -->
		<div id="waitDialog" style="display: none"><div id="progressbar"></div></div>
		<div id="authDialog" style="display: none">
			<p><?php echo _("To save changes in OSM database you have to be authenticated at osm.org.");?></p>
			<p><?php echo _("After authentification you'll be redirected back here.");?></p>
		</div>
		<div id="selectPlaceDialog" style="display: none" title="<?php echo _("Choose a place");?>"><form name="selectPlace"></form></div>
		<div id="preferencesDialog" style="display:none" title="<?php echo _("Preferences");?>">
			<form name="preferencesForm">
				<h3><?php echo _("Map");?></h3>
				<p>
				    <input type="radio" id="radioPrefOSM" name="mapLayer"> <label for="radioPrefOSM"><?php echo _("Default OpenStreetMap");?></label><br>
					<input type="radio" id="radioPrefMapquest" name="mapLayer"> <label for="radioPrefMapquest"><?php echo _("Mapquest");?></label>
					<br><input type="radio" id="radioPrefToolserver" name="mapLayer"> 
					<label for="radioPrefToolserver"><?php echo _("Toolserver localised maps");?></label>
					<select id="selectPrefMapLang" disabled="disabled"><?php
					foreach (Conf::$TOOLSERVER_LANGUAGES as $lang)
					{
						echo '<option value="' . $lang . '">' . $lang . (array_key_exists($lang, Conf::$LANGUAGE_CODES) ? (" (" . Conf::$LANGUAGE_CODES[$lang] . ")") : "") . '</option>';
					}
					?></select>
					<br><input type="radio" id="radioPrefMLM" name="mapLayer" value="_"> <label for="radioPrefMLM"><?php echo _("jochentopf.com multilingual maps");?></label>
					<input type="text" id="textPrefMLM" disabled="disabled">
					<blockquote><?php echo _("Specify a list of languages with the separator '|' (ie <i>_|de|ru</i>); '_' is for the <i>name</i> attribute");?></blockquote>
				</p>
				<h3><?php echo _("Preferred language");?></h3>
				<p><input type="checkbox" id="checkPrefAutoTrans">
					<label for="checkPrefAutoTrans"><?php echo _("When editing a place, add automically a field for translating into this language");?></label>
					<input type="text" id="textPrefLanguage" size="3" maxlength="3" disabled="disabled"></p>
				<h3><?php echo _("Suggestion");?></h3>
				<p><input type="checkbox" id="checkPrefSuggestions">
				<label for="checkPrefSuggestions"><?php echo _("Get translation suggestions when an object is opened");?></label></p>
			</form>
		</div>
		<div id="tabNavButtons" style="display:none" title="TabNavButtons">

			<?php
			if( $user == null ){
				?><a href="javascript:osmAuth()" style="float:right"><?php echo _("Login with OSM");?></a><?php
			}else{
				echo '<span style="float:right">'.$user->getName().'</span>';
			}
			?>
			<a href="javascript:showPreferences()" style="float:right"> <?php echo _("Preferences");?></a>
			<a href="javascript:showPreferences()" style="float:right"><img src="img/prefs.png"/></a>
		</div>

		<div id="mainContainer">
			<div id="appTitle">OpenStreetMap Nomino<span id="subtitle1"><?php echo _("(verb, latin)");?></span> <span id="subtitle2"><?php echo _("I name");?></span></div>
			<div id="tabs">
				<ul>
					<li><a href="#tabs-1"><?php echo _("Find Places");?></a></li>
					<li><a href="#tabs-2"><?php echo _("Translate");?></a></li>
					<li><a href="#tabs-3"><?php echo _("View changes");?></a></li>
					<li><a href="#tabs-4"><?php echo _("Documentation");?></a></li>
				</ul>
				<div id="tabs-1"><!-- Find places -->
					<div id="radio_find_mode">
						<input type="radio" id="radio_find_mode1" name="radio" checked="checked" /><label for="radio_find_mode1"><?php echo _("Find by name");?></label>
						<input type="radio" id="radio_find_mode2" name="radio"/><label for="radio_find_mode2"><?php echo _("OSM Object");?></label>
					</div>
					<div id="find_mode_1">
						<form name="findPlace" action="javascript:search_for_places();">
							<input type="text" name="query" id="query">
							<input type="submit" value="<?php echo _("Search");?>" id="button_find_places">
						</form>
						<div id="map_find_places"  style="float:left">
						</div>
						<div id="list_find_places"><ul><li><?php echo _("Right-click the map to select places");?></li></ul></div>
					</div>
					<div id="find_mode_2" style="display:none">
						<form name="findOSMObject" action="javascript:search_for_osm_object();">
							<select name="search_osm_type" id="search_osm_type">
								<option value="node"><?php echo _("Node");?></option>
								<option value="way"><?php echo _("Way");?></option>
								<option value="relation"><?php echo _("Relation");?></option>
							</select>
							<input type="text" name="search_osm_id" id="search_osm_id">
							<input type="submit" value="<?php echo _("Search");?>" id="button_find_places">
						</form>
					</div>
				</div>
				<div id="tabs-2"><!-- Translate -->
					<form name="editNames" action="javascript:saveObject()">
						<h3><?php echo _("Names");?> <input type="submit" value="<?php echo _("Save");?>" id="button_save_edit"></h3>
						<table id="table_names">
							<tr>
								<td><?php echo _("Name");?></td>
								<td><input type="text" id="edit_name" class="name_edit" name="name"></td>
							</tr>
							<tr id="row_edit_old_name">
								<td><?php echo _("Old name");?></td>
								<td><input type="text" id="edit_old_name" class="name_edit" name="old_name"></td>
								<td><a href="javascript:hideNameField('old_name')"><img src="img/delete.png"></td></td>
							</tr>
							<tr id="row_edit_alt_name">
								<td><?php echo _("Alternative name");?></td>
								<td><input type="text" id="edit_alt_name" class="name_edit" name="alt_name"></td>
								<td><a href="javascript:hideNameField('alt_name')"><img src="img/delete.png"></td></td>
							</tr>
							<tr id="row_edit_official_name">
								<td><?php echo _("Official name");?></td>
								<td><input type="text" id="edit_official_name" class="name_edit" name="official_name"></td>
								<td><a href="javascript:hideNameField('official_name')"><img src="img/delete.png"></td></td>
							</tr>
							<tr id="row_edit_loc_name">
								<td><?php echo _("Local name");?></td>
								<td><input type="text" id="edit_loc_name" class="name_edit" name="loc_name"></td>
								<td><a href="javascript:hideNameField('loc_name')"><img src="img/delete.png"></td></td>
							</tr>
						</table>
										<p id="link_add_tr"><a href="javascript:addLine()"><img src="img/add.png"> <?php echo _("Add translation");?></a>
										<a href="javascript:displayNameField('old_name','')" id="link_set_old_name"><img src="img/add.png"> <?php echo _("Set old name");?></a>
										<a href="javascript:displayNameField('alt_name','')" id="link_set_alt_name"><img src="img/add.png"> <?php echo _("Set alternative name");?></a>
										<a href="javascript:displayNameField('official_name','')" id="link_set_official_name"><img src="img/add.png"> <?php echo _("Set official name");?></a>
										<a href="javascript:displayNameField('loc_name','')" id="link_set_loc_name"><img src="img/add.png"> <?php echo _("Set local name");?></a>
										</p>
										<p id="proposals" style="display:none"></p>
										<h3><?php echo _("Other tags");?> <a href="#" target="_blank" id="linkOsmObject"><?php echo _("(View OSM Object)");?></a></h3>
										<table id="table_other_tags">
										</table>
										</form>
										</div>
										<div id="tabs-3"><!-- View changes -->
											<h3><?php echo _("Changes ");?>
												<input type="submit" value="<?php echo _("Submit changes");?>" id="button_submit_changes" class="changesetButton" disabled="disabled" onclick="submitChanges()">
												<input type="submit" value="<?php echo _("Download OSM file");?>" id="button_download_changes" class="changesetButton" disabled="disabled" onclick="downloadOsmFile()">
											</h3>
											<div id="table_changes">
												<ul></ul>
											</div>
										</div>
										<div id="tabs-4"><!-- Documentation -->
											<?php $locale->printDoc();?>
										</div>
										</div>
										</div>
										</body>
										</html>
