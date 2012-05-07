<?php 
// Dynamic routing modules
// Copied from ivr and calleridlookup modules
// John Fawcett Sept 2009



$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$id = isset($_REQUEST['id'])?$_REQUEST['id']:'';
$nbroptions = isset($_REQUEST['nbroptions'])?$_REQUEST['nbroptions']:'3';
$tabindex = 0;

switch ($action) {
	case "add":
		$id = dynroute_get_dynroute_id('Unnamed');
		// Set the defaults
		dynroute_sidebar($id);
		dynroute_show_edit($id, 3,  $def);
		$def['timeout']=5;
		break;
	case "edit":
		dynroute_sidebar($id);
		dynroute_show_edit($id, $nbroptions, $_POST);
		break;
	case "edited":
		if (isset($_REQUEST['delete'])) {
			sql("DELETE from dynroute where dynroute_id='$id'");
			sql("DELETE FROM dynroute_dests where dynroute_id='$id'");
			needreload();
		} else {
			dynroute_do_edit($id, $_POST);
			dynroute_sidebar($id);
			if (isset($_REQUEST['increase'])) 
				$nbroptions++;
			if (isset($_REQUEST['decrease'])) {
				$nbroptions--;
			}
			if ($nbroptions < 1)
				$nbroptions = 1;
			$url = 'config.php?type=setup&display=dynroute&action=edit&id='.$id.'&nbroptions='.$nbroptions;
			needreload();
			redirect($url);
			break;
		}
	default:
		dynroute_sidebar($id);
?>
<div class="content">
<h2><?php echo _("Routing"); ?></h2>
<h3><?php 
echo _("Instructions")."</h3>";
echo _("You use the Dynamic Routing module to route calls based on sql lookup.")."\n";
echo _("It is also possible to request user input (dtmf) and then use that in the query too.")."\n";
echo _("Optionally an announcement can be played before reading dtmf.")."\n";
echo _("You need to specify hostname, database name, username and password for mysql server.")."\n";
echo _("In the query you can use the special string [NUMBER] to be substituted by the incoming callerid number")."\n"; 
echo _("or the special string [INPUT] to be substituted by the captured dtmf.")."\n"; 
echo _("The selected field returned from the query is matched against the options text to decide which destination to use.")."\n"; 
echo _("Optionally you may define variable names in order to capture the dtmf input and / or query result. Those variables may")."\n"; 
echo _("be used later on a further pass through a dynroute inside the query string be enclosing in [] or in custom destinations.")."\n"; 
echo _("When refering to the variables in custom destinations (typically to pass to an agi script) DYNROUTE_ is prefixed to the variable name.")."\n"; 
echo _("You should define an option named default which will be used if no match is found. If you do not the call will be hang up on no match.")."\n"; 
echo _("If you have defined a default option you may also omit the mysql hostname and other parameters in order to bypass a query and")."\n";
echo _("procede with the default action. This is useful if you only want to capture dtmf into a variable without a mysql lookup.")."\n"; ?>
</div>

<?php
}


function dynroute_sidebar($id)  {
?>
        <div class="rnav"><ul>
        <li><a id="<?php echo empty($id)?'current':'nul' ?>" href="config.php?display=dynroute&amp;action=add"><?php echo _("Add Route")?></a></li>
<?php

        $dynroute_results = dynroute_list();
        if (isset($dynroute_results)){
                foreach ($dynroute_results as $tresult) {
                        echo "<li><a id=\"".($id==$tresult['dynroute_id'] ? 'current':'nul')."\" href=\"config.php?display=dynroute";
                        echo "&amp;action=edit&amp;id={$tresult['dynroute_id']}\">{$tresult['displayname']}</a></li>\n";
                }
        }
        echo "</ul></div>\n";
}

function dynroute_show_edit($id, $nbroptions, $post) {
	global $db;
	global $tabindex;

	$dynroute_details = dynroute_get_details($id);
	$dynroute_dests = dynroute_get_dests($id);
?>
	<div class="content">
	<h2><?php echo _("Dynamic Routes"); ?></h2>
	<h3><?php echo _("Edit Menu")." ".$dynroute_details['displayname']; ?></h3>
<?php 
?>
	<form name="prompt" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return prompt_onsubmit();">
	<input type="hidden" name="action" value="edited" />
	<input type="hidden" name="display" value="dynroute" />
	<input type="hidden" name="id" value="<?php echo $id ?>" />
	<input name="Submit" type="submit" style="display:none;" value="save" />
	<input name="delete" type="submit" value="<?php echo _("Delete")." "._("Route")." {$dynroute_details['displayname']}"; ?>" />
<?php
	if ($id) {
		$usage_list = framework_display_destination_usage(dynroute_getdest($id));
		if (!empty($usage_list)) {
		?>
			<br /><a href="#" class="info"><?php echo $usage_list['text']?>:<span><?php echo $usage_list['tooltip']?></span></a>
		<?php
		}
	}
	?>
	<table>
		<tr><td colspan=2><hr /></td></tr>
		<tr>
			<td><a href="#" class="info"><?php echo _("Change Name"); ?><span><?php echo _("This changes the short name, visible on the right, of this Route");?></span></a></td>
			<td><input type="text" name="displayname" value="<?php echo $dynroute_details['displayname'] ?>" tabindex="<?php echo ++$tabindex;?>"></td>
		</tr>
                <tr>
                        <td><a href="#" class="info"><?php echo _("Get DTMF input");?><span><?php echo _("If checked reads in DTMF digis, the value is available in the sql query with special name of [INPUT].");?></span></a></td>
                        <td><input type="checkbox" name="enable_dtmf_input" <?php echo $dynroute_details['enable_dtmf_input'] ?> tabindex="<?php echo ++$tabindex;?>"></td>
                </tr>
                <tr>
                        <td><a href="#" class="info"><?php echo _("Timeout");?><span><?php echo _("The amount of time (in seconds) to wait for input");?></span></a></td>
                        <td><input type="text" name="timeout" value="<?php echo $dynroute_details['timeout'] ?>" tabindex="<?php echo ++$tabindex;?>"></td>
                </tr>
                <tr>
                        <td><a href="#" class="info"><?php echo _("Input Variable");?><span><?php echo _("Optional variable name if you want the dmtf input to be available later in the call (e.g. futher dynamic route query or to pass to agi script)");?></span></a></td>
                        <td><input type="text" name="chan_var_name" value="<?php echo $dynroute_details['chan_var_name'] ?>" tabindex="<?php echo ++$tabindex;?>"></td>
                </tr>
 
<?php
        $annmsg_id = isset($dynroute_details['announcement_id'])?$dynroute_details['announcement_id']:'';
        if(function_exists('recordings_list')) { //only include if recordings is enabled ?>
                <tr>
                        <td><a href="#" class="info"><?php echo _("Announcement")?><span><?php echo _("Message to be played to the caller. To add additional recordings please use the \"System Recordings\" MENU to the left")?></span></a></td>
                        <td>
                                <select name="annmsg_id" tabindex="<?php echo ++$tabindex;?>">
                                <?php
                                        $tresults = recordings_list();
                                        echo '<option value="">'._("None")."</option>";
                                        if (isset($tresults[0])) {
                                                foreach ($tresults as $tresult) {
                                                        echo '<option value="'.$tresult['id'].'"'.($tresult['id'] == $annmsg_id ? ' SELECTED' : '').'>'.$tresult['displayname']."</option>\n";
                                                }
                                        }
                                ?>
                                </select>
                        </td>
                </tr>

<?php
        } else {
?>
                <tr>
                        <td><a href="#" class="info"><?php echo _("Announcement")?><span><?php echo _("Message to be played to the caller.<br><br>You must install and enable the \"Systems Recordings\" Module to edit this option")?></span></a></td>
                        <td>
                        <?php
                                $default = (isset($annmsg_id) ? $annmsg_id : '');
                        ?>
                                <input type="hidden" name="annmsg_id" value="<?php echo $default; ?>"><?php echo ($default != '' ? $default : 'None'); ?>
                        </td>
                </tr>
<?php
        }
?>

		<tr>
			<td><a href="#" class="info"><?php echo _("Host");?><span><?php echo _("Query");?></span></a></td>
			<td><input type="text" name="mysql_host" value="<?php echo $dynroute_details['mysql_host'] ?>" tabindex="<?php echo ++$tabindex;?>"></td>
		</tr>
		<tr>
			<td><a href="#" class="info"><?php echo _("Database");?><span><?php echo _("Query");?></span></a></td>
			<td><input type="text" name="mysql_dbname" value="<?php echo $dynroute_details['mysql_dbname'] ?>" tabindex="<?php echo ++$tabindex;?>"></td>
		</tr>
		<tr>
			<td><a href="#" class="info"><?php echo _("Username");?><span><?php echo _("Query");?></span></a></td>
			<td><input type="text" name="mysql_username" value="<?php echo $dynroute_details['mysql_username'] ?>" tabindex="<?php echo ++$tabindex;?>"></td>
		</tr>
		<tr>
			<td><a href="#" class="info"><?php echo _("Password");?><span><?php echo _("Query");?></span></a></td>
			<td><input type="text" name="mysql_password" value="<?php echo $dynroute_details['mysql_password'] ?>" tabindex="<?php echo ++$tabindex;?>"></td>
		</tr>
		<tr>
			<td><a href="#" class="info"><?php echo _("Query");?><span><?php echo _("Query");?></span></a></td>
			<td><input type="text" name="mysql_query" value="<?php echo $dynroute_details['mysql_query'] ?>" tabindex="<?php echo ++$tabindex;?>"></td>
		</tr>
                <tr>
                        <td><a href="#" class="info"><?php echo _("Result Variable");?><span><?php echo _("Optional variable name if you want the query result to be available later in the call (e.g. futher dynamic route query or to pass to agi script)");?></span></a></td>
                        <td><input type="text" name="chan_var_name_res" value="<?php echo $dynroute_details['chan_var_name_res'] ?>" tabindex="<?php echo ++$tabindex;?>"></td>
                </tr>
		<tr><td colspan=2><hr /></td></tr>
		<tr><td colspan=2>

			<input name="increase" type="submit" value="<?php echo _("Increase Options")?>">
			&nbsp;
			<input name="Submit" type="submit" value="<?php echo _("Save")?>" tabindex="<?php echo ++$tabindex;?>">
			&nbsp;
			<?php if ($nbroptions > 1) { ?>
			<input name="decrease" type="submit" value="<?php echo _("Decrease Options")?>">
			<?php } ?>
		</td>
	</tr>
	<tr><td colspan=2><hr /></td></tr>
<?php
	// Draw the destinations
	$dests = dynroute_get_dests($id);
	$count = 0;
	if (!empty($dests)) {
		foreach ($dests as $dest) {
			drawdestinations($count, $dest['selection'], $dest['dest']);
			$count++;
    }
	}
	while ($count < $nbroptions) {
		drawdestinations($count, null, null, 0);
		$count++;
	}
?>
	
</table>
<?php
	if ($nbroptions < $count) { 
		echo "<input type='hidden' name='nbroptions' value=$count />\n";
	} else {
		echo "<input type='hidden' name='nbroptions' value=$nbroptions />\n";
	} 

	global $module_hook;
	echo $module_hook->hookHtml;
?>
	<input name="increase" type="submit" value="<?php echo _("Increase Options")?>">
	&nbsp;
	<input name="Submit" type="submit" value="<?php echo _("Save")?>">
	&nbsp;
	<?php if ($nbroptions > 1) { ?>
	<input name="decrease" type="submit" value="<?php echo _("Decrease Options")?>">
	<?php } ?>
	
	<script language="javascript">
	<!--

var theForm = document.prompt;
theForm.displayname.focus();

	function prompt_onsubmit() {
		var msgInvalidOption = "<?php echo _("Invalid option"); ?>";
		
		defaultEmptyOK = true;

		// go thru the form looking for options
		// where the option isn't blank (as that will be removed) do the validation
	    var allelems = theForm.elements;
        if (allelems != null)
        {
        	var i, elem;
            for (i = 0; elem = allelems[i]; i++)
            {
            	if (elem.type == 'text' && elem.name.indexOf('option') == 0)
                {
                	if (elem.value != '') {
                    	if (!isRouteOption(elem.value))
                        	return warnInvalid(elem, msgInvalidOption);
                        
                        var gotoNum = elem.name.charAt(6);
                        var isok = validateSingleDestination(theForm,gotoNum,true);
                        if (!isok)
                        	return false;
                    }
                 }
          	}
        }
                              	
		return true;
	}
	
	//-->
	</script>
        </form>
        </div>


<?php

echo "</div>\n";
}

function drawdestinations($count, $sel,  $dest) { 
	global $tabindex
?>
	<tr> <td style="text-align:right;">

		<input size="10" type="text" name="option<?php echo $count ?>" value="<?php echo $sel ?>" tabindex="<?php echo ++$tabindex;?>"><br />
<?php if (strlen($sel)) {  ?>
		<i style='font-size: x-small'><?php echo _("Leave blank to remove");?></i>
<?php }  ?>
	</td>
		<td> <table> <?php echo drawselects($dest,$count); ?> </table> </td>
	</tr>
	<tr><td colspan=2><hr /></td></tr>
<?php
}

// this can be removed in 2.2 and put back to just runModuleSQL which is in admin/functions.inc.php
// I didn't want to do it in 2.1 as there's a significant user base out there, and it will break
// them if we do it here.

function localrunModuleSQL($moddir,$type){
        global $db;
        $data='';
        if (is_file("modules/{$moddir}/{$type}.sql")) {
                // run sql script
                $fd = fopen("modules/{$moddir}/{$type}.sql","r");
                while (!feof($fd)) {
                        $data .= fread($fd, 1024);
                }
                fclose($fd);

                preg_match_all("/((SELECT|INSERT|UPDATE|DELETE|CREATE|DROP).*);\s*\n/Us", $data, $matches);

                foreach ($matches[1] as $sql) {
                                $result = $db->query($sql);
                                if(DB::IsError($result)) {
                                        return false;
                                }
                }
                return true;
        }
                return true;
}

?>
