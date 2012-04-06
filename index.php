<?php include("conf.php"); ?>
<html>
<head>
<title>Nomino</title>
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
foreach(Conf::$LANGUAGE_CODES as $k => $v){
	$langCodeJS .= ($langCodeJS==""?"":",") . "'$k':'$v'";
}
?> 
<script type="text/javascript">
var LANGUAGE_CODES = {<?php echo $langCodeJS;?>};
var ISO639 = [];
for(var code in LANGUAGE_CODES)ISO639.push(code);
</script>
<?php if(isset($_GET["osm_type"]) && isset($_GET["osm_id"])){?>
<script>
$(function(){
	beginEdit('<?php echo $_GET["osm_type"]?>','<?php echo $_GET["osm_id"]?>');
});
</script>
<?php }?>
</head>
<body>

<!-- Dialogs -->
<div id="waitDialog" style="display: none"><div id="progressbar"></div></div>
<div id="selectPlaceDialog" style="display: none"><form name="selectPlace"></form></div>
<div id="preferencesDialog" style="display:none" title="Preferences">
	<form name="preferencesForm">
		<h3>Map</h3>
		<p>Choose the map layer<br>
		<input type="radio" id="radioPrefMapquest" name="mapLayer"> <label for="radioPrefMapquest">Mapquest</label>
		<br><input type="radio" id="radioPrefToolserver" name="mapLayer"> 
		<label for="radioPrefToolserver">Toolserver localised maps
		<select id="selectPrefMapLang" disabled="disabled"><?php foreach(Conf::$TOOLSERVER_LANGUAGES as $lang){echo '<option value="'.$lang.'">'.$lang.(array_key_exists($lang,Conf::$LANGUAGE_CODES)?(" (".Conf::$LANGUAGE_CODES[$lang].")"):"").'</option>';}?></select></label></p>
		<h3>Preferred language</h3>
		<p><input type="checkbox" id="checkPrefAutoTrans">
		<label for="checkPrefAutoTrans">When editing a place, add automically a field for translating into this language</label>
		<input type="text" id="textPrefLanguage" size="3" maxlength="3" disabled="disabled"></p>
	</form>
</div>

<div id="mainContainer">
	<div id="appTitle">OpenStreetMap Nomino<span id="subtitle1">(verb, latin)</span> <span id="subtitle2">I name</span></div>
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1">Find Places</a></li>
			<li><a href="#tabs-2">Translate</a></li>
			<li><a href="#tabs-3">View changes</a></li>
			<li><a href="#tabs-4">Documentation</a></li>
		</ul>
		<div id="tabs-1">
			<div id="radio_find_mode">
				<input type="radio" id="radio_find_mode1" name="radio" checked="checked" /><label for="radio_find_mode1">Find by name</label>
				<input type="radio" id="radio_find_mode2" name="radio"/><label for="radio_find_mode2">OSM Object</label>
			</div>
			<div id="find_mode_1">
				<form name="findPlace" action="javascript:search_for_places();">
					<input type="text" name="query" id="query">
					<input type="submit" value="Search" id="button_find_places">
				</form>
				<div id="map_find_places"  style="float:left">
				</div>
				<div id="list_find_places"><ul><li>Right-click the map to select places</li></ul></div>
			</div>
			<div id="find_mode_2" style="display:none">
				<form name="findOSMObject" action="javascript:search_for_osm_object();">
					<select name="search_osm_type" id="search_osm_type">
						<option value="node">Node</option>
						<option value="way">Way</option>
						<option value="relation">Relation</option>
					</select>
					<input type="text" name="search_osm_id" id="search_osm_id">
					<input type="submit" value="Search" id="button_find_places">
				</form>
			</div>
		</div>
		<div id="tabs-2">
			<form name="editNames" action="javascript:saveObject()">
				<h3>Names <input type="submit" value="Save" id="button_save_edit"></h3>
				<table id="table_names">
					<tr>
						<td>Name</td>
						<td><input type="text" id="edit_name" class="name_edit" name="name"></td>
					</tr>
				</table>
				<p id="link_add_tr"><a href="javascript:addLine()"><img src="img/add.png"> Add translation</a></p>
				<h3>Other tags</h3>
				<table id="table_other_tags">
				</table>
			</form>
		</div>
		<div id="tabs-3">
			<h3>Changes 
			<input type="submit" value="Submit changes" id="button_submit_changes" class="changesetButton" disabled="disabled" onclick="submitChanges()">
			<input type="submit" value="Download OSM file" id="button_download_changes" class="changesetButton" disabled="disabled" onclick="downloadOsmFile()">
			</h3>
			<div id="table_changes">
			<ul></ul>
			</div>
		</div>
		<div id="tabs-4">
			<p>Nomino is a specialised OpenStreetMap editor, intended to translated place names.</p>
			<p>Unlike JOSM and others, Nomino is not designed to edit everything in OpenStreetMap.
			Instead, it provides a simpler interface, usable by beginner contributors.</p>
			<h2>Find places</h2>
			<p>Nomino does not download OSM in a whole area, but you may search for a place to edit.</p>
			<p>Just type a place name or an address to search, then click "Search", 
			several OSM objects are proposed in a list on the right, and you can see them on the map.
			By clicking a place name, you can edit the object.</p>
			<p>If you prefer choosing places on the map, you may right-click somewhere on it.
			The place you clicked is contained in a city, a county, a state, a country.
			These territories are displayed in a list, choose one of these items to edit the city, the county, ...</p>
			<p>It is possible to find an object by its number, knowing the object type (node, way, relation),
			and its id. Cick "OSM Object" button, the select the object type and the object number, then click "Search" to open it.</p>
			<h2>Translate</h2>
			<h2>Changes</h2>
			<p>All changed objects are shown in a list, with the tags you have edited. Click the place name to edit it again,
			or click the "Revert" button to discard the changes.</p>
			<p>The "Download OSM file" button returns an OpenStreetMap XML file, which you can open in JOSM or Merkaartor.
			The changes are not sent to OpenStreetMap.</p>
			<p>The "Submit changes" button allows to upload your edits to OpenStreetMap server.</p>
		</div>
	</div>
</div>
</body>
</html>