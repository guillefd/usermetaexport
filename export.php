<?php

# get db data
require_once('../wp-config.php');

# queries
$q_allusers = "SELECT 
			    u.ID,
			    u.user_login,
			    u.user_email,
			    u.user_nicename,
			    u.user_registered,
			    u.user_status AS status,
			    u.display_name,
			    um1.meta_value AS departamento,
			    um2.meta_value AS description,
			    um3.meta_value AS dia_cumple,
			    um4.meta_value AS mes_cumple,
			    um5.meta_value AS dni,
			    um6.meta_value AS estado,
			    um7.meta_value AS fecha_de_ingreso
			FROM
			    re5gu_users AS u
			        LEFT JOIN
			    re5gu_usermeta um1 ON u.ID = um1.user_id
			        LEFT JOIN
			    re5gu_usermeta um2 ON u.ID = um2.user_id
			        LEFT JOIN
			    re5gu_usermeta um3 ON u.ID = um3.user_id
			        LEFT JOIN
			    re5gu_usermeta um4 ON u.ID = um4.user_id
			        LEFT JOIN
			    re5gu_usermeta um5 ON u.ID = um5.user_id
			        LEFT JOIN
			    re5gu_usermeta um6 ON u.ID = um6.user_id
			        LEFT JOIN
			    re5gu_usermeta um7 ON u.ID = um7.user_id
			WHERE
			    um1.meta_key = 'departamento'
			        AND um2.meta_key = 'description'
			        AND um3.meta_key = 'dia_cumple'
			        AND um4.meta_key = 'mes_cumple'
			        AND um5.meta_key = 'dni'
			        AND um6.meta_key = 'estado'
			        AND um7.meta_key = 'fecha_de_ingreso'
			";

$q_usersextra = "SELECT 
			    u.ID,
			    u.user_login,
			    um8.meta_value AS fecha_de_nacimiento,
			    um9.meta_value AS first_name,
			    um10.meta_value AS last_name,
			    um11.meta_value AS lugar_de_trabajo,
			    um12.meta_value AS nickname,
			    um13.meta_value AS posicion,
			    um14.meta_value AS telefono
			FROM
			    re5gu_users AS u
			        LEFT JOIN
			    re5gu_usermeta um8 ON u.ID = um8.user_id
			        LEFT JOIN
			    re5gu_usermeta um9 ON u.ID = um9.user_id
			        LEFT JOIN
			    re5gu_usermeta um10 ON u.ID = um10.user_id
			        LEFT JOIN
			    re5gu_usermeta um11 ON u.ID = um11.user_id
			        LEFT JOIN
			    re5gu_usermeta um12 ON u.ID = um12.user_id
			        LEFT JOIN
			    re5gu_usermeta um13 ON u.ID = um13.user_id
			        LEFT JOIN
			    re5gu_usermeta um14 ON u.ID = um14.user_id
			WHERE
			        um8.meta_key = 'fecha_de_nacimiento'
			        AND um9.meta_key = 'first_name'
			        AND um10.meta_key = 'last_name'
			        AND um11.meta_key = 'lugar_de_trabajo'
			        AND um12.meta_key = 'nickname'
			        AND um13.meta_key = 'posicion'
			        AND um14.meta_key = 'telefono'		        
";

$q_usersavatar = "SELECT 
			    u.ID,
			    u.user_login,
			    um15.meta_value AS user_avatar
			FROM
			    re5gu_users AS u
			        LEFT JOIN
			    re5gu_usermeta um15 ON u.ID = um15.user_id
			WHERE
			        um15.meta_key = 'user_avatar'			        
";

# Arrays
$fieldsToExport = [
					'users'=>[
						'ID','user_login','user_email','user_nicename','user_registered','status','display_name',
						'departamento','description','dia_cumple','mes_cumple','dni','estado','fecha_de_ingreso'
					],
					'usersextra'=>[
						'fecha_de_nacimiento','first_name','last_name','lugar_de_trabajo','nickname','posicion','telefono'
					],
					'usersavatar'=>[
						'user_avatar'
					],
					'fields_to_format'=>[
						'display_name'=>['lowercase','capitalize'],
						'departamento'=>['lowercase','capitalize','lowercaseSpecialChar'],
						'description'=>['lowercase','capitalize'],
						'first_name'=>['lowercase','capitalize'],
						'last_name'=>['lowercase','capitalize'],
						'lugar_de_trabajo'=>['setLugardeTrabajo'],
						'posicion'=>['lowercase','capitalize'],
						#'user_email'=>['fillblankemailaddress'], 
						'fecha_de_nacimiento'=>['format_date'],
						'fecha_de_ingreso'=>['format_date'],
					],
];

