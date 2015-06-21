<?php

ini_set('max_execution_time', 600); 

# get db data
require_once('../wp-config.php');

################
# 1 - INIT
################


define('WPUPLOADDIR', WP_CONTENT_DIR.'/uploads/');
define('CONNIMAGEPATH','wp-content/uploads/connections-images/');

$result = new stdClass();
$result->counts = [];
$result->connections = [];
$result->raw = [];
$result->updatedata = [];

$q_connections = "SELECT 
						id,
						slug,
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
						user
					FROM
			    		re5gu_connections AS c
			     ";

############################################################################
# 2 - CONNECT AND SET DATA
############################################################################

# connect
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if($db->connect_error)
{
	trigger_error('Database connection failed: '.$db->connect_error, E_USER_ERROR);
}

# run query
$connections = $db->query($q_connections);


############################################################################
# 3 - SET RESULT
############################################################################

$fieldsToFormat = [
	#field 	  #runFunctions
    'id'=>['copyvalue'],
    'slug'=>['copyvalue'],
    'notes'=>['copyvalue','explodevalue'],
	'options'=>['copyvalue','unserialize'],
	'user'=>['copyvalue'],

];

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
			setResultConnection($row);
			$result->counts['connections']++;
		}		
	}

function setResultConnection($row)
{
	global $result;
	global $fieldsToFormat;
	
	$connection = [];
	$id = $row['id'];
	foreach($row as $field=>$value)
	{
		if(array_key_exists($field, $fieldsToFormat))
		{
			$functionstorun = $fieldsToFormat[$field];
			foreach($functionstorun as $function)
			{	
				switch($function)
				{
					case 'unserialize':
									$connection['_'.$field] = unserialize($value);
									break;

					case 'explodevalue':
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

					case 'copyvalue':
									$connection[$field] = $value;
									break;		
				}
			}	
		}
	}
	$result->connections[$id] = $connection;
}

############################################################################
# UPDATE CONNECTIONS VALUES
############################################################################

$connectionfieldsToUpdate = [
    #field 	 #runFunction
	'user'=>'linkwithWPuser',
	'options'=>'setUserimage',
	'notes'=>'blank',
];

# set connection fields to update
foreach($result->connections as $connID=>$conn)
{
	foreach($conn as $field=>&$value)
	{
		if(array_key_exists($field, $connectionfieldsToUpdate))
		{
			$connid = $conn['id'];
			$function = $connectionfieldsToUpdate[$field];
			switch($function)
			{
				case 'linkwithWPuser':
										setConnection_wpuserid($connid);
										break;

				case 'setUserimage':
										setConnection_imageAvatar($connid);
										break;

				case 'blank':
										$result->connections[$connid][$field] = '';
										break;
			}
		}
	}
}

function setConnection_wpuserid($connid)
{
	global $result;

	# si userid esta seteado
	if(isset($result->connections[$connid]['wpuserid']))
	{
		# set connection user field value
		$result->connections[$connid]['user'] = $result->connections[$connid]['wpuserid']; 
	}
}

function setConnection_imageAvatar($id)
{
	global $result;

	# init
	$connimageuri = site_url().'/wp-content/uploads/connections-images/';
	$connimagepath = ABSPATH.CONNIMAGEPATH;
	$userslug = $result->connections[$id]['slug'];
	$filename = false;

	# set filename
	$useravataruri = $result->connections[$id]['useravatar'];
	if($useravataruri!='')
	{
		# extract filename, filepath and set fileinfo
		$filename = extract_useravatar_filename($useravataruri);
		$filepath = extract_useravatar_filepath($filename, $useravataruri);	
		$fileinfo = getFileInfo($filename, $filepath);	
		$imagecopied = copyimagetofolder($filename, $filepath, $fileinfo, $userslug);
	}
	# set connection image
	if($filename && $filepath && $fileinfo && $imagecopied)
	{	
		# set Array
		$imageArr = [
			'linked'=>true,
			'display'=>true,
			'name'=>[
					'original'=>$filename, # filename			
				],
			'meta'=>[
					'original'=>[
						'name'=>$filename, #filename
						'path'=>$connimagepath.$userslug.'/'.$filename,
						'url'=>$connimageuri.$userslug.'/'.$filename,
						'width'=>$fileinfo->width,
						'height'=>$fileinfo->height,
						'size'=>$fileinfo->attr,
						'mime'=>$fileinfo->mime,
						'type'=>$fileinfo->type,
					],
			],
		];
		$result->connections[$id]['_options']['image'] = $imageArr;
	}	
}

