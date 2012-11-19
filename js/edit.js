
var objectNames = {};// object of tag keys and values in the editing environment
var rowLanguages = {};// array of tags keys in the form
var numAltName = 0;
var typingLanguage = false;
var editedObject = {};// information abot the currenlty edited object

$(function(){	
	$("#button_save_edit").click(saveObject);
});

function setChangeEvents()
{
	$(".name_edit").unbind();
	$(".name_edit").change(function(){
		objectNames[$(this).attr("name")] = $(this).val();
	});
	// set the autocompletion for all edits fields, with existing values
	$(".name_edit").autocomplete("destroy");
	var proposedNames = [];
	$(".name_edit").each(function(){
		if(proposedNames.indexOf($(this).val()) == -1)
		{
			proposedNames.push($(this).val());
		}
	});
	$(".name_edit").autocomplete({source:proposedNames});
}

/**
 * Save the edits in the form
 */
function saveObject()
{
//	$( "#waitDialog" ).dialog('open');
	var reqParams = objectNames;
	reqParams.action = 'set';
	reqParams.id = editedObject.id;
	reqParams.type = editedObject.type;
	$.ajax({
		dataType:'text',
		url: "api/osm_iface.php",
		data:reqParams,
		success: function(e){
			if(e  == 1)
			{
				$("#tabs").tabs("enable",2);
				$("#tabs").tabs("select",2);
			}
			else
			{
				alert("Error while saving object");
			}
			$( "#waitDialog" ).dialog('close');
		},
		error: function(e){alert('Error while sending object data');}
    });
}

/**
 * Request to get a give OSM object from the API
 * and then display the edit form
 * @param type object type
 * @param id object id
 */
function beginEdit(type,id)
{
	$( "#waitDialog" ).dialog('open');
	$.ajax({
		dataType:'text',
		url: "api/osm_iface.php",
		data:{
			'action':'get',
			'type':type,
			'id':id
		},
		success: function(e){
			if($(e).find('tag').length == 0){
				$( "#waitDialog" ).dialog('close');
				alert("Invalid object");
				return;
			}
			editedObject.type = type;
			editedObject.id = id;
			objectNames = {};
			
			// All name fields are hidden
			for(var key in NAME_FIELDS)hideNameField(key);
			
			$("#table_other_tags").empty();
			$("#table_names .alternative").remove();
			numAltName = 0;
			
			// tags are sorted by keys
			var tags = {};// associatice array tag key => tags values
			var keys = [];// array of tag keys
			$(e).find('tag').each(function(){
				tags[$(this).attr("k")] = $(this).attr("v");
				keys.push($(this).attr("k"));
			});
			keys.sort();// keys are sorted
			
			for(var numKey in keys) {
				var key = keys[numKey];
				if(key in NAME_FIELDS)
			    {
			    	displayNameField(key,tags[key]);
			    }
				else if(key.substring(0,4) != "name")
			    {
			    	$("#table_other_tags").append("<tr><td>"+key+"</td><td>"+tags[key]+"</td></tr>")
			    }
			    else if(key == "name")
			    {
			    	$("#edit_name").val(tags[key]);
			    	objectNames["name"] = tags[key];
			    }
			    else
			    {
			    	var code = key.substring(5);
			    	var label = code;
					if(code in LANGUAGE_CODES)
						label += ' <span class="placeDetails">('+LANGUAGE_CODES[code]+')</span>';
			    	$("#table_names").append("<tr class=\"alternative\" id=\"alternative-"+numAltName+"\"><td>"+label+"</td>" +
			    			"<td><input name=\""+key+"\" type=\"text\" value=\""+tags[key]+"\" id=\"name-edit-"+numAltName+"\" class=\"name_edit\"></td>" +
			    			"<td><a href=\"javascript:removeRow("+numAltName+")\"><img src=\"img/delete.png\"></a></td></tr>");
			    	objectNames[key] = tags[key];
			    	rowLanguages[numAltName] = key;
			    	numAltName++;
			    }
			};
			
			// if the preferred language was set and there is no such translation in the object
			// a line is added
			if($.cookie("prefLang"))
			{
				var key = "name:"+$.cookie("prefLang");
				if(typeof(objectNames[key]) == 'undefined')
				{
					var langLabel = "";
					if($.cookie("prefLang") in LANGUAGE_CODES)
						langLabel += ' <span class="placeDetails">('+LANGUAGE_CODES[$.cookie("prefLang")]+')</span>';
					objectNames[key] = "";
			    	rowLanguages[numAltName] = key;
			    		$("#table_names").append("<tr class=\"alternative\" id=\"alternative-"+numAltName+"\"><td>"+$.cookie("prefLang")+langLabel+"</td>" +
			    			"<td><input name=\""+key+"\" type=\"text\" value=\"\" id=\"name-edit-"+numAltName+"\" class=\"name_edit\"></td>" +
			    			"<td><a href=\"javascript:removeRow("+numAltName+")\"><img src=\"img/delete.png\"></a></td></tr>");
				}
			}
			numAltName++;
			
			setChangeEvents();
			$("#tabs").tabs("enable",1);
			$("#tabs").tabs("select",1);
			$( "#waitDialog" ).dialog('close');
		},
		error: function(e){$( "#waitDialog" ).dialog('close');alert('Error while retrieving OSM object');}
    });
}

