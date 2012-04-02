
var ISO639 = ['aa', 'ab', 'ae', 'af', 'ak', 'am', 'an', 'ar', 'as', 'av', 'ay', 'az', 'ba', 'be', 'bg', 'bh', 'bi', 'bm', 'bn', 'bo', 'br', 'bs', 'ca', 'ce', 'ch', 'co', 'cr', 'cs', 'cu', 'cv', 'cy', 'da', 'de', 'dv', 'dz', 'ee', 'el', 'en', 'eo', 'es', 'et', 'eu', 'fa', 'ff', 'fi', 'fj', 'fo', 'fr', 'fy', 'ga', 'gd', 'gl', 'gn', 'gu', 'gv', 'ha', 'he', 'hi', 'ho', 'hr', 'ht', 'hu', 'hy', 'hz', 'ia', 'id', 'ie', 'ig', 'ii', 'ik', 'io', 'is', 'it', 'iu', 'ja', 'jv', 'ka', 'kg', 'ki', 'kj', 'kk', 'kl', 'km', 'kn', 'ko', 'kr', 'ks', 'ku', 'kv', 'kw', 'ky', 'la', 'lb', 'lg', 'li', 'ln', 'lo', 'lt', 'lu', 'lv', 'mg', 'mh', 'mi', 'mk', 'ml', 'mn', 'mo', 'mr', 'ms', 'mt', 'my', 'na', 'nb', 'nd', 'ne', 'ng', 'nl', 'nn', 'no', 'nr', 'nv', 'ny', 'oc', 'oj', 'om', 'or', 'os', 'pa', 'pi', 'pl', 'ps', 'pt', 'qu', 'rm', 'rn', 'ro', 'ru', 'rw', 'sa', 'sc', 'sd', 'se', 'sg', 'si', 'sk', 'sl', 'sm', 'sn', 'so', 'sq', 'sr', 'ss', 'st', 'su', 'sv', 'sw', 'ta', 'te', 'tg', 'th', 'ti', 'tk', 'tl', 'tn', 'to', 'tr', 'ts', 'tt', 'tw', 'ty', 'ug', 'uk', 'ur', 'uz', 've', 'vi', 'vo', 'wa', 'wo', 'xh', 'yi', 'yo', 'za', 'zh'];
var objectNames = {};
var rowLanguages = {};
var numAltName = 0;
var typingLanguage = false;
var editedObject = {};

$(function(){	
	$("#button_save_edit").click(saveObject);
});

function setChangeEvents()
{
	$(".name_edit").unbind();
	$(".name_edit").change(function(){
		objectNames[$(this).attr("name")] = $(this).val();
	});
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
			$("#tabs").tabs("enable",2);
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
			editedObject.type = type;
			editedObject.id = id;
			objectNames = {};
			
			$("#table_other_tags").empty();
			$("#table_names .alternative").remove();
			numAltName = 0;
			$(e).find('tag').each(function () {
			    if($(this).attr("k").substring(0,4) != "name")
			    {
			    	$("#table_other_tags").append("<tr><td>"+$(this).attr("k")+"</td><td>"+$(this).attr("v")+"</td></tr>")
			    }
			    else if($(this).attr("k") == "name")
			    {
			    	$("#edit_name").val($(this).attr("v"));
			    	objectNames["name"] = $(this).attr("v");
			    }
			    else
			    {
			    	$("#table_names").append("<tr class=\"alternative\" id=\"alternative-"+numAltName+"\"><td>"+$(this).attr("k").substring(5)+"</td>" +
			    			"<td><input name=\""+$(this).attr("k")+"\" type=\"text\" value=\""+$(this).attr("v")+"\" id=\"name-edit-"+numAltName+"\" class=\"name_edit\"></td>" +
			    			"<td><a href=\"javascript:removeRow("+numAltName+")\"><img src=\"img/delete.png\"></a></td></tr>");
			    	objectNames[$(this).attr("k")] = $(this).attr("v");
			    	rowLanguages[numAltName] = $(this).attr("k");
			    	numAltName++;
			    }
			});
			
			setChangeEvents();
			$("#tabs").tabs("enable",1);
			$("#tabs").tabs("select",1);
			$( "#waitDialog" ).dialog('close');
		},
		error: function(e){$( "#waitDialog" ).dialog('close');alert('Error while retrieving OSM object');}
    });
}

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
			$("#link_add_tr").show();
			typingLanguage = false;
			rowLanguages[numAltName-1] = "name:"+$("#edit_lang").val();
			objectNames["name:"+$("#edit_lang").val()] = "";
			$("#alternative-"+(numAltName-1)+" .name_edit").attr("name","name:"+$("#edit_lang").val());
			$("#alternative-"+(numAltName-1)+" td").eq(0).html($("#edit_lang").val());
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
				"<td style=\"display:none\"><input type=\"text\" id=\"name-edit-"+numAltName+"\" class=\"name_edit\"></td>"+
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