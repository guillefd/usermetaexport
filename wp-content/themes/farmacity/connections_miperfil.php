<?php
/*
Template Name: Connections MyProfile
*/

/* Get user info. */
global $current_user;
global $wp_roles;
global $wpdb;
get_currentuserinfo();

# INIT
$connection_shortcode_list = "[connections template='slim-plus' str_select='Seleccionar Lugar de Trabajo' str_image='No hay Foto disponible' page_limit=10]";


# get user connection  entry ID
$metadata = get_user_meta($current_user->data->ID, 'connections_entry_id');
$connid = isset($metadata[0]) && is_numeric($metadata[0]) ? $metadata[0] : false;
$entryslug = false;

if($connid)
{
	# get connection slug
	$q = $wpdb->get_row("SELECT slug FROM re5gu_connections WHERE id = ".$connid);
	if(isset($q->slug) && $q->slug!='')
	{
		$entryslug = $q->slug;
		$header =   '<ul id="cn-entry-actions">
						<li class="cn-entry-action-item">
							<a href="'.site_url().'/directorio/name/'.$entryslug.'/edit/"><i class="fa fa-edit"></i> Editar</a>
						</li>
					</ul>';
		$shortcode = "[connections id='".$connid."' template='slim-plus' show_alphaindex=FALSE enable_search=FALSE enable_category_select=FALSE]";			
	}
	else
		{
			$header = '<div class="alert alert-info">Tu perfil en el Directorio aun no ha sido creado.</div>';
			$shortcode = $connection_shortcode_list;
		}
}
else
	{
			$header = '<div class="alert alert-info">Tu perfil en el Directorio aun no ha sido creado.</div>';
			$shortcode = $connection_shortcode_list;
	}


########################
# VIEW
########################

get_header();
echo '<div style="margin:10px 20px;">';
	echo '<div class="subtitle"><h1 class="pix-page-title">Mi Perfil</h1></div>';
	echo $header;
	echo '<div class="entry-content entry">';
		echo do_shortcode($shortcode);
	echo '</div>';
echo '</div>';

get_footer();

?>
<script>
	$(document).ready(function(){
		$('.cn-detail.cn-clear.cn-hide').css('display','block');
	});
</script>