/**
 * Remove a translation row
 * @param num
 */
function removeRow(num)
{
	$("#alternative-"+num).remove();
	delete objectNames[rowLanguages[num]];
	setChangeEvents();
}

/**
 * Called after a language code is chosen, the input text and delete buttons are displayed
 * @param event
 */
function selectLang(event)
{
	if($("#edit_lang").val().length == 2)
	{
		if(!("name:"+$("#edit_lang").val() in objectNames))
		{
			$(".name_edit").show();
			$("#link_add_tr").show();
			$("#type-lang-tip").remove();
			typingLanguage = false;
			rowLanguages[numAltName-1] = "name:"+$("#edit_lang").val();
			objectNames["name:"+$("#edit_lang").val()] = "";
			$("#alternative-"+(numAltName-1)+" .name_edit").attr("name","name:"+$("#edit_lang").val());
			var label = $("#edit_lang").val();
			if($("#edit_lang").val() in LANGUAGE_CODES)
				label += ' <span class="placeDetails">('+LANGUAGE_CODES[$("#edit_lang").val()]+')</span>';
			$("#alternative-"+(numAltName-1)+" td").eq(0).html(label);
			$("#alternative-"+(numAltName-1)+" td").eq(1).show();
			$("#alternative-"+(numAltName-1)+" td").eq(2).show();
			// the name edit field is given the focus
			$("#name-edit-"+(numAltName-1)).focus();
		}
		else
			alert("This language is already in use");
	}
}

/**
 * Add a new edit line, only a text input is displayed, to type the languague code
 */
function addLine()
{
	if(!typingLanguage)
	{
		$("#table_names").append("<tr class=\"alternative\" id=\"alternative-"+numAltName+"\"><td><input type=\"text\" id=\"edit_lang\" size=\"2\" maxlength=\"2\"></td>"+
				"<td>" +
					"<span id=\"type-lang-tip\" class=\"placeDetails\">type a <a href=\"http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes\" target=\"blank\">language code (ie. en, fr, de)</a></span>"+
					"<input type=\"text\" id=\"name-edit-"+numAltName+"\" class=\"name_edit\" style=\"display:none\">" +
				"</td>"+
				"<td style=\"display:none\"><a href=\"javascript:removeRow("+numAltName+")\"><img src=\"img/delete.png\"></td></tr>");
		$("#edit_lang").autocomplete({source:ISO639,close:selectLang});
		$("#edit_lang").blur(selectLang);
		$("#edit_lang").focus();
		numAltName++;
		typingLanguage = true;
		$("#link_add_tr").hide();
	}
	setChangeEvents();
}

/**
 * Add a name tag in the tag set, and display the table row with the given value 
 * @param key name tag key
 * @param value name tag key
 */
function displayNameField(key,value)
{
	if(! key in NAME_FIELDS)return;
	objectNames[key] = value;
	$("#row_edit_"+key).show();
	$("#link_set_"+key).hide();
	$("#edit_"+key).val(value);
	setChangeEvents();
}

/**
 * Delete a name tag from the tag set, and hide the table row
 * @param key name tag key
 */
function hideNameField(key)
{
	if(! key in NAME_FIELDS)return;
	delete objectNames[key];
	$("#row_edit_"+key).hide();
	$("#link_set_"+key).show();
	setChangeEvents();
}
