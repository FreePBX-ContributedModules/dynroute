<?php
namespace FreePBX\modules\Dynroute;
use FreePBX\modules\Backup as Base;
class Restore Extends Base\RestoreBase{
	public function runRestore(){
		$configs = $this->getConfigs();
		foreach ($configs['dynroutes'] as $id => $dynroute) {
			dynroute_save_details($dynroute['0']);
		}

		foreach($configs['entries'] as $id => $entry) {
			dynroute_restore_entries($id, $entry);
		}
	}
}

