<?php
// Dynamic routing modules
// Copied from ivr and calleridlookup modules
// John Fawcett Sept 2009

function dynroute_init() {
    global $db;
    global $amp_conf;

    // Check to make sure that install.sql has been run
    $sql = "SELECT displayname from dynroute where displayname='__install_done' LIMIT 1";

    $results = $db->getAll($sql, DB_FETCHMODE_ASSOC);

    if (DB::IsError($results)) {
            // It couldn't locate the table. This is bad. Lets try to re-create it, just
            // in case the user has had the brilliant idea to delete it.
            // runModuleSQL taken from page.module.php. It's inclusion here is probably
            // A bad thing. It should be, I think, globally available.
            localrunModuleSQL('dynroute', 'uninstall');
            if (localrunModuleSQL('dynroute', 'install')==false) {
                    echo _("There is a problem with install.sql, cannot re-create databases. Contact support\n");
                    die;
            } else {
                    $results = $db->getAll($sql, DB_FETCHMODE_ASSOC);
            }
    }
    
    if (!isset($results[0])) {
        // Note: There's an invalid entry created, __invalid, after this is run,
        // so as long as this has been run _once_, there will always be a result.

		$result = sql("INSERT INTO dynroute (displayname) VALUES ('__install_done')");
		needreload();
    }
}

// The destinations this module provides
// returns a associative arrays with keys 'destination' and 'description'
function dynroute_destinations() {
	//get the list of routes 
	$results = dynroute_list();

	// return an associative array with destination and description
	if (isset($results)) {
		foreach($results as $result){
			$extens[] = array('destination' => 'dynroute-'.$result['dynroute_id'].',s,1', 'description' => $result['displayname']);
		}
	}
	if (isset($extens)) 
		return $extens;
	else
		return null;
}

function dynroute_getdest($exten) {
	return array('dynroute-'.$exten.',s,1');
}

function dynroute_getdestinfo($dest) {
	global $active_modules;

	if (substr(trim($dest),0,9) == 'dynroute-') {
		$exten = explode(',',$dest);
		$exten = substr($exten[0],9);

		$thisexten = dynroute_get_details($exten);
		if (empty($thisexten)) {
			return array();
		} else {
			return array('description' => sprintf(_("Route: %s"),$thisexten['displayname']),
			             'edit_url' => 'config.php?display=dynroute&action=edit&id='.urlencode($exten),
								  );
		}
	} else {
		return false;
	}
}
function dynroute_recordings_usage($recording_id) {
        global $active_modules;

        $results = sql("SELECT `dynroute_id`, `displayname` FROM `dynroute` WHERE `announcement_id` = '$recording_id'","getAll",DB_FETCHMODE_ASSOC);
        if (empty($results)) {
                return array();
        } else {
                //$type = isset($active_modules['dynroute']['type'])?$active_modules['dynroute']['type']:'setup';
                foreach ($results as $result) {
                        $usage_arr[] = array(
                                'url_query' => 'config.php?display=dynroute&action=edit&id='.urlencode($result['dynroute_id']),
                                'description' => sprintf(_("Dynamic route: %s"),$result['displayname']),
                        );
                }
                return $usage_arr;
        }
}

