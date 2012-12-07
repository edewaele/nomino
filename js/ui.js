/**
 * UI instanciation
 */

// array of setss of map layers (osm, mapquest, multilingual...)
var layerSets = [];

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

OpenLayers.Layer.OSM.Toolserver = OpenLayers.Class(OpenLayers.Layer.OSM, {
	
	initialize: function(name, options) {
		var url = [
		"http://a.www.toolserver.org/tiles/" + name + "/${z}/${x}/${y}.png", 
		"http://b.www.toolserver.org/tiles/" + name + "/${z}/${x}/${y}.png", 
		"http://c.www.toolserver.org/tiles/" + name + "/${z}/${x}/${y}.png"
		];
		
		options = OpenLayers.Util.extend({
			numZoomLevels: 18
		}, options);
		OpenLayers.Layer.OSM.prototype.initialize.apply(this, [name, url, options]);
	},
	
	CLASS_NAME: "OpenLayers.Layer.OSM.Toolserver"
});

$(function() {
	
	$( "#tabs" ).tabs({
		show: function(event, ui){
			if(ui.index == 2)loadChangeset();
		}
	});
	$("#tabs").tabs("disable",1);
	
	map_find_places = new OpenLayers.Map("map_find_places",
	{   
		maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
		maxResolution: 156543.0399,
		numZoomLevels: 19,
		units:'m',
		projection: new OpenLayers.Projection("EPSG:900913"),
		displayProjection: new OpenLayers.Projection("EPSG:4326"),
		controls:[
		new OpenLayers.Control.Navigation(),
		new OpenLayers.Control.PanZoom()
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
	var oClick = new OpenLayers.Control.Click({
		eventMethods:{
			'rightclick': function(e) {
				$( "#waitDialog" ).dialog('open');
				var ll = MToLonLat(map_find_places.getLonLatFromPixel(e.xy));
				search_for_position(ll.lon,ll.lat);
				$('#waitDialog').dialog('close');
			}
		}
	});
	
	map_find_places.addControl(oClick);
	oClick.activate();
	
	layerOSM = new OpenLayers.Layer.OSM("Default OSM");
	map_find_places.addLayer(layerOSM);
	layerSets.push([layerOSM]);
	
	layerMapquest = new OpenLayers.Layer.OSM("MapQuest Tiles", ["http://otile1.mqcdn.com/tiles/1.0.0/osm/${z}/${x}/${y}.png"]);
	map_find_places.addLayer(layerMapquest);
	layerSets.push([layerMapquest]);
	
	layerNoLabels = new OpenLayers.Layer.OSM.Toolserver('osm-no-labels');
	map_find_places.addLayer(layerNoLabels);
	
	layerLang = new OpenLayers.Layer.OSM.Toolserver('osm-labels-en',{
		isBaseLayer:false
	});
	map_find_places.addLayer(layerLang);
	
	layerSets.push([layerNoLabels,layerLang]);
	
	layerMLMBackground = new OpenLayers.Layer.OSM("OSM.de bagkground", ["http://a.tile.openstreetmap.de:8002/tiles/1.0.0/bg//${z}/${x}/${y}.jpg","http://b.tile.openstreetmap.de:8002/tiles/1.0.0/bg//${z}/${x}/${y}.jpg"]);
	map_find_places.addLayer(layerMLMBackground);
	
	layerMLMLabels = new OpenLayers.Layer.OSM("MLM Labels", ["http://c.tile.openstreetmap.de:8002/tiles/1.0.0/labels/_/${z}/${x}/${y}.png"],{
		isBaseLayer:false
	});
	map_find_places.addLayer(layerMLMLabels);
	layerSets.push([layerMLMBackground,layerMLMLabels]);
	
	updateMapDisplay();

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
	$("#radio_find_mode1").click(function(){
		$("#find_mode_1").show();
		$("#find_mode_2").hide();
	});
	$("#radio_find_mode2").click(function(){
		$("#find_mode_2").show();
		$("#find_mode_1").hide();
	});
    

	$( "#preferencesDialog" ).dialog({
		width:500,
		modal: true,
		resizable:false,
		autoOpen:false,
		closeOnEscape:true,
		buttons:[{
		         text: LANG.SAVE,
		         click:function(){
					savePreferences();
					$(this).dialog('close');
				}
			},{
		         text: LANG.CANCEL,
		         click:function(){
					$(this).dialog('close');
				}
			}
		]
	});
	
	$("input[name='mapLayer']").change(function(){
		if($("input[name='mapLayer']:checked").attr("id") == "radioPrefToolserver")
			$("#selectPrefMapLang").removeAttr("disabled");
		else 
			$("#selectPrefMapLang").attr("disabled","disabled");
		if($("input[name='mapLayer']:checked").attr("id") == "radioPrefMLM")
			$("#textPrefMLM").removeAttr("disabled");
		else 
			$("#textPrefMLM").attr("disabled","disabled");
	});
	$("#checkPrefAutoTrans").click(function(){
		if($("#checkPrefAutoTrans").attr("checked") == "checked")
			$("#textPrefLanguage").removeAttr("disabled");
		else 
			$("#textPrefLanguage").attr("disabled","disabled");
	});
	$("#textPrefLanguage").autocomplete({
		source:ISO639
	});
    
	// remove html from javascript and put it into html part
	$("#tabs .ui-tabs-nav").append( $('#tabNavButtons').show() );

//    $("#list_find_places ul li").hover(
//    		function(){$(this).addClass("place_highlight");},
//    		function(){$(this).removeClass("place_highlight");}
//    );

});

/**
 * Display the preferences dialog and set the fields according to the settings
 */
function showPreferences()
{
	switch($.cookie("map")*1)
	{
		case 0: $("#radioPrefOSM").attr("checked","checked"); break;
		case 1: $("#radioPrefMapquest").attr("checked","checked"); break;
		case 2: $("#radioPrefToolserver").attr("checked","checked"); break;
		case 3: $("#radioPrefMLM").attr("checked","checked"); break;
	}
	if($.cookie("map") != 2)
	{
		$("#selectPrefMapLang").attr("disabled","disabled");
	}
	else
	{
		$("#selectPrefMapLang").val($.cookie("toolserverLang"));
		$("#selectPrefMapLang").removeAttr("disabled");
	}
	if($.cookie("map") != 3)
	{
		$("#textPrefMLM").attr("disabled","disabled");
	}
	else
	{
		$("#textPrefMLM").removeAttr("disabled");
	}
	$("#textPrefMLM").val($.cookie("langMLM"));
	if($.cookie("prefLang"))
	{
		$("#checkPrefAutoTrans").attr("checked","checked");
		$("#textPrefLanguage").val($.cookie("prefLang"));
		$("#textPrefLanguage").removeAttr("disabled");
	}
	else
	{
		$("#checkPrefAutoTrans").removeAttr("checked");
		$("#textPrefLanguage").attr("disabled","disabled");
	}
	$( "#preferencesDialog" ).dialog("open");
}

/**
 * Triggered when the user saves the preferences 
 */
function savePreferences()
{
	if($("input[name='mapLayer']:checked").attr("id") == "radioPrefToolserver")
	{
		$.cookie("map",2);
		$.cookie("toolserverLang",$("#selectPrefMapLang").val());
	}
	else if($("input[name='mapLayer']:checked").attr("id") == "radioPrefMLM")
	{
		$.cookie("map",3);
		$.cookie("langMLM",$("#textPrefMLM").val());
	}
	else if($("input[name='mapLayer']:checked").attr("id") == "radioPrefOSM")
		$.cookie("map",0);
	else if($("input[name='mapLayer']:checked").attr("id") == "radioPrefMapquest")
		$.cookie("map",1);
	else 
		$.cookie("map","");
	if($("#checkPrefAutoTrans").attr("checked") == "checked")
		$.cookie("prefLang",$("#textPrefLanguage").val());
	else
		$.cookie("prefLang","");
	$.cookie("langMLM",$("#textPrefMLM").val());
	
	updateMapDisplay();
	
	// save preferences in the OSM server
	var keys = {'map':1,'prefLang':1,'toolserverLang':1,'langMLM':1};
	for(var key in keys)
	{
		$.ajax({
			dataType:'text',
			url: "api/osm_iface.php",
			data:{
				'action':'savePref',
				'k':key,
				'v':$.cookie(key)
			},
			success: function(e){
			},
			error: function(e){alert(LANG.ERROR_PREF);}
	    });
	}
}

/**
 * Update the map layer, Mapquest or Toolserver + language
 */
function updateMapDisplay()
{
	for(var numLayerSet = 0; numLayerSet < layerSets.length; numLayerSet++)
	{
		if($.cookie("map") == numLayerSet)
		{
			map_find_places.setBaseLayer(layerSets[numLayerSet][0]);
			if(layerSets[numLayerSet].length > 1)
				layerSets[numLayerSet][1].setVisibility(true);
		}
		else
		{
			if(layerSets[numLayerSet].length > 1)
				layerSets[numLayerSet][1].setVisibility(false);
		}
	}
	if($.cookie("map") == 2)
	{
		layerLang.url = "http://a.www.toolserver.org/tiles/osm-labels-"+$.cookie("toolserverLang")+"/${z}/${x}/${y}.png";
		layerLang.redraw();
	}
	if($.cookie("map") == 3)
	{
		layerMLMLabels.url = "http://c.tile.openstreetmap.de:8002/tiles/1.0.0/labels/"+($.cookie("langMLM")!=""?$.cookie("langMLM"):"_")+"/${z}/${x}/${y}.png";
		layerMLMLabels.redraw();
	}
}

function osmAuth()
{
	url = 'api/osm_iface.php?action=osmOAuth' ;

	$('#authDialog').dialog({
		height: 350,
		width:500,
		modal: true,
		resizable:false,
		autoOpen:true,
		closeOnEscape:true,
		// add a close listener to prevent adding multiple divs to the document
		close: function(event, ui) {
		// remove div with all data and events
		//$(this).remove();
		},
		buttons: [
		{
			text: LANG.CANCEL,
			click: function() {
				$(this).dialog("close");
			}
		},
		{
			text: LANG.AUTHENTICATE,
			click: function() {
				window.location = url ;
			}
		}
		]
	});

}
