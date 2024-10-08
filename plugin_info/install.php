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

require_once __DIR__ . '/../../../core/php/core.inc.php';

function groupe_install() {
	foreach (groupe::byType('groupe', true) as $group) {
		try {
			$etats = $group->getConfiguration('etat');
			if(count($etats) > 0) {
				$listener = listener::byClassAndFunction('groupe', 'pull', array('groupe_id' => intval($group->getId())));
				if (!is_object($listener)) {
					$listener = new listener();
				}
				$listener->setClass('groupe');
				$listener->setFunction('pull');
				$listener->setOption(array('groupe_id' => intval($group->getId())));
				$listener->emptyEvent();
				foreach ($etats as $etat) {
						$cmd = cmd::byId(str_replace('#', '', $etat));
						if (!is_object($cmd)) {
							message::add('groupe', __('Commande déclencheur inconnue : ' . $etat . ' pour ' . $group->getName(), __FILE__), 'cmdError');
							continue;
						}
						$listener->addEvent($etat);
				}
				$listener->save();				
			}
			$group->save();
			/*$file = __DIR__.'/../data/backup.md';
			$dir = __DIR__ . '/../core/template/img';
			if (!is_file($file) && !is_dir($dir)) {
				$cmd = system::getCmdSudo() . ' touch ' . __DIR__ . '/../data/backup.md;';	
				com_shell::execute($cmd);				
			}*/
		} catch (Exception $e) {
				throw new Exception(__('erruer ',  __FILE__));
		}
	}	
	
}

function groupe_update() {
	
	foreach (groupe::byType('groupe', true) as $group) {
		try {
			$cmd = $group->getCmd(null, 'last');
			if (is_object($cmd)) {
				$cmd->setType('info');
				$cmd->setSubType('string');
				$cmd->save();				
			}
			$etats = $group->getConfiguration('etat');
			if(count($etats) > 0) {
				$listener = listener::byClassAndFunction('groupe', 'pull', array('groupe_id' => intval($group->getId())));
				if (!is_object($listener)) {
					$listener = new listener();
				}
				$listener->setClass('groupe');
				$listener->setFunction('pull');
				$listener->setOption(array('groupe_id' => intval($group->getId())));
				$listener->emptyEvent();
				foreach ($etats as $etat) {
						$cmd = cmd::byId(str_replace('#', '', $etat));
						if (!is_object($cmd)) {
							message::add('groupe', __('Commande déclencheur inconnue : ' . $etat . ' pour ' . $group->getName(), __FILE__), 'cmdError');
							continue;
						}
						$listener->addEvent($etat);
				}
				$listener->save();				
			}
			$imgOn = str_replace('core/template','data',$group->getConfiguration('imgOn'));
			$imgOff = str_replace('core/template','data',$group->getConfiguration('imgOff'));
			$group->setConfiguration('imgOn',$imgOn);
			$group->setConfiguration('imgOff',$imgOff);			
			$group->save();
		} catch (Exception $e) {
				throw new Exception(__('erreur ' . $e,  __FILE__));
		}
		/*$file = __DIR__.'/../data/backup.md';
		$dir = __DIR__ . '/../core/template/img';
		if (!is_file($file) && is_dir($dir)) {
			try {			
				groupe::saveConfigFolder();
				if(is_file($file)) {
					message::add('groupe', __(' !!! La mise à jour est réussie !!! ', __FILE__), 'update');
				} else {
					message::add('groupe', __('Erreur lors de la mise à jour,Voir la documentation (menu Update)', __FILE__), 'update');
				}
			} catch (Exception $e) {
				message::add('groupe', __('Erreur lors de l\'update', __FILE__), 'update');
				message::add('groupe', __($e, __FILE__), 'update');
			}			
		} elseif(!is_file($file)) {
			$cmd = system::getCmdSudo() . ' touch ' . __DIR__ . '/../data/backup.md;';	
			com_shell::execute($cmd);
		}*/		
	}	
}

?>
