<?php

# get db data
require_once('../wp-config.php');


$q_connections = "SELECT 
						id,
						entry_type,
						visibility,
						first_name,
						last_name,
						title,
						organization,
						department,
						phone_numbers,
						email,
						dates,
						birthday,
						bio,
						notes,
						options,
						user,						
						cm.meta_value AS dni
					FROM
			    		re5gu_connections AS c
			    	LEFT OUTER JOIN 
			    		re5gu_connections_meta AS cm ON c.id = cm.entry_id
			     ";

$fieldsToFormat = [
	#field 	  #runFunction
    'notes'=>['explodenotes'],
	'options'=>['unserialize'],

];

$fieldsToUpdate = [
    #field 	 #runFunction
	'user'=>'linkwithWPuser',
	'options'=>'setUserimage',
	'notes'=>'blank',
];

$customFieldsToInsert = [
	#metakey #value
	'dni'=>'dni'
];


############################################################################
# CONNECT AND SET DATA
############################################################################

# INIT
$result = new stdClass();
$result->counts = [];
$result->connections = [];
$result->raw = [];

# connect
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if($db->connect_error)
{
	trigger_error('Database connection failed: '.$db->connect_error, E_USER_ERROR);
}

# run query
$connections = $db->query($q_connections);

# Connections > check user query and iterate
if($connections === false) 
{
	set_db_error('connections');
}
else 
	{
  		$result->counts['raw'] = $connections->num_rows;	
  		$result->counts['connections'] = 0;	
		$connections->data_seek(0);
		while($row = $connections->fetch_assoc())
		{
			$id = $row['id'];
			# save raw
			$result->raw[$id] = $row;		
			# set connection
			$result->connections[$id] = setConnectionField($row);
			$result->counts['connections']++;
		}		
	}


# set connection fields to update
foreach($result->connections as $conn)
{
	foreach($conn as $field=>$value)
	{
		if(array_key_exists($field, $fieldsToUpdate))
		{

		}
	}
}


############################################################################
# UPDATE CONNECTION DB DATA
############################################################################

if($_POST && $_POST['action'])
{
	var_dump($_POST);
}



#############
# HELPERS   #
#############

function setConnectionField($row)
{
	global $fieldsToFormat;
	$connection = [];

	foreach($row as $field=>$value)
	{
		if(array_key_exists($field, $fieldsToFormat))
		{
			switch($field)
			{
				case 'options':
								$connection[$field] = $value;
								$connection['_'.$field] = unserialize($value);
								break;

				case 'notes':
								$connection[$field] = $value;
								if($value!='')
								{
									$explodedfields = explode(';', $value);
									foreach($explodedfields as $rawfield)
									{								
										$arr = explode('|', $rawfield);
										$connection[$arr[0]] = $arr[1];
									}
								}	
								break;
			}
		}
	}
	return $connection;
}

# INSERT CUSTOM DATA TO CONNECTIONS

function insertConnectionEntryCustomField()
{

}





############################################################################
# PRINT 
############################################################################


ini_set('xdebug.var_display_max_depth', 10);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);
var_dump($result);


?>

<style>
	ul{
		list-style: none;
	}
	ul li span{
		display: inline-block;
		width: 175px;
	}
	form{
		margin: 30px;
	}
	label{
		display: block;
	}
	input{
		margin: 10px;
	}
	button{
		margin: 10px 0;
	}
	table{
		width:100%;
	}
</style>

<h2>Update Connection Entries</h2>
<hr>

<ul>
	<li><span>Entries:</span><?php echo $result->counts['raw'] ?></li>
	<li><span>Connections to update:</span><?php echo $result->counts['connections'] ?></li>
</ul>

<form method="POST">
	<label>Confirm update</label>
	<input name="action" type="hidden" value="runupdate">	
	<button type="submit">Update all entries</button>
</form>

<table>
	<thead>
		<th>ID</th>
		<th>Nombre/Apellido</th>
		<th>Organization</th>
		<th>wpuserid</th>
	</thead>	
	<?php foreach($result->raw as $id=>$conn): ?>

		<tr>
			<td><?php echo $id; ?></td>
			<td><?php echo $conn['first_name']; ?> <?php $conn['last_name']; ?></td>
			<td><?php echo $conn['organization']; ?></td>
			<td><?php echo $conn['user']; ?></td>
		</tr>	

	<?php endforeach; ?>
</table>