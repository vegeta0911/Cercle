
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */
 
$("#table_cmd_grp").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});

$("body").delegate(".listCmdInfo", 'click', function() {
	var type = $(this).attr('data-type');
	var el = $(this).closest('.' + type).find('.cmdAttr[data-type='+type+'][data-l1key=configuration][data-l2key=state]');
    jeedom.cmd.getSelectModal({cmd: {type: 'info', subtype: 'binary'}}, function(result) {
        el.value(result.human);
    });
});

$("body").delegate(".listCmdActionOff", 'click', function() {
    var type = $(this).attr('data-type');
    var el = $(this).closest('.' + type).find('.cmdAttr[data-l1key=configuration][data-l2key=OFF]');
    jeedom.cmd.getSelectModal({cmd: {type: 'action'}}, function(result) {
        el.value(result.human);
    });
});

$("body").delegate(".listCmdActionOn", 'click', function() {
    var type = $(this).attr('data-type');
    var el = $(this).closest('.' + type).find('.cmdAttr[data-l1key=configuration][data-l2key=ON]');
    jeedom.cmd.getSelectModal({cmd: {type: 'action'}}, function(result) {
        el.value(result.human);
    });
});




$('.cmdAction[data-action=addCmd]').on('click', function () {
	var _cmd = "";
    addCmdToTable(_cmd);
});


$('.chooseImg').off().on('click', function() {
	var type = $(this).attr('id');
	$('#md_modal2').dialog({
		autoOpen: false,
		modal: true,
		title: 'Images',
		buttons: {
		Cancel: function() {
			$('#md_modal2').dialog( "close" );

		},
		"Choisir": changeImg,
		}//,
		

	});
	$('#md_modal2').load('index.php?v=d&plugin=groupe&modal=img.modal&id=' +$('.eqLogicAttr[data-l1key=id]').val() + '&type='+ type).dialog('open');
});

function changeImg() {
	$('.eqLogicAttr[data-l1key=configuration][data-l2key='+ _type + ']').empty().val(filePath);
	$('.' + _type + ' img').attr("src", filePath);
	$('#md_modal2').dialog( "close" );
	
}



 $('body').undelegate('.icone .iconeOn[data-l1key=chooseIcon]', 'click').delegate('.icone .iconeOn[data-l1key=chooseIcon]', 'click', function () {
    var mode = $(this).closest('.icone');
    chooseIcon(function (_icon) {
        mode.find('.iconeAttrOn[data-l2key=iconOn]').empty().append(_icon);
    });
});

 $('body').undelegate('.icone .iconeAttrOn[data-l2key=iconOn]', 'click').delegate('.icone .iconeAttrOn[data-l2key=iconOn]', 'click', function () {
    $(this).empty();
});

 $('body').undelegate('.icone .iconeOff[data-l1key=chooseIcon]', 'click').delegate('.icone .iconeOff[data-l1key=chooseIcon]', 'click', function () {
    var mode = $(this).closest('.icone');
    chooseIcon(function (_icon) {
        mode.find('.iconeAttrOff[data-l2key=iconOff]').empty().append(_icon);
    });
});

 $('body').undelegate('.icon .iconeAttrOff[data-l2key=iconOff]', 'click').delegate('.icone .iconeAttrOff[data-l2key=iconOff]', 'click', function () {
    $(this).empty();
});

$( "#widgetType" ).change(function() {
	switch($(this).value()) {
		case "icon":
			$('.icon').show();
			$('.img').hide();
			break;
		case "img":
			$('.icon').hide();
			$('.img').show();
			break;
	}
});

function printEqLogic(_eqLogic) {
	$('.action').show();
	$('.icon,.img').hide();
	if (!isset(_eqLogic)) {
		var _eqLogic = {configuration: {}};
	}
	
	if (!isset(_eqLogic.configuration)) {
	   _eqLogic.configuration = {};
	}

	if(_eqLogic.configuration.widgetType == "") {
		_eqLogic.configuration.widgetType += 'icon';
	}
	
	if(!isset(_eqLogic.configuration.widgetType)) {
	   $('.icon').show();
	 } else {
		$('.' + _eqLogic.configuration.widgetType).show();		 
	 }
		
	
	if (isset(_eqLogic.configuration.activAction)) {
		if(_eqLogic.configuration.activAction == 1) {
			$('.action').show();
		} else {
			$('.action').hide();
			
		}
	} else {
		$('.action').show();
	}
	if(!isset(_eqLogic.configuration.imgOff)) {
	  $('.imgOff img').attr("src", '/plugins/groupe/data/img/light_off.png');
	  $('.eqLogicAttr[data-l1key=configuration][data-l2key=imgOff]').val('/plugins/groupe/data/img/light_off.png');
	 } else {
	  $('.imgOff img').attr("src", _eqLogic.configuration.imgOff); 
	 }
	
	if(!isset(_eqLogic.configuration.imgOn)) {
		$('.imgOn img').attr("src", '/plugins/groupe/data/img/light_on.png');
		$('.eqLogicAttr[data-l1key=configuration][data-l2key=imgOn]').val('/plugins/groupe/data/img/light_on.png');
	   
	 } else {
		 $('.imgOn img').attr("src", _eqLogic.configuration.imgOn);
	 } 		
}

 function saveEqLogic(_eqLogic) {
    if (!isset(_eqLogic.configuration)) {
        _eqLogic.configuration = {};
    }
	 _eqLogic.configuration.etat = [];
	 _eqLogic.configuration.cmd_on = [];
	 _eqLogic.configuration.cmd_off = []
    $('#table_cmd_grp .cmd').each(function () {
        var etats = $(this).find('.trigger').getValues('.cmdAttr[data-l1key=configuration][data-l2key=state]');
		var ons = $(this).find('.action').getValues('.cmdAttr[data-l1key=configuration][data-l2key=ON]');
		var offs = $(this).find('.action').getValues('.cmdAttr[data-l1key=configuration][data-l2key=OFF]');
		 _eqLogic.configuration.etat.push(etats[0].configuration.state);
		 _eqLogic.configuration.cmd_on.push(ons[0].configuration.ON);
		 _eqLogic.configuration.cmd_off.push(offs[1].configuration.OFF);
		 
		
    });

  	return _eqLogic;
 }
 