# Plugin Connections > fields map
$fieldsToHeaderMap = [
                'entry_type'=>'Entry Type',
				'ID'=>'user', #custom upload (this ID does the link with WP user)
				#'user_login'=>'',
				'user_email'=>'Email | Email Trabajo',
				#'user_nicename'=>'',
				#'user_registered'=>'',
				#'status'=>'',
				#'display_name'=>'',
				'departamento'=>'Department',
				'description'=>'Biography',
				#'dia_cumple'=>'',
				#'mes_cumple'=>'',
				'dni'=>'DNI', #custom upload
				#'estado'=>'',
				'fecha_de_ingreso'=>'Date | Empleo', #format date
				'fecha_de_nacimiento'=>utf8_decode('Date | Cumpleaños'), #format date
				'first_name'=>'First Name',
				'last_name'=>'Last Name',
				#'lugar_de_trabajo'=>'',
				#'nickname'=>'',
				'posicion'=>'Title',
				'telefono'=>utf8_decode('Phone | Teléfono Trabajo'),
				#'user_avatar'=>'', #custom upload
				'visibility'=>'Visibility',
				'categories'=>'Categories',
				'organization'=>'Organization',
				'notes'=>'Notes', # for use in next step
];

# Special chars
$specialCharMap = [
					'Á'=>'á',
					'É'=>'é',
					'Í'=>'í',
					'Ó'=>'ó',
					'Ú'=>'ú',
];

