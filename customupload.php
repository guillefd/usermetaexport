<?php

# get db data
require_once('../wp-config.php');


$q_connections = "SELECT *
					FROM
			    	re5gu_connections AS u
			     ";

$fieldsToFormat = [
    'notes'=>['explodenotes'],
	'options'=>['unserialize'],

];

$fieldsToUpdate = [
	'user'=>'notesdatawpuserid',
	'options'=>'addandserializeoptions-notesdatauseravatar',
	'notes'=>'blank',
];

$customFieldsToInsert = [
	'dni'=>'notedatadni'
];


############################################################################
# OK RUN
############################################################################

# connect
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if($db->connect_error)
{
	trigger_error('Database connection failed: '.$db->connect_error, E_USER_ERROR);
}

# run query
$connections = $db->query($q_connections);

# INIT
$result = new stdClass();
$result->connections = [];
$result->counts = [];


# Connections > check user query and iterate
if($connections === false) 
{
	set_db_error('connections');
}
else 
	{
  		$result->counts['connections'] = $connections->num_rows;	
		$connections->data_seek(0);
		while($row = $connections->fetch_assoc())
		{
			$result->connectionsrows[$row['id']] = $row;
		}		
	}


var_dump($result);




?>