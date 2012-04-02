
/**
 * Refresh the "View changes" tab, queries the API to get the changes list
 */
function loadChangeset()
{
	$.ajax({
		dataType:'text',
		url: "api/osm_iface.php",
		data:{
			'action':'list'
		},
		success: function(e){
			$("#table_changes ul").empty();
			$(e).find('Object').each(function () {
		    	$("#table_changes ul").append("<li><a href=\"javascript:beginEdit('"+$(this).attr("osm_type")+"',"+$(this).attr("osm_id")+")\">"+$(this).attr("name")+"</a><a href=\"javascript:revertChange('"+$(this).attr("osm_type")+"',"+$(this).attr("osm_id")+")\"><img src=\"img/revert.png\" title=\"Revert\"></a><br><span class=\"dirtyTags\">"+$(this).attr("dirty_tags")+"</span></li>");
			});
			if($(e).find('Object').length > 0)
				$(".changesetButton").button('enable');
			else 
				$(".changesetButton").button('disable');
		},
		error: function(e){alert('Error while retrieving OSM object');}
    });
}

/**
 * Revert all changes to a specified object
 * @param type string node/way/relation
 * @param id int osm object id
 */
function revertChange(type,id)
{
	$( "#waitDialog" ).dialog('open');
	$.ajax({
		dataType:'text',
		url: "api/osm_iface.php",
		data:{
			'action':'revert',
			'type':type,
			'id':id
		},
		success: function(e){
			$( "#waitDialog" ).dialog('close');
			loadChangeset();
		},
		error: function(e){alert('Error while retrieving OSM object');}
    });
}