function dynroute_get_config($engine) {
        global $ext;
        global $conferences_conf;
	global $version;

	switch($engine) {
		case "asterisk":
			$dynroutelist = dynroute_list();
			if(is_array($dynroutelist)) {
				foreach($dynroutelist as $item) {
					$id = "dynroute-".$item['dynroute_id'];
					$details = dynroute_get_details($item['dynroute_id']);
					if (version_compare($version, "1.6", "lt")) {
                                                          //Escaping MySQL query - thanks to http://www.asteriskgui.com/index.php?get=utilities-mysqlscape
                                                          $replacements = array (
                                                                '\\' => '\\\\',
                                                                '"' => '\\"',
                                                                '\'' => '\\\'',
                                                                ' ' => '\\ ',
                                                                ',' => '\\,',
                                                                '(' => '\\(',
                                                                ')' => '\\)',
                                                                '.' => '\\.',
                                                                '|' => '\\|'
                                                          );
						$query = str_replace(array_keys($replacements), array_values($replacements), $item['mysql_query']);
					} else {
						$query = $item['mysql_query'];
					}
                                        $query = str_replace('[NUMBER]', '${CALLERID(num)}', $query);
                                        $query = str_replace('[INPUT]', '${dtmfinput}', $query);
					$query = preg_replace('/\[([^\]]*)\]/','${DYNROUTE_$1}',$query);
					$announcement_id = (isset($details['announcement_id']) ? $details['announcement_id'] : '');
					if ($item['enable_dtmf_input']=='CHECKED')
					{
                                        	if ($announcement_id) {
                                              		$announcement_msg = recordings_get_file($announcement_id);
                                        	} else {
							$announcement_msg = '';
                                        	}	
						$ext->add($id, 's', '', new ext_read('dtmfinput',$announcement_msg,'','','',$item['timeout']));
						if ($item['chan_var_name'] != '')
							$ext->add($id, 's', '', new ext_setvar('__DYNROUTE_'.$item['chan_var_name'], '${dtmfinput}'));
                                        }
					if ($item['mysql_host']!='')
					{
                                        	$ext->add($id, 's', '', new ext_mysql_connect('connid', $item['mysql_host'],  $item['mysql_username'],  $item['mysql_password'],  $item['mysql_dbname']));
                                        	$ext->add($id, 's', '', new ext_mysql_query('resultid', 'connid', $query));
                                        	$ext->add($id, 's', '', new ext_mysql_fetch('fetchid', 'resultid', 'dynroute')); 
                                        	$ext->add($id, 's', '', new ext_mysql_clear('resultid'));                            
                                        	$ext->add($id, 's', '', new ext_mysql_disconnect('connid'));
						if ($item['chan_var_name_res'] != '')
							$ext->add($id, 's', '', new ext_setvar('__DYNROUTE_'.$item['chan_var_name_res'], '${dynroute}'));
						$ext->add($id, 's', '', new ext_gotoif('$[${fetchid} = 0]',$id.',1,1'));
                                        }
					$dests = dynroute_get_dests($item['dynroute_id']);
					if (!empty($dests)) {
						$default_dest='';
						foreach($dests as $dest) {
							if ($dest['selection'] == 'default') {
							 	$default_dest=$dest['dest'];
							} else {
								$ext->add($id, 's', '', new ext_gotoif('$["${dynroute}" = "'.$dest['selection'].'"]',$dest['dest']));
							}
						}
					}
					$ext->add($id, 's', '', new ext_goto($id.',1,1'));
					if ($default_dest != '') $ext->add($id, '1', '', new ext_goto($default_dest));
					$ext->add($id, '1', '', new ext_hangup(''));
				}
			}
		break;
	}
}



function dynroute_get_dynroute_id($name) {
	global $db;
	$res = $db->getRow("SELECT dynroute_id from dynroute where displayname='$name'");
	if (count($res) == 0) {
		// It's not there. Create it and return the ID
		sql("INSERT INTO dynroute (displayname )  values('$name')");
		$res = $db->getRow("SELECT dynroute_id from dynroute where displayname='$name'");
	}
	return ($res[0]);
	
}

