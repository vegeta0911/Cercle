<?php
if (!isConnect('admin')) {
  throw new Exception('{{401 - Accès non autorisé}}');
}


             
$dir = __DIR__ . '/../../data/img/';
sendVarToJS('_id', init('id'));
sendVarToJS('_type', init('type'));
?>

<div class="row row-overflow">
	<div class="row">
		<span class="btn btn-default btn-file pull-right" style="right:50px;">
			<i class="fas fa-cloud-upload-alt"></i> {{Envoyer}}<input  id="bt_uploadImageIcon" type="file" name="file">
		</span>	
	
	</div>

	<?php
		$files = scandir($dir);
		foreach ($files as $file) {
			if (substr($file, -4) == '.png') {
				echo '<div class="divIcon" style="margin:5px;float:left;">';
				echo '<span class="iconSel"><img height="100" width="100" src="/plugins/groupe/data/img/' . $file . '" name="'. $file . '" ></span >';
				echo '<center>'.substr(basename($file),0,12).'</center>';
				echo  '<center><a class="btn btn-danger btn-xs bt_removeImgIcon" data-filename="'.$file.'"><i class="fas fa-trash"></i> {{Supprimer}}</a></center>';
				echo '<input class="filePath" type="text" value="/plugins/groupe/data/img/'.$file.'" id="'. $file . '" style="position:absolute; opacity:0">';
				echo '</div>';
			}
		}
	?>
</div>
                    
<script>
	console.log(_id + ' ' + _type)
var filePath = "";
$(".iconSel img").on('click', function() {
	$('.iconSel img').each(function (index, value) { 
		$(this).css("border", "");
	});;
	$(this).css("border", "1px solid red");
	
	filePath = $(this).closest('div').find('.filePath').val();
	
});

$('#bt_uploadImageIcon').fileupload({
	replaceFileInput: false,
	url: 'plugins/groupe/core/ajax/groupe.ajax.php?action=imgUpload&jeedom_token='+JEEDOM_AJAX_TOKEN,
	dataType: 'json',
	done: function (e, data) {
		if (data.result.state != 'ok') {

			$('#div_iconSelectorAlert').showAlert({message: data.result.result, level: 'danger'});
			return;
		}

		$('#md_modal2').empty().load('index.php?v=d&plugin=groupe&modal=img.modal');

	}
});

$('.bt_removeImgIcon').on('click',function(){
	var filename = $(this).attr('data-filename');
	bootbox.confirm('{{Êtes-vous sûr de vouloir supprimer cette image}} <span style="font-weight: bold ;">' + filename + '</span> ?', function (result) {
		if (result) {
			$.ajax({// fonction permettant de faire de l'ajax
				type: "POST", // methode de transmission des données au fichier php
				url: "plugins/groupe/core/ajax/groupe.ajax.php", // url du fichier php
				data: {
					action: "deleteImg",
					name: filename
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
					$('#md_modal2').empty().load('index.php?v=d&plugin=groupe&modal=img.modal');
				}
			});				
		}
	});
});
	

</script>

<?php
	include_file('3rdparty', 'jquery.tree/jstree.min', 'js');
?>