$categories = [
	'variantes'=>[
					'CASA CENTRAL'=>'casa-central',
					'CASA CENTRAL.'=>'casa-central',
					'Oficinas Centrales'=>'casa-central',
					'CENTROS DE DISTRIBUCION'=>'centro-distribucion',
					'CENTRO DE DISTRIBUCION'=>'centro-distribucion',
					'FARMACIAS'=>'farmacias',
					'FARMACITY'=>'farmacias',
					'SIMPLICITY'=>'simplicity',
					'LOOK'=>'look',
					],		
	'definidas'=>[
					'casa-central'=>'Casa Central',
					'centro-distribucion'=>'Centro de Distribución',					
					'farmacias'=>'Farmacias',
					'simplicity'=>'Simplicity',
					'look'=>'Look',
					],				
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

# run query1
$users = $db->query($q_allusers);
# run query2
$usersextra = $db->query($q_usersextra);
# run query3
$usersavatar = $db->query($q_usersavatar);

# INIT
$result = new stdClass();
$result->userscount = [];
$result->usersextracount = [];
$result->usersavatarcount = [];
$result->usersnotfound = [];
$result->errors = [];
$result->userrows = [];
$result->userextrarows = [];
$result->useravatarrows = [];
$result->users = [];

$userArr = [];
$allfields = array_merge(
							$fieldsToExport['users'], 
							$fieldsToExport['usersextra'], 
							$fieldsToExport['usersavatar']);
foreach($allfields as $field)
{
	$userArr[$field] = '';
}

# USERS > check user query and iterate
if($users === false) 
{
	set_db_error('users');
}
else 
	{
  		$result->userscount = $users->num_rows;	
		$users->data_seek(0);
		while($row = $users->fetch_assoc())
		{
			$result->userrows[$row['ID']] = $row;
			$user = $userArr;
			foreach($fieldsToExport['users'] as $field)
			{
				$user[$field] = format_field_value($field, $row);
			}
			$result->users[$row['ID']] = $user;
			unset($user);
		}		
	}

# USERS EXTRA > check userextra query and iterate
if($usersextra === false) 
{
	set_db_error('usersextra');
}
else 
	{
  		$result->usersextracount = $usersextra->num_rows;	
		$usersextra->data_seek(0);
		while($row = $usersextra->fetch_assoc())
		{
			$result->userextrarows[$row['ID']] = $row;
			if(isset($result->users[$row['ID']]))
			{
				foreach($fieldsToExport['usersextra'] as $field)
				{
					$result->users[$row['ID']][$field] = format_field_value($field, $row);
				}	
			}
			else
				{
					# not found
					$result->usersnotfound[] = $row['ID'];
				}		
		}		
	}

# USERS AVATAR > check useravatar query and iterate
if($usersavatar === false) 
{
	set_db_error('usersavatar');
}
else 
	{
  		$result->usersavatarcount = $usersavatar->num_rows;	
		$usersavatar->data_seek(0);
		while($row = $usersavatar->fetch_assoc())
		{
			$result->useravatarrows[$row['ID']] = $row;
			if(isset($result->users[$row['ID']]))
			{
				foreach($fieldsToExport['usersavatar'] as $field)
				{
					$result->users[$row['ID']][$field] = format_field_value($field, $row);
				}	
			}
			else
				{
					# not found
					$result->usersnotfound[] = $row['ID'];
				}		
		}		
	}

# add custom data
foreach($result->users as $userid=>&$user)
{
	$user['entry_type'] = 'individual';
	$user['visibility'] = 'public';
	$user['categories'] = $categories['definidas'][$user['lugar_de_trabajo']];
	$user['organization'] = $categories['definidas'][$user['lugar_de_trabajo']];
	
	# data to be used in next Step > custom data (customupload.php)
	$user['notes'] = 'wpuserid@'.$user['ID'].'|'
					.'useravatar@'.$user['user_avatar'].'|'
					.'dni@'.$user['dni'];
}


### functions

function set_db_error($query)
{
	$result->errors[][$query] = trigger_error('Error: ' . $db->error, E_USER_ERROR);	
}

function format_field_value($field, $row)
{
	global $specialCharMap;
	global $fieldsToExport;
	global $categories;
	
	$fieldsToFormat = $fieldsToExport['fields_to_format'];
	$value = $row[$field];

	if(array_key_exists($field, $fieldsToFormat))
	{
		$formatsToRun = $fieldsToFormat[$field];
		foreach($formatsToRun as $format)
		{ 
			switch($format)
			{
				case 'lowercase':
									if($value!='')
									{
										$value = strtolower($value);
									}	
									break;

				case 'capitalize':
									if($value!='')
									{
										$value = ucwords($value);	
									}	
									break;

				case 'lowercaseSpecialChar':
									if($value!='')
									{
										foreach($specialCharMap as $char=>$replace)
										{
											# UTF decoding
											$char = utf8_decode($char);
											$replace = utf8_decode($replace);
											# find position
											$pos = strpos($value, $char);
											if($pos!==false)
											{	
												# replace
												$value = str_replace($char,$replace,$value);
											}
										}		
									}			
									break;

				case 'fillblankemailaddress':
									$value = trim($value);
									if($value=='')
									{	
										$defaultuser = $row['dni'];
										$defaultdomain = '@email.com';
										$value = $defaultuser.$defaultdomain;	
									}	
									break;	

				case 'setLugardeTrabajo':
										if($value!='')
										{
											$value = trim($value);
											if(array_key_exists($value, $categories['variantes']))
											{
												$catslug = $categories['variantes'][$value];
												$value = $catslug;
											}
											else
												{
													$value = 'UNDEFINED';
												}
										}
										break;						

				case 'format_date':
										if($value!='')
										{
											$valueArr = explode('/', $value);	
											$value = date('m/d/Y', mktime(0,0,0,$valueArr[1], $valueArr[0], $valueArr[2]));
										}
										break;						
			}
		}	
	}
	return $value;
}


//////////// WRITE CSV ////////////

$headers = [];
$list = [];
foreach($result->users as $user)
{
	if(is_array($user))
	{
		$list[] = $user;
	}	
}
$userrow = array_pop($result->users);

foreach($userrow as $field=>$value)
{
	$headers[] = isset($fieldsToHeaderMap[$field]) ? $fieldsToHeaderMap[$field] : $field;
}

$file = fopen(date('Y-m-d').'_all_users_exported.csv', 'w');

# write headers
fputcsv($file, $headers);

# write values
foreach($list as $fields) 
{
    fputcsv($file, $fields);
}
fclose($file);

var_dump('users count', $result->userscount);
var_dump('users extra count', $result->usersextracount);
var_dump('usersavatarcount', $result->usersavatarcount);
var_dump('not found', $result->usersnotfound);
var_dump($result->users);

?>



<!-- //////////// VIEW //////////// -->
<html>
<head>
	<meta charset="ISO-8859-1">
</head>
<style>
	.bigpanel{
		width:2500px;
	}
	h5{
		margin: 10px;
		font-size: 14px;
	}
	h5 span{
		display: inline-block;
		width: 150px;
	}
</style>
<body>
<h2>User Export</h2>
<hr>
<h5><span>User rows:</span><?php echo count($result->userrows); ?></h5>
<h5><span>User extra rows:</span><?php echo count($result->userextrarows); ?></h5>
<h5><span>Users exported:</span><?php echo $result->userscount; ?></h5>
<hr>

<?php 
		$tcount = 0;
		$show = 10; 
?>
<div class="bigpanel">
<?php foreach($result->users as $user): ?>

	<?php if(is_array($user)): ?>
	
		<?php 
				$tcount++;

				$fields = count($user);
				$fcount = 0; 
		?>		
		<?php foreach($user as $field=>$value): ?>

			<?php echo '"'.$value.'"'; ?>
			<?php 
					$fcount++;
					if($fcount<$fields) echo ', '; 
			?>

		<?php endforeach; ?>
		<br>
	<?php endif; ?>

	<?php if($tcount>=$show) break; ?>

<?php endforeach; ?>	
</div>

</body>
</html>