$('.eqLogicAttr[data-l1key=configuration][data-l2key=activAction]').change(function () {
	 if(this.checked) {
		 $('.action').show();		 
		 
	 } else {
		$('.action').hide();
	 }
});		
 



function addCmdToTable(_cmd) {
		
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
		
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
	
	if (_cmd.logicalId == 'statuson' || _cmd.logicalId == 'statusoff' || _cmd.logicalId == 'status' || _cmd.logicalId == 'last' || _cmd.logicalId == 'allon' || _cmd.logicalId == 'alloff' ) {
		var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
		tr += '<td>';
		tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}">';
		tr += '</td>';
		tr += '<td>';
		tr += '<span class="type" type="' + init(_cmd.type) + '" disabled>' + jeedom.cmd.availableType() + '</span>';
		tr += '<span class="subType" subType="' + init(_cmd.subType) + '" disabled></span>';
		tr += '</td>';
		tr += '<td>';
		if (is_numeric(_cmd.id)) {
			tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fa fa-cogs"></i></a> ';
			tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
		}
		tr += '</td>';
		tr += '</tr>';
		$('#table_info_grp tbody').append(tr);
		$('#table_info_grp tbody tr:last').setValues(_cmd, '.cmdAttr');
		if (isset(_cmd.type)) {
			$('#table_info_grp tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
		}
		jeedom.cmd.changeType($('#table_info_grp tbody tr:last'), init(_cmd.subType));
	} else {
		if (!isset(_cmd.subType)) {
			_cmd.subType = "";
		}
		var tr = '<tr class="cmd ' + _cmd.type + '" data-cmd_id="' + init(_cmd.id) + '">';
		tr += '<td>';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">';
		tr += '<span class="type" type="info" style="display : none;">' + jeedom.cmd.availableType() + '</span>';
		tr += '<input class="cmdAttr form-control" type="hidden" data-l1key="subType" value="groupe">';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" 	 placeholder="{{Nom}}">';
		tr += '</td><td class="trigger">';
		tr += ' <input class="cmdAttr form-control input-sm"  data-type="' + _cmd.type + '" data-l1key="configuration" data-l2key="state"  style="margin-bottom : 5px;width : 80%; display : inline-block;" disabled>';
		tr += ' <a class="btn btn-default btn-sm cursor listCmdInfo" data-type="' + _cmd.type + '"  style="margin-left : 5px;"><i class="fas fa-list-alt "></i></a>';		
		tr += '</td><td class="action" style="display : none;">';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-type="' + _cmd.type + '" data-l2key="ON"  style="margin-bottom : 5px;width : 80%; display : inline-block;" disabled>';
		tr += '<a class="btn btn-default btn-sm cursor listCmdActionOn" data-type="' + _cmd.type + '" data-input="ON" style="margin-left : 5px;"><i class="fas fa-list-alt "></i></a>';
		tr += '</td><td class="action" style="display : none;">';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-type="' + _cmd.type + '" data-l2key="OFF"  style="margin-bottom : 5px;width : 80%; display : inline-block;" disabled>';
		tr += '<a class="btn btn-default btn-sm cursor listCmdActionOff" data-type="' + _cmd.type + '" data-input="OFF" style="margin-left : 5px;"><i class="fas fa-list-alt "></i></a>';
		tr += '</td><td>';
		tr += '<input type="checkbox" class="tooltips cmdAttr" data-l1key="configuration" data-l2key="reverse">';
		tr += '</td><td>';
		tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
		tr += '</td>';
		tr += '</tr>';
		$('#table_cmd_grp tbody').append(tr);
		$('#table_cmd_grp tbody tr:last').setValues(_cmd, '.cmdAttr');
		if (isset(_cmd.type)) {
			$('#table_cmd_grp tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
		}
	}

	$.ajax({
		type: 'POST',
		url: 'plugins/groupe/core/ajax/groupe.ajax.php',
		data: {
			action: 'getStatus',
			id: $('.eqLogicAttr[data-l1key=id]').value()
		},
		dataType: 'json',
		error: function (request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function (data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			if (data.result == 1) {
				$('.action').show();
			} else {
				$('.action').hide();
			}					
		}
   });	
		

//			
}
