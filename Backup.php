<?php
// Copyright (c) 2015-2020 John Fawcett
// This is a dervied work licenced under GPL V3 or later
// The original file was published by Sagoma Technologies in
// Freepbx IVR module

namespace FreePBX\modules\Dynroute;
use FreePBX\modules\Backup as Base;
class Backup Extends Base\BackupBase{
  public function runBackup($id,$transaction){
    $this->addDependency('core');
    $this->addDependency('recordings');
    $configs = [
        'dynroutes' => $this->FreePBX->Dynroute->getAllDetails(),
        'entries' => $this->FreePBX->Dynroute->getAllEntries(),
    ];
    $this->addConfigs($configs);
  }
}
