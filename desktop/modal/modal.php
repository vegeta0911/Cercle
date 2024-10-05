<?php

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


if (init('id') == '') {
    throw new Exception('{{L\'id de l\'opération ne peut etre vide : }}' . init('op_id'));
}

$id = init('id');
$groupe = groupe::byId($id);
if (!is_object($groupe)) { 	  
	throw new Exception(__('Aucun equipement ne  correspond : Il faut (re)-enregistrer l\'équipement ', __FILE__) . init('action'));
}
$active = $groupe->getConfiguration('activAction');
$name_off = $groupe->getConfiguration('nameOff','OFF');
$name_on =  $groupe->getConfiguration('nameOn','ON');
$cmds = groupe::getCmdEq(init('id'));
sendVarToJS('infoGroupe', $cmds);
?>
<div class="modalGroup">
	<center>
		<h3>Équipements <?php echo  $name_on ?></h1>
		<table border="0"  id='activeTable'> 
			<thead>
				<tr>
					<th>{{Nom}}</th>
					<?php
					if($active == 1) {
						echo '<th>{{Commande ON}}</th><th>{{Commande OFF}}</th>';
					}
					?>
					<th>{{Dernière communication}}</th>
				</tr>
			</thead>	
			<tbody></tbody>
		</table>	
	</center>
	<center>
		<h3>Équipements   <?php echo  $name_off ?></h1>
		<table border="0"  id='inactiveTable'> 
			<thead>
				<tr>
					<th>{{Nom}}</th>
					<?php
					if($active == 1) {
						echo '<th>{{Commande ON}}</th><th>{{Commande OFF}}</th>';
					}
					?>
					<th >{{Dernière communication}}</th>
				</tr>
			</thead>	
			<tbody></tbody>
		</table>
	</center>
</div>


<style>
#inactiveTable td,  #activeTable td{
	height:40px;
	width:150px;
	padding:10px;
	text-align: center;
}
#inactiveTable th,  #activeTable th {
	text-align: center;
	padding:10px;
	text-align: center;
}
#inactiveTable button,  #activeTable button {
	width: fit-content;
}

</style>
<?php include_file('desktop', 'modal', 'js', 'groupe');?>
<script>
readTable(infoGroupe);
</script>


