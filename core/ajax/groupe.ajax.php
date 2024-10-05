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

try {
    require_once __DIR__ . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');
	
	ajax::init(array('launchUpdateFile'));

    if (init('action') == 'launchAction') {
        groupe::launchCmd(init('id'));
		ajax::success();
    }
    if (init('action') == 'getStatus') {
        $groupe = groupe::byId(init('id'));
		$return = $groupe->getConfiguration('activAction');
		ajax::success($return);
    }
	
    if (init('action') == 'actionAll') {
        groupe::actionAll(init('id'));
		ajax::success();
    }
    if (init('action') == 'getCmdEq') {
        $return = groupe::getCmdEq(init('id'));
		ajax::success($return);
    }
    if (init('action') == 'execCmdEq') {
		$cmdEq= cmd::byId(init('cmdId'));
		$groupe = $cmdEq->getEqLogic();
		$cmdAction = cmd::byId(init('id'));
		if (!is_object($groupe) || !is_object($cmdEq) || !is_object($cmdAction)) { 
		 throw new Exception(__('Aucun equipement ne  correspond ou problème avec une commande: Il faut vérifier l\'équipement ou effacer une commande ', __FILE__) . init('action'));
		 }
		$cmdAction->execCmd();
		$active = $groupe->getConfiguration('activAction');
		$name_off = $groupe->getConfiguration('nameOff','OFF');
		$name_on =  $groupe->getConfiguration('nameOn','ON');
		$all = $groupe->getCmd();
		$cmds = array();
		$i=0;
		foreach ($all as $one) {
			if ($one->getlogicalId () == '') {
				$id = $one->getConfiguration('state');
				$cmd = cmd::byId(str_replace('#', '', $id));
				if ($cmdEq->getId() == $one->getId()) {
					if(init('id') == str_replace('#', '', $one->getConfiguration('ON'))) {
						$state =1;
						log::add('groupe', 'debug', 'Commande ON :' . $cmdEq->getName() );
					} else {
						log::add('groupe', 'debug', 'Commande OFF :' . $cmdEq->getName());
						$state =0;
					}
					$last_seen =  date('Y-m-d H:i:s');
				} else {
					$state = $cmd->execCmd();
					$last_seen =  $cmd->getCollectDate();					
				}
				if($one->getConfiguration('reverse') == 1) {
					($state == 0) ? $state = 1 : $state = 0;
				}
				array_push($cmds,array($state,str_replace('#', '', $one->getConfiguration('ON')),str_replace('#', '', $one->getConfiguration('OFF')),$active,$name_on,$name_off,$last_seen,$one->getID(),$one->getName()));
			}
		}
		usort($cmds, array('groupe','compareCmds'));
		ajax::success($cmds);
	} elseif (init('action') == 'imgUpload') {

		if (!isset($_FILES['file'])) {
			throw new Exception(__('Aucun fichier trouvé. Vérifiez le paramètre PHP (post size limit)', __FILE__));
		}
		$extension = strtolower(strrchr($_FILES['file']['name'], '.'));
		if (!in_array($extension, array('.jpg', '.png','.gif'))) {
			throw new Exception('Extension du fichier non valide (autorisé .jpg .png .gif) : ' . $extension);
		}
		if (filesize($_FILES['file']['tmp_name']) > 5000000) {
			throw new Exception(__('Le fichier est trop gros (maximum 5Mo)', __FILE__));
		}
        $uploaddir = __DIR__ . '/../../data/img/';
        if (!file_exists($uploaddir)) {
            mkdir($uploaddir);
        }
		$filename = $_FILES['file']['name'];
		$filepath = $uploaddir . '/' . $filename;
		file_put_contents($filepath,file_get_contents($_FILES['file']['tmp_name']));
		if(!file_exists($filepath)){
			throw new \Exception(__('Impossible de sauvegarder l\'image',__FILE__));
		}
		ajax::success(array('filepath' => $filepath));
    } elseif (init('action') == 'deleteImg') {
		$file = __DIR__ . '/../../data/img/' . init('name');
		if (!isConnect('admin')) {
			throw new Exception(__('401 - Accès non autorisé', __FILE__));
		}
		unautorizedInDemo();
		$filepath  = __DIR__ . '/../../data/img/' . init('name');
		if(!file_exists($filepath)){
			throw new Exception(__('Fichier introuvable, impossible de le supprimer', __FILE__));
		}
		unlink($filepath);
		if(file_exists($filepath)){
			throw new Exception(__('Impossible de supprimer le fichier', __FILE__));
		}
		ajax::success();		

	} elseif (init('action') == 'launchUpdateFile') {
		
		$uploaddir = __DIR__ . '/../../data/';
		if (!isset($_FILES['file'])) {
			throw new Exception(__('Aucun fichier trouvé. Vérifiez le paramètre PHP (post size limit)', __FILE__));
		}
		$extension = strtolower(strrchr($_FILES['file']['name'], '.'));
		if ($extension != '.gz') {
			throw new Exception('Extension du fichier non valide (autorisé .zip) : ' . $extension);
		}
		if (filesize($_FILES['file']['tmp_name']) > 1000000000) {
			log::add('groupe','debug','file2 ' . $file);
			throw new Exception(__('Le fichier est trop gros (maximum 1Go)', __FILE__));
		}
		$file = $_FILES['file']['tmp_name'];
		if(!is_dir($uploaddir)) {
			$cmd = system::getCmdSudo() . 'mkdir -p ' . __DIR__ . '/../../data;';
		}
		$cmd .= system::getCmdSudo() . 'tar -xvzf ' . $file . ' -C ' . $uploaddir . ' ./plugins/groupe/core/template/img/ --strip-components 5;';
		$cmd .= system::getCmdSudo() . ' touch ' . __DIR__ . '/../../data/backup.md;';
		$dir = __DIR__ . '/../template/img';
		if(is_dir($dir)) {
			$cmd .= system::getCmdSudo() . 'rm -R ' . $dir;
		}		
		$return = com_shell::execute($cmd);
		ajax::success($return);
	}
	
			
    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>