<?php
	$uuid = $entry->getRuid();
	$categories = array();
	$categoryNames =array();

	foreach ( $entry->getCategory() as $category ) {
		$categories[] = $category->slug;
		$categoryNames[] = $category->name;
	}
?>


<ul id="cn-entry-actions">
	<li class="cn-entry-action-item">
		<a onclick="history.back();return false;" href="<?php echo site_url(); ?>/directorio/"><i class="fa fa-arrow-left"></i> Regresar al directorio</a>
	</li>
	<li class="cn-entry-action-item">
		<a href="<?php echo site_url(); ?>/directorio/name/<?php echo $entry->getSlug(); ?>/edit/"><i class="fa fa-edit"></i> Editar mi Perfil</a>
	</li>
</ul>

<div id="entry-id-<?php echo $uuid; ?>" class="cn-entry">

	<?php

	$open = in_array( 'show', $categories ) ? ' cn-open' : '';

	echo '<h3 class="cn-accordion-item" data-div-id="cn-detail-' , $uuid , '"' , ' style="border-bottom: ' , $atts['color'] , ' 1px solid; color:' , $atts['color'] , ';"><span class="cn-sprite' . $open . '" style="background-color: ' , $atts['color'] , ';"></span>' , $entry->getNameBlock( array( 'format' => $atts['name_format'] ) ) , '</h3>';

	#in_array( 'show', $categories ) ? $show = ' cn-show' : $show = ' cn-hide';

	$show = 'cn-show';

	echo '<div class="cn-detail cn-clear' . $show . '" id="cn-detail-' , $uuid , '">';

		echo '<div class="cn-left">';

			$entry->getImage( array(
				'image'    => $atts['image'],
				'height'   => 120,
				'width'    => 100,
				'fallback' => array(
					'type'     => $atts['image_fallback'],
					'string'   => $atts['str_image']
					)
				)
			);

		echo '</div>';

		echo '<div class="cn-right">';

			if ( $atts['show_title'] )$entry->getTitleBlock();

			if ( $atts['show_org'] ) $entry->getOrgUnitBlock();

			if ( $atts['show_contact_name'] )$entry->getContactNameBlock( array( 'format' => $atts['contact_name_format'] , 'label' => $atts['str_contact'] ) );

			if ( $atts['show_family'] )$entry->getFamilyMemberBlock();

			if ( $atts['show_addresses'] ) $entry->getAddressBlock( array( 'format' => $atts['addr_format'] , 'type' => $atts['address_types'] ) );

			if ( $atts['show_phone_numbers'] ) $entry->getPhoneNumberBlock( array( 'format' => $atts['phone_format'] , 'type' => $atts['phone_types'] ) );

			if ( $atts['show_email'] ) $entry->getEmailAddressBlock( array( 'format' => $atts['email_format'] , 'type' => $atts['email_types'] ) );

			if ( $atts['show_dates'] ) $entry->getDateBlock( array( 'format' => $atts['date_format'], 'type' => $atts['date_types'] ) );

			if ( $atts['show_links'] ) $entry->getLinkBlock( array( 'format' => $atts['link_format'], 'type' => $atts['link_types'] ) );

			if ( $atts['show_im'] ) echo $entry->getImBlock();

			if ( $atts['show_social_media'] ) echo $entry->getSocialMediaBlock();

			$entry->getContentBlock( $atts['content'], $atts, $template );

			if ( $atts['enable_bio'] && $entry->getBio() != '' ) {

				echo '<div class="cn-bio" id="cn-bio-' , $uuid , '">';

					if ( $atts['enable_bio_head'] ) echo '<h5>' , $atts['str_bio_head'] , '</h5>';

					echo $entry->getBioBlock();

				echo '</div>';
			}

			if ( $atts['enable_note'] && $entry->getNotes() != '' ) {

				echo '<div class="cn-notes" id="cn-bio-' , $uuid , '">';

					if ( $atts['enable_note_head'] )  echo '<h5>' , $atts['str_note_head'] , '</h5>';

					echo $entry->getNotesBlock();

				echo '</div>';
			}

		echo '</div>';

	echo '</div>';

	?>
</div>
