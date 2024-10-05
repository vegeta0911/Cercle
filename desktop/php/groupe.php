<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('groupe');
sendVarToJS('eqType', 'groupe');
$eqLogics = eqLogic::byType('groupe');
?>

<div class="row row-overflow">
    <div class="col-xs-12 eqLogicThumbnailDisplay">
		<legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
        <div class="eqLogicThumbnailContainer">
        	<div class="cursor eqLogicAction logoSecondary" data-action="add"  >
                <i class="fas fa-plus-circle"></i>
                <br>
                <span>{{Ajouter}}</span>
            </div>
            <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
              <i class="fas fa-wrench"></i>
            <br>
            <span >{{Configuration}}</span>
            </div>            
		</div>	
		<legend>{{Mes Equipements}}</legend>
		<input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
		<div class="eqLogicThumbnailContainer">
		<?php
			foreach ($eqLogics as $eqLogic) {				
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '" >';
				echo '<img src="' . $plugin->getPathImgIcon() . '" />';
				echo "<br>";
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '</div>';
			 }
		?>
		</div>		
    </div>

	<div class="col-xs-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-default eqLogicAction btn-sm roundedLeft" data-action="configure"><i class="fas fa-cogs"></i> {{Configuration avancée}}</a><a class="btn btn-default btn-sm eqLogicAction" data-action="copy"><i class="fas fa-copy"></i> {{Dupliquer}}</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
			</span>
		</div>
        <ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
			<li role="presentation"><a href="#infotab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i> {{Configuration}}</a></li>
			<li role="presentation"><a href="#infocmd" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i> {{Commandes}}</a></li>
        </ul>
        <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">  
                <form class="form-horizontal">
					<fieldset>
						<br />
						<div class="form-group">
							<label class="col-md-2 control-label">{{Nom de l'équipement groupe}}</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
								<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement groupe}}"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label" >{{Objet parent}}</label>
							<div class="col-sm-3">
								<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
									<option value="">{{Aucun}}</option>
									<?php
										foreach (jeeObject::all() as $object) {
											echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
										}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label">{{Catégorie}}</label>
							<div class="col-md-8">
								<?php
								foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
								echo '<label class="checkbox-inline">';
								echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
								echo '</label>';
								}
								?>
							</div>
						</div>           
						<div class="form-group">
							<label class="col-md-2 control-label"></label>
							<div class="col-sm-9">
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
							</div>
						</div>
					</fieldset>
				</form>
            </div>
            <div role="tabpanel" class="tab-pane" id="infotab">      	      	
				<form class="form-horizontal">
             		<br />
					<div class="form-group">
						<label class="col-md-2 control-label">{{Ajouter un équipement}}</label>
						<div class="col-md-2">
							<a  class="btn btn-success btn-sm cmdAction btAdd_table_cmd " data-action="addCmd"><i class="fas fa-plus-circle"></i></a>
						</div>
					</div>
					<br/>  
					<div class="form-group">
						<label class="col-md-2 control-label">{{Activer les actions}}</label>
					    <div class="col-md-2">
							<input type="checkbox" class="eqLogicAttr checkbox-inline" data-l1key="configuration"  data-l2key="activAction"  checked/>
						</div>
					</div>
					</br>
					<div class="form-group">
						<label class="col-sm-2 control-label"></label>
						<div class="col-sm-9">
							<table id="table_cmd_grp" class="table table-bordered table-condensed ui-sortable table_cmd">
								<thead>
									<tr>
										<th style="width: 10%;">{{Nom}}</th>
										<th  class="etat" >{{Commande Etat}}</th>
										<th class="action" >{{Commande ON}}</th>
										<th class="action" >{{Commande OFF}}</th>
										<th style="width: 5%;">{{Inverser}}</th>
										<th style="width: 5%;">{{Effacer}}</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
            		<div class="form-group">
						<label class="col-sm-2 control-label">{{Nom ON}} <sup data-toggle="tooltip" data-placement="top" title="{{Nom de la commande qui apparait dans la modale}}"><i class="fas fa-question-circle"></i></sup></label>
						<div class="col-sm-1">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key='nameOn' placeholder="{{Nom}} " />
						</div>
                    </div>
					<br/>
                    <div class="form-group">
						<label class="col-sm-2 control-label">{{Nom OFF}} <sup data-toggle="tooltip" data-placement="top" title="{{Nom de la commande qui apparait dans la modale}}"><i class="fas fa-question-circle"></i></sup></label>
						<div class="col-sm-1">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key='nameOff' placeholder="{{Nom}} " />
						</div>               
                    </div>
					<br/>
					<div class="alert alert-info"> {{ Widgets }}</div>					 
                    <br/> 
					<div class="form-group">
						<label class="col-sm-2 control-label">{{ Inverser }} <sup data-toggle="tooltip" data-placement="top" title="{{Si cocher , le widget sera actif si TOUS les équipements sont actifs, sinon 1 seul suffit}}"><i class="fas fa-question-circle"></i></sup></label>
						<div class="col-sm-1">
							<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="stateInverse" />
						</div>				 
					</div>					 
				 	<br/>	 
					<div class="form-group">
						<label class="col-sm-2 control-label">{{Type :}} </label>
						<div class="col-sm-2">
							<select id="widgetType" class="eqLogicAttr form-control"  data-l1key="configuration" data-l2key="widgetType">
							<option value="icon">{{Icones}}</option>
							<option value="img">{{Images}}</option>
							</select>
						</div>					 
					</div>
				    <br/> 
				 	<div class="icon">
						<div class="form-group"> 
							<label class="col-sm-2 control-label">{{Icône On}}</label>
							<div id="div_on">
								<div class="icone">
									<div class="col-sm-2">
										<a class="iconeOn btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fas fa-flag"></i> {{Icône}}</a>
										<span class="eqLogicAttr iconeAttrOn label label-info cursor"  data-l1key="configuration" data-l2key="iconOn"  style="font-size : 1em;" ></span>
									</div>                  
								</div>
							</div>  
						</div> 
					<div class="form-group" >
						<label class="col-sm-2 control-label">{{Icône Off}}</label>
						<div id="div_off">
							<div class="icone">
								 <div class="col-sm-2">
									<a class="iconeOff btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fas fa-flag"></i> {{Icône}}</a>
									<span class="eqLogicAttr iconeAttrOff label label-info cursor" data-l1key="configuration" data-l2key="iconOff"  style="font-size : 1em;" ></span>
								 </div>                   
							 </div>
						</div>
					</div>					 
				 </div>
				<br/>
				 <div class="img row">
					<label class="col-sm-2 control-label"></label>
					<div class="form-group col-sm-4"> 
						<div id="div_on">
							<div class="icone">
								 <div class="col-sm-3">
									<a id="imgOn" class=" btn btn-default btn-sm chooseImg" ><i class="fas fa-flag"></i> {{ Image On }}</a>
									<input type="text" class="eqLogicAttr iconeAttrOn label label-info cursor"  data-l1key="configuration" data-l2key="imgOn"   >
								 </div>
								 <div class="imgOn col-sm-6">
									 <img src="" height=120 width=120>
								</div>								
							 </div>
						</div>  
					</div> 
					<div class="form-group col-sm-4" >
						<div id="div_off">
							<div class="icone">
								 <div class="col-sm-3">
									<a id="imgOff"  class=" btn btn-default btn-sm chooseImg" ><i class="fas fa-flag"></i>{{ Image Off }}</a>
									<input type="text" class="eqLogicAttr  label label-info cursor" data-l1key="configuration" data-l2key="imgOff" >
								 </div> 
								 <div class="imgOff col-sm-6">
									 <img src="" height=120 width=120>
								</div>
							 </div>
						</div>
					</div>
				 </div>	 					 
                 <br/>
				</form>
            </div>
            <div role="tabpanel" class="tab-pane" id="infocmd">
				<br/>
				<div class="form-group">
					<label class="col-sm-1 control-label"></label>
					<div class="col-sm-9">				
						<table id="table_info_grp" class="table table-bordered table-condensed">
							<thead>
								<tr>
									<th>{{Nom}}</th>
									<th>{{type}}</th>
									<th>{{Action}}</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<?php include_file('desktop', 'groupe', 'js', 'groupe');?>
<?php include_file('core', 'plugin.template', 'js');?>