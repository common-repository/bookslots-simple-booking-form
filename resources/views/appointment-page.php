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
Bookslots\Includes\view( 'partials/new-appointment' );
?>

<div class="wrap">
  <div id="icon-users" class="icon32"></div>
  <h2>Appointments <a x-data id="add-new-booking" @click.prevent="$dispatch('e-open-new-appointment', true)" href="#0" class="page-title-action">Add New</a></h2>
  <form>
	<?php $data['table']->display(); ?>
  </form>
</div>

</div>
