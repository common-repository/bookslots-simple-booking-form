<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://pluginette.com
 * @since      1.0.0
 *
 * @package    Bookslot
 * @subpackage Bookslot/admin/partials
 */
?>

<div id="bookslots-app">

  <?php
	Bookslots\Includes\view( 'partials/admin-header' );
	Bookslots\Includes\view( 'partials/new-employee' );
	?>

  <div class="wrap">
	<div id="icon-users" class="icon32"></div>
	<h2><?php esc_html_e( Bookslots\Includes\get_label( 'employees' ) ); ?> <a x-data id="add-employee-dipatcher" @click.prevent="$dispatch('e-new-employee', true)" href="#0" class="page-title-action">Add New</a></h2>
	<form>
	  <?php $data['employee_table']->display(); ?>
	</form>
  </div>

</div>
