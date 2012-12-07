
/**
 * Function used to search for a named place
 * @param adress optional parameter, an adress search for, if it is undefined, the value of #query is used instead
 */
function search_for_places(address)
{
	// the "address" string is inserted into the text field, as if the user had actually typed it
	if(typeof(address) != 'undefined')
	{
		$("#query").val(address);
	}
	$.ajax({
		dataType:'text',
		url: "api/find.php",
		data:{'q':$("#query").val()},
		success: function(e){
			var reader = new OpenLayers.Format.KML({extractStyles : true});
			var places = reader.read(e);
			$("#list_find_places ul").html("");
			layer_find_places.removeAllFeatures();
			var markers = [];
			for(var numPlace = 0; numPlace < places.length; numPlace++)
			{
				// most place images are taken from Nominatim results
				var imageURL =  places[numPlace].attributes.icon?places[numPlace].attributes.icon:"img/marker.png";
				// list item
				if(places[numPlace].attributes.osm_type == "relation")
				{
					$("#list_find_places ul").append("<li onmouseover=\"$(this).addClass('place_highlight');map_find_places.setCenter(LonLatToM(new OpenLayers.LonLat("+places[numPlace].geometry.x+
							","+places[numPlace].geometry.y+")))\" onmouseout=\"$(this).removeClass('place_highlight');\"><img src=\""+imageURL+"\">"+places[numPlace].attributes.name+
							" <span class=\"placeDetails\">("+places[numPlace].attributes["class"]+","+places[numPlace].attributes.type+")</span>"+
							"<br>&nbsp;&nbsp;&nbsp;<a href=\"#\" id=\"linkNode"+places[numPlace].attributes.osm_id+"\">"+LANG.NODE_OBJ+"</a>"+
							"<br>&nbsp;&nbsp;&nbsp;<a href=\"javascript:beginEdit('relation',"+places[numPlace].attributes.osm_id+")\">"+LANG.RELATION_OBJ+"</a>"+
							"</li>");
				}
				else
					$("#list_find_places ul").append("<li onmouseover=\"$(this).addClass('place_highlight');map_find_places.setCenter(LonLatToM(new OpenLayers.LonLat("+places[numPlace].geometry.x+","+places[numPlace].geometry.y+")))\" onmouseout=\"$(this).removeClass('place_highlight');\"><img src=\""+imageURL+"\"><a href=\"javascript:beginEdit('"+places[numPlace].attributes.osm_type+"',"+places[numPlace].attributes.osm_id+")\">"+places[numPlace].attributes.name+"</a> <span class=\"placeDetails\">("+places[numPlace].attributes["class"]+","+places[numPlace].attributes.type+")</span></li>");
				// feature on the map
				var style = OpenLayers.Util.extend({}, iconPlace);
				style.externalGraphic = imageURL;
				var marker = new OpenLayers.Feature.Vector(LonLatToPoint(LonLatToM(new OpenLayers.LonLat(places[numPlace].geometry.x,places[numPlace].geometry.y))), null,style);
				marker.attributes = {
					"name" : places[numPlace].attributes.name,
					"numPlace":numPlace
				};
				markers.push(marker);
				
				if(places[numPlace].attributes.osm_type == "relation")
				{
					requestAdminCentre(places[numPlace].attributes.osm_id);
				}
			}
			
			markers.reverse();
			layer_find_places.addFeatures(markers);
			map_find_places.zoomToExtent(layer_find_places.getDataExtent());
		},
		error: function(){
			alert(LANG.ERROR_PLACES);
		}
    });
}

/**
 * Determine the admin_centre node of a boundary relation and update the link
 * @param relationId object id in OSM
 */
function requestAdminCentre(relationId)
{
	$.ajax({
		dataType:'text',
		url: "api/osm_iface.php",
		data:{
			'action':'adminCentre',
			'id':relationId
		},
		success:function(e){
			if(e == -1)
			{	// If the relation had no suitable node, the link is destroyed
				$("#linkNode"+relationId).remove();
			}
			else
			{
				$("#linkNode"+relationId).attr("href","javascript:beginEdit('node',"+e+")");
			}
		}
	});
}

/**
 * Search for an OSM object, given its type and id
 */
function search_for_osm_object()
{
	beginEdit($("#search_osm_type").val(),$("#search_osm_id").val());	
}

/**
 * Search for places related to a lon/lat point, and display a list of territories.
 * The user is then asked to choose a territory in the list.
 * @param lon
 * @param lat
 */
function search_for_position(lon,lat)
{
	$.ajax({
		dataType:'xml',
		url: "api/find.php",
		data:{
			'lon':lon,
			'lat':lat
		},
		success: function(e){
			var forbidden_admin_levels = ["country_code","postcode","house_number"];
			var adressLines = [];
			var previousQuery = "";
					
			var addressParts = $(e).find("addressparts").children();
			for(var numItem = addressParts.length-1;numItem >= 0; numItem--)
			{
				var key = addressParts[numItem].nodeName;
				if(forbidden_admin_levels.indexOf(key) == -1)
				{
					var value = addressParts.eq(numItem).text();
					previousQuery = value + (previousQuery!=""?(", "+previousQuery):""); 
					adressLines.push({
						type:key,
						name:value,
						query:previousQuery
					});
				}
			}
			
			var formHTML = "";
			$.each(adressLines,function(index,value){
				formHTML += '<input type="radio" name="place" id="selectPlace'+index+'" value="'+value.query+'"><label for="selectPlace'+index+'"><span class="placeDetails" style="width:100px">'+value.type+'</span> '+value.name+'</label><br>';
			});
			
			$("#selectPlaceDialog form").html(formHTML);
			
			$( "#selectPlaceDialog" ).dialog({
				modal: true,
				resizable:false,
				autoOpen:true,
				buttons:[{
						text:LANG.CHOOSE,
						click:function(){
							search_for_places($("#selectPlaceDialog input[name=place]:checked").val());
							$(this).dialog('close');
						}
					},{
						text:LANG.CANCEL,
						click:function(){
							$(this).dialog('close');
						}
					}
				]
			});
			
			//$( "#waitDialog" ).dialog('close');
		},
		error: function(){
			alert(LANG.ERROR_PLACES);
		}
    });
}
