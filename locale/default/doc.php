<p>Nomino is a specialised OpenStreetMap editor, intended to translate place names.</p>
<p>Unlike JOSM and others, Nomino is not designed to edit everything in OpenStreetMap.
	Instead, it provides a simpler interface, especially designed to new contributors.</p>
<h2>Find places</h2>
<p>Nomino does not download OSM in a whole area, but you may search for a place to edit.</p>
<p>Just type a place name or an address to search, then click "Search", 
	several OSM objects are proposed in a list on the right, and you can see them on the map.
	By clicking a place name, you can edit the object.</p>
<p>If you prefer choosing places on the map, you may right-click somewhere on it.
	The place you clicked is contained in a city, a county, a state, a country.
	These entities are displayed in a list, choose one of these items to edit the city, the county, ...</p>
<p> You may open an OSM object given its number.
 Click "OSM Object" button, the select the object type and the object number, then click "Search" to open it.</p>
<h2>Translate</h2>
<h2>Changes</h2>
<p>All changed objects are shown in the "Changes" tag, along with the tags you edited. Click the place name to open it again,
	or click the "Revert" button to discard the changes.</p>
<p>To save your changes in OpenStreetMap database, click "Submit Changes". An OpenStreetMap user account is required, and Nomino will need to a be authorized to edit OSM data.</p>
<p>The "Download OSM file" button returns an OpenStreetMap XML file, which you can open in JOSM or Merkaartor.</p>
<h2>User preferences</h2>
<p>The preferences dialog is useful to choose the map layer to used in "Find Places" tab.</p>
<p>Most users want to translates place names into their own language. The "Preferred languague" option defines a language that will always be displayed in the "Translate" tab; if no translation exists in this language, a new empty line is addded.</p>
<h2>About</h2>
<p>This application was developped by Emmanuel Dewaele.
	Nomino is free sotware released under the <a href="http://www.gnu.org/licenses/agpl-3.0.html">Affero General Public License</a>.
	You are free to contribute on <a href="http://gitorious.org/nomino">Gitorious</a>.</p>
<p>Acknowledgements:</p>
<ul>
	<li>Cyrille Giquello for <a href="https://github.com/Cyrille37/yapafo">yapafo</a>, the great OpenStreetMap-handling library</li>
	<li>Toolserver for the <a href="https://wiki.toolserver.org/view/OpenStreetMap">multilingual maps</a></li>
	<li><a href="http://nominatim.openstreetmap.org/">Nominatim</a>'s developers, for the geocoding API</li>
	<li>Mapquest for <a href="http://open.mapquest.com/">Open Mapquest</a> tiles</li>
</ul>