function dynroute_add_command($id, $cmd, $dest) {
	global $db;
	// Does it already exist?
	$res = $db->getRow("SELECT * from dynroute_dests where dynroute_id='$id' and selection='$cmd'");
	if (count($res) == 0) {
		// Just add it.
		sql("INSERT INTO dynroute_dests VALUES('$id', '$cmd', '$dest')");
	} else {
		// Update it.
		sql("UPDATE dynroute_dests SET dest='$dest' where dynroute_id='$id' and selection='$cmd'");
	}
}
function dynroute_do_edit($id, $post) {
	global $db;
        $displayname = $db->escapeSimple($post['displayname']);
        $mysql_host = $db->escapeSimple($post['mysql_host']);
        $mysql_dbname = $db->escapeSimple($post['mysql_dbname']);
        $mysql_query = $db->escapeSimple($post['mysql_query']);
        $mysql_username = $db->escapeSimple($post['mysql_username']);
        $mysql_password = $db->escapeSimple($post['mysql_password']);
        $annmsg_id = isset($post['annmsg_id'])?$post['annmsg_id']:'';
        $enable_dtmf_input = isset($post['enable_dtmf_input'])?$post['enable_dtmf_input']:'';

        if (!empty($enable_dtmf_input)) {
                $enable_dtmf_input='CHECKED';
        }
        $timeout = isset($post['timeout'])?$post['timeout']:'';
        $chan_var_name = isset($post['chan_var_name'])?$post['chan_var_name']:'';
        $chan_var_name_res = isset($post['chan_var_name_res'])?$post['chan_var_name_res']:'';

 
	
	$sql = "
	UPDATE dynroute 
	SET 
		displayname='$displayname', 
		sourcetype='mysql', 
		mysql_host='$mysql_host', 
		mysql_dbname='$mysql_dbname', 
		mysql_username='$mysql_username', 
		mysql_password='$mysql_password', 
		mysql_query='$mysql_query',
		announcement_id='$annmsg_id',  
		enable_dtmf_input='$enable_dtmf_input',  
		timeout='$timeout',  
		chan_var_name='$chan_var_name',  
		chan_var_name_res='$chan_var_name_res'  
	WHERE dynroute_id='$id'
	";
	sql($sql);

	// Delete all the old dests
	sql("DELETE FROM dynroute_dests where dynroute_id='$id'");
	// Now, lets find all the goto's in the post. Destinations return gotoN => foo and get fooN for the dest.
	// Is that right, or am I missing something?
	foreach(array_keys($post) as $var) {
		if (preg_match('/goto(\d+)/', $var, $match)) {
			// This is a really horrible line of code. take N, and get value of fooN. See above. Note we
			// get match[1] from the preg_match above
			$dest = $post[$post[$var].$match[1]];
			$cmd = $post['option'.$match[1]];
			// Debugging if it all goes pear shaped.
			// print "I think pushing $cmd does $dest<br>\n";
			if (strlen($cmd))
				dynroute_add_command($id, $cmd, $dest);
		}
	}
}


function dynroute_list() {
	global $db;

	$sql = "SELECT * FROM dynroute where displayname <> '__install_done' ORDER BY displayname";
        $res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
        if(DB::IsError($res)) {
		return null;
        }
        return $res;
}

function dynroute_get_details($id) {
	global $db;

	$sql = "SELECT * FROM dynroute where dynroute_id='$id'";
        $res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
        if(DB::IsError($res)) {
		return null;
        }
        return $res[0];
}

function dynroute_get_dests($id) {
	global $db;

	$sql = "SELECT selection, dest FROM dynroute_dests where dynroute_id='$id' ORDER BY selection";
        $res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
        if(DB::IsError($res)) {
                return null;
        }
        return $res;
}
	
function dynroute_get_name($id) {
	$res = dynroute_get_details($id);
	if (isset($res['displayname'])) {
		return $res['displayname'];
	} else {
		return null;
	}
}

function dynroute_check_destinations($dest=true) {
	global $active_modules;

	$destlist = array();
	if (is_array($dest) && empty($dest)) {
		return $destlist;
	}
	$sql = "SELECT dest, displayname, selection, a.dynroute_id dynroute_id FROM dynroute a INNER JOIN dynroute_dests d ON a.dynroute_id = d.dynroute_id  ";
	if ($dest !== true) {
		$sql .= "WHERE dest in ('".implode("','",$dest)."')";
	}
	$sql .= "ORDER BY displayname";
	$results = sql($sql,"getAll",DB_FETCHMODE_ASSOC);

	foreach ($results as $result) {
		$thisdest = $result['dest'];
		$thisid   = $result['dynroute_id'];
		$destlist[] = array(
			'dest' => $thisdest,
			'description' => sprintf(_("Route: %s / Option: %s"),$result['displayname'],$result['selection']),
			'edit_url' => 'config.php?display=dynroute&action=edit&id='.urlencode($thisid),
		);
	}
	return $destlist;
}
?>
