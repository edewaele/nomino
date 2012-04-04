<?php include("conf.php"); ?>
<html>
<head>
<title>Nomino</title>
<link type="text/css" href="lib/jQuery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="lib/jQuery/js/jquery-1.7.1.min.js"></script>
<script src="lib/jQuery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script src="lib/OpenLayers/OpenLayers.js"></script>
<link href='http://fonts.googleapis.com/css?family=Metamorphous' rel='stylesheet' type='text/css'>
<link href='css/style.css' rel='stylesheet' type='text/css'>

<script src="js/ui.js"></script>
<script src="js/find_places.js"></script>
<script src="js/edit.js"></script>
<script src="js/changeset.js"></script>

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
			<p>Nomino is a specialised OpenStreetMap editor.</p>
		</div>
	</div>
</div>
</body>
</html>