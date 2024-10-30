<div id="plugin-profiles">

<div class="-ml-20 tw-bg-white"></div>
<?php
	$subpage = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // WPCS: Input var ok.
?>
<!-- This example requires Tailwind CSS v2.0+ -->
<div style="margin-left: -20px;" class="-tw-ml-20 tw-bg-white">
  <div class="tw-mx-auto tw-px-2 lg:tw-max-w-7xl xl:tw-max-w-full sm:tw-px-6 lg:tw-px-6">
	<div class="tw-justify-between tw-items-center tw-flex tw-relative">
	  <div class="tw-flex-1 tw-justify-between tw-items-center tw-flex">
		<div class="tw-flex-shrink-0 sm:tw-items-center tw-flex tw-justify-between tw-w-full">
		  <!-- <span class="tw-font-semibold tw-text-lg">Bookslots</span> -->

			<img class="tw-border-none" src="<?php echo esc_url( BOOKSLOTS_RESOURCES . 'images/logoo.svg' ); ?>" />


		  <div class="mt-4 sm:tw-mt-0 sm:tw-ml-10">
			<nav class="tw--mb-px tw-space-x-8 tw-flex">
			  <!-- Current: "border-blue-500 text-blue-600", Default: "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" -->
			  <a href="<?php echo esc_url( menu_page_url( 'bookslots', false ) ); ?>" class="focus:tw-shadow-none tw-border-transparent tw-border-b-2 tw-text-gray-800 tw-font-normal tw-text-sm tw-py-4 tw-px-4 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap<?php echo $subpage == 'bookslots' ? ' tw-text-wp-blue tw-bg-gray tw-border-wp-blue tw-border-b-4' : ''; ?>">
				<?php esc_html_e( 'Appointments', 'bookslots' ); ?>
			  </a>
			  <a href="<?php echo esc_url( menu_page_url( 'bookslots-services', false ) ); ?>" class="focus:tw-shadow-none tw-border-transparent tw-border-b-2 tw-text-gray-800 tw-font-normal tw-text-sm tw-py-4 tw-px-1 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap<?php echo $subpage == 'bookslots-services' ? ' tw-text-wp-blue tw-border-wp-blue tw-border-b-4' : ''; ?>">
				<?php esc_html_e( Bookslots\Includes\get_label( 'services' ) ); ?>
			  </a>
			  <a href="<?php echo esc_url( menu_page_url( 'bookslots-employees', false ) ); ?>" class="focus:tw-shadow-none tw-border-transparent tw-border-b-2 tw-text-gray-800 tw-font-normal tw-text-sm tw-py-4 tw-px-1 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap<?php echo $subpage == 'bookslots-employees' ? ' tw-text-wp-blue tw-border-wp-blue tw-border-b-4' : ''; ?>">
				<?php esc_html_e( Bookslots\Includes\get_label( 'employees' ) ); ?>
			  </a>
			  <a href="<?php echo esc_url( menu_page_url( 'bookslots-settings', false ) ); ?>" class="focus:tw-shadow-none tw-border-transparent tw-border-b-2 tw-text-gray-800 tw-font-normal tw-text-sm tw-py-4 tw-px-1 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap<?php echo $subpage == 'bookslots-settings' ? ' tw-text-wp-blue tw-border-wp-blue tw-border-b-4' : ''; ?>">
				<?php esc_html_e( 'Settings', 'bookslots' ); ?>
			  </a>
			</nav>
		  </div>

		</div>
	  </div>
	  <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">

	  </div>
	</div>
  </div>

</div>
