<?php
function evarisk_init_permission()
{// Ajout des permissions
	if(function_exists('get_role'))
	{
		// On récupère l'objet "Role administrateur".
		$role = get_role('administrator');
		// On récupère les droits du plugin.
		$droits = getDroitEvarisk();
		foreach($droits as $droit => $appellation)
		{// Pour chaque droit du plugin, on l'affecte à l'administrateur.
			if($role != null && !$role->has_cap($droit))
			{
				$role->add_cap($droit);
			}
		}
		// On supprime la variable de notre fonction.
		unset($role);
	}	
}
?>