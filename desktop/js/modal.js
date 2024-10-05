

function readTable(infoGroupe) {
	$("#activeTable tbody").empty();
	$("#inactiveTable tbody").empty();
	console.log(infoGroupe)
	console.log(infoGroupe.length)
	var html = "";
	var html1 = "";
	for (i = 0; i < infoGroupe.length; i++) {
		if (infoGroupe[i][3] == 1) {;
			if (infoGroupe[i][0] == 0) {
				html += "<tr class='line1'><td> " + infoGroupe[i][8] + "</td><td><button  name=" + infoGroupe[i][8] + " data-id='" + infoGroupe[i][7] +  "' data-action='on' class='btn btn-success form-control actionGroup' value='" + infoGroupe[i][1] +  "'> " + infoGroupe[i][4] +   "</button></td><td><button   data-id='" + infoGroupe[i][7] +  "' data-action='off' class='btn btn-danger form-control actionGroup' name=" + infoGroupe[i][8] + " value='" + infoGroupe[i][2] +  "'> " + infoGroupe[i][5] +   "</button></td><td> " + infoGroupe[i][6] +"</td></tr>";
			} else {
				html1 += "<tr class='line2'><td> " + infoGroupe[i][8] + "</td><td><button  data-id='" + infoGroupe[i][7] +  "' data-action='on' name=" + infoGroupe[i][8] + " class='btn btn-success form-control actionGroup' value='" + infoGroupe[i][1] +  "'> " + infoGroupe[i][4] +   " </button></td><td><button  data-id='" + infoGroupe[i][7] +  "' data-action='off' class='btn btn-danger form-control actionGroup' name=" + infoGroupe[i][8] + " value='" + infoGroupe[i][2] +  "'> " + infoGroupe[i][5] +   "</button></td><td> " + infoGroupe[i][6] +"</td></tr>";
			}

		} else {
			if (infoGroupe[i][0] == 0) {
				html += "<tr class='line1'><td> " + infoGroupe[i][8] + "</td><td> " + infoGroupe[i][6] +"</td></tr>";
			} else {
				html1 += "<tr class='line2'><td> " + infoGroupe[i][8] + "</td><td> " + infoGroupe[i][6] +"</td></tr>";
			}			
		}
	}
	$("#activeTable tbody").append(html1);
	$("#inactiveTable tbody").append(html);	
	$('.actionGroup').on('click', function () {
		console.log('tutu')
		$.ajax({// fonction permettant de faire de l'ajax
			type: "POST", // methode de transmission des données au fichier php
			url: "plugins/groupe/core/ajax/groupe.ajax.php", // url du fichier php
			global:false,
			data: {
				action: "execCmdEq",
				id: $(this).value(),
				cmdId: $(this).attr('data-id')
			},
			dataType: 'json',
			error: function(request, status, error) {
				handleAjaxError(request, status, error);
			},
			success: function(data) { // si l'appel a bien fonctionné
				if (data.state != 'ok') {
					$('#div_alert').showAlert({message:  data.result,level: 'danger'});
					return;
				}
				console.log(data)
				readTable(data.result)

			}
		});	 				
	});
}

