/**
 * UI instanciation
 */

var map_find_places = null;
var layer_find_places = null;

var iconPlace = OpenLayers.Util.extend({}, OpenLayers.Feature.Vector.style['default']);
iconPlace.graphicWidth = 20;
iconPlace.graphicHeight = 20;
iconPlace.graphicXOffset = -iconPlace.graphicHeight/2; 
iconPlace.graphicYOffset = -iconPlace.graphicHeight/2;
iconPlace.graphicOpacity = 1;
iconPlace.pointRadius = 6;

function LonLatToPoint(ll)
{
	return new OpenLayers.Geometry.Point(ll.lon,ll.lat);
}


/**
 * Mercator vers longitude/latitude
 */
function MToLonLat(ll)
{
	return ll.transform(new OpenLayers.Projection("EPSG:900913"),new OpenLayers.Projection("EPSG:4326"));  
}

/**
 * longitude/latitude vers Mercator
 */
function LonLatToM(ll)
{
	return ll.transform(new OpenLayers.Projection("EPSG:4326"),new OpenLayers.Projection("EPSG:900913"));  
}


function get_osm_url (bounds) {
  var res = this.map.getResolution();
  var x = Math.round ((bounds.left - this.maxExtent.left) / (res * this.tileSize.w));
  var y = Math.round ((this.maxExtent.top - bounds.top) / (res * this.tileSize.h));
  var z = this.map.getZoom();
  var limit = Math.pow(2, z);

  if (y < 0 || y >= limit)
    {
      return null;
    }
  else
    {
      return this.url + z + "/" + x + "/" + y + "." + this.type; 
    }
}

$(function() {
	$( "#tabs" ).tabs({
		show: function(event, ui){if(ui.index == 2)loadChangeset();}
	});
	$("#tabs").tabs("disable",1);
	
	map_find_places = new OpenLayers.Map("map_find_places",
	{                maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
		numZoomLevels:18,
        maxResolution: 156543.0399,
		units:'m',
		projection: new OpenLayers.Projection("EPSG:900913"),
		displayProjection: new OpenLayers.Projection("EPSG:4326"),
	       controls:[
   					new OpenLayers.Control.Navigation(),
					new OpenLayers.Control.PanZoom(),
			  ]
	});
	

	// Get control of the right-click event:
	document.getElementById('map_find_places').oncontextmenu = function(e){
		 e = e?e:window.event;
		 if (e.preventDefault) e.preventDefault(); // For non-IE browsers.
		 else return false; // For IE browsers.
	};

	// A control class for capturing click events...
	OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {                
		defaultHandlerOptions: {
		'single': true,
		'double': true,
		'pixelTolerance': 0,
		'stopSingle': false,
		'stopDouble': false
		},
		handleRightClicks:true,
		initialize: function(options) {
		this.handlerOptions = OpenLayers.Util.extend(
		{}, this.defaultHandlerOptions
		);
		OpenLayers.Control.prototype.initialize.apply(
		this, arguments
		); 
		this.handler = new OpenLayers.Handler.Click(
		this, this.eventMethods, this.handlerOptions
		);
		},
		CLASS_NAME: "OpenLayers.Control.Click"
	});


	// Add an instance of the Click control that listens to various click events:
	var oClick = new OpenLayers.Control.Click({eventMethods:{
		'rightclick': function(e) {
			$( "#waitDialog" ).dialog('open');
			var ll = MToLonLat(map_find_places.getLonLatFromPixel(e.xy));
			search_for_position(ll.lon,ll.lat);
		}
	}});
	map_find_places.addControl(oClick);
	oClick.activate();
	
	var mapquest = new OpenLayers.Layer.TMS( 
	          "MapQuest Tiles", 
	          ["http://otile1.mqcdn.com/tiles/1.0.0/osm/"],
	          {type:'png',
	          getURL: get_osm_url,
		  transitionEffect: 'resize',
	          displayOutsideMaxExtent: true }, {'buffer':0} );
	
	
	map_find_places.addLayer(mapquest);

	map_find_places.zoomTo(4);
	map_find_places.setCenter(LonLatToM(new OpenLayers.LonLat(2,47)));
	
	layer_find_places = new OpenLayers.Layer.Vector("places");
	map_find_places.addLayer(layer_find_places);
	
	
	layer_find_places.events.on({
	      "featureselected": function(feature) {
	    	  $("#list_find_places ul li").eq(feature.feature.attributes.numPlace).addClass("place_highlight");
	      },
	      "featureunselected": function(feature) {
	        $("#list_find_places ul li").removeClass("place_highlight");
	      }
	    });

    var selectControl = new OpenLayers.Control.SelectFeature(layer_find_places, {
      multiple: false,
      hover: true
    });
    map_find_places.addControl(selectControl);
    selectControl.activate();
    
    $("#button_find_places").button();
    $("#button_save_edit").button();
    $(".changesetButton").button();
    
    $( "#progressbar" ).progressbar({
		value: 100
	});
    $( "#waitDialog" ).dialog({
		height: 50,
		modal: true,
		resizable:false,
		autoOpen:false,
		closeOnEscape:false,
		dialogClass:'noTitle'
	});
    
    $("#radio_find_mode").buttonset();
    $("#radio_find_mode1").click(function(){$("#find_mode_1").show();$("#find_mode_2").hide();});
    $("#radio_find_mode2").click(function(){$("#find_mode_2").show();$("#find_mode_1").hide();});
    
//    $("#list_find_places ul li").hover(
//    		function(){$(this).addClass("place_highlight");},
//    		function(){$(this).removeClass("place_highlight");}
//    );
});