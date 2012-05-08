<?php
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }

sql('DROP TABLE IF EXISTS dynroute');
sql('DROP TABLE IF EXISTS dynroute_dests');
~ 