function extract_useravatar_filename($string)
{
	# init
	$filename = false;

	# get last occurrence
	$pos = strrpos($string, '/');	
	if($pos)
	{	
		$substring = substr($string, $pos+1);
		$check = count(explode('.', $substring))>=2 ? true : false;
		$filename = $check ? $substring : false; 
	}
	return $filename;
}

function extract_useravatar_filepath($filename, $useravataruri)
{	
	$uripath = 'http://www.comunidadfarmacity.com/wp-content/uploads/';
	$path = false;
	if($filename)
	{
		# slice uri path
		$subpath = str_replace($uripath,'',$useravataruri);
		$subpath = str_replace($filename,'',$subpath);
		$path = WPUPLOADDIR.$subpath;
	}
	return $path;
}

function getFileInfo($file, $path)
{
	$info = false;
	if($file && $path)
	{
		# get size
		$size = getimagesize($path.$file);
		# set info
		$info = new stdClass();
		$info->width = $size[0];
		$info->height = $size[1];
		$info->type = $size[2];
		$info->attr = $size[3];
		$info->mime	= $size['mime'];	
	}	
	return $info;
}

function copyimagetofolder($file, $path, $info, $userslug)
{
	$copied = false;
	if($file && $path && $info)
	{
		$source = $path.$file;
		$destinationpath = ABSPATH.CONNIMAGEPATH.$userslug;
		$direxists = false;
		if(file_exists($destinationpath) == false)
		{
			$direxists = mkdir($destinationpath, 0750);
		}
		else
			{
				$direxists = true;	
			}
		if($direxists)
		{
			$copied = copy($source, $destinationpath.'/'.$file);
		}	
	}
	return $copied;
}


############################################################################
# UPDATE DB > RESULT CONNECTION 
############################################################################

$DBfieldsToUpdate = [
	'user'=>'user',
	'notes'=>'notes',
	'options'=>'options',
];
# serialize field '_options' and set to connections
foreach($result->connections as $connid=>&$conn)
{
	$conn['options'] = serialize($conn['_options']);
}

# set array data update
foreach($result->connections as $connid=>$conn)
{
	$result->updatedata[] = [
								'data'=>[
									'user'=>$conn['user'],
									'notes'=>$conn['notes'],
									'options'=>$conn['options'],
								],
								'where'=>[
									'id'=>$connid,
								],
						];
}

# RUN UPDATE
if($_POST && $_POST['action'])
{
	# use WP database global object
	global $wpdb;

	# update
	foreach($result->updatedata as &$row)
	{
		$row['result'] = $wpdb->update('re5gu_connections', $row['data'], $row['where']);
	}	

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
		margin: 25px;
		border: 1px solid #ededed;
		padding: 10px;
	}
	th{
		text-align: left;
	}
	td{
		padding:5px;
		border: 1px solid #ededed;
	}
	td.thopt{
		width: 200px;
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
		<th></th>
		<th>user</th>
		<th>notes</th>
		<th>options</th>
		<th></th>
	</thead>	
	<?php foreach($result->raw as $id=>$conn): ?>

		<tr>
			<td><?php echo $id; ?></td>
			<td><?php echo $conn['first_name']; ?> <?php $conn['last_name']; ?></td>
			<td><?php echo $conn['organization']; ?></td>
			<td></td>
			<td><?php echo $result->connections[$id]['user']; ?></td>
			<td><?php echo $result->connections[$id]['notes']; ?></td>
			<td class="thopt"><?php echo $result->connections[$id]['options']; ?></td>
		</tr>	

	<?php endforeach; ?>
</table>