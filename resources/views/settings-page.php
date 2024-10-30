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
	Bookslots\Includes\view('partials/admin-header');
	Bookslots\Includes\view('appointments/new-appointment');
	$options = $data['options'];
	// dd($data);

	?>


	<div class="wrap">
		<div id="icon-users" class="icon32">
			<!-- <h2>Settings <a x-data id="add-new-booking" @click.prevent="$dispatch('e-open-new-appointment', true)" href="#0" class="page-title-action">Update</a></h2> -->
		</div>
		<h2>Settings</h2>

		<?php if (isset($_GET['status']) && 'error' === $_GET['status']) { ?>
			<div class="notice notice-error is-dismissible !m-0">
				<p>There is a problem with your form settings. Please check and save again.</p>
			</div>
		<?php } ?>

		<?php if (isset($_GET['status']) && 'success' === $_GET['status']) { ?>
			<div class="notice notice-success is-dismissible !m-0">
				<p>Settings saved successfully.</p>
			</div>
		<?php } ?>

		<div x-data="bookslots.settings" class="lg:tw-grid lg:tw-grid-cols-12 lg:tw-gap-x-5 tw-mt-10">
			<aside class="tw-py-6 tw-px-2 sm:tw-px-6 lg:tw-py-0 lg:tw-px-0 lg:tw-col-span-3">
				<nav class="tw-space-y-1">
					<!-- Current: "bg-gray-50 text-blue hover:text-blue hover:bg-white", Default: "text-gray-900 hover:text-gray-900 hover:bg-gray-50" -->
					<a @click.prevent="tab = 'general'" href="#" class="hover:tw-bg-white tw-group tw-rounded-md tw-px-3 tw-py-2 tw-flex tw-items-center tw-text-sm tw-font-medium" :class="tab == 'general' ? 'tw-text-blue hover:tw-text-blue tw-bg-gray-50' : 'tw-text-gray-900 hover:tw-text-gray-900'" aria-current="page">
						<svg class=" tw-flex-shrink-0 tw--ml-1 tw-mr-3 tw-h-6 tw-w-6" :class="tab == 'general' ? 'tw-text-blue group-hover:tw-text-blue' : 'tw-text-gray-400 group-hover:tw-text-gray-500'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
							<path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
						</svg>
						<span class="tw-truncate"> General </span>
					</a>

					<a @click.prevent="tab = 'labels'" href="#" class=" hover:tw-bg-gray-50 tw-group tw-rounded-md tw-px-3 tw-py-2 tw-flex tw-items-center tw-text-sm tw-font-medium" :class="tab == 'labels' ? 'tw-text-blue hover:tw-text-blue tw-bg-gray-50' : 'tw-text-gray-900 hover:tw-text-gray-900' ">

						<svg xmlns="http://www.w3.org/2000/svg" class="tw-flex-shrink-0 tw--ml-1 tw-mr-3 tw-h-6 tw-w-6" :class="tab == 'labels' ? 'tw-text-blue group-hover:tw-text-blue' : 'tw-text-gray-400 group-hover:tw-text-gray-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
							<path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
						</svg>
						<span class="tw-truncate"> Text & Labels </span>
					</a>

				</nav>
			</aside>

			<div class="sm:tw-px-6 lg:tw-px-0 lg:tw-col-span-9">
				<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">

					<div class="tw-border tw-border-gray-200 tw-shadow-sm sm:tw-overflow-hidden">

						<!-- general Tab -->
						<div x-show="tab == 'general'" class="tw-bg-white tw-py-6 tw-px-4 tw-space-y-6 sm:tw-p-6">
							<div>
								<h3 class="tw-text-lg tw-leading-6 tw-font-medium tw-text-gray-900">General</h3>
								<p class="tw-mt-1 tw-text-sm tw-text-gray-500">Change style and appearance here.</p>
							</div>

							<div class="tw-space-y-6 sm:tw-space-y-5">
								<div class="sm:tw-grid sm:tw-grid-cols-3 sm:tw-gap-4 sm:tw-items-start sm:tw-border-t sm:tw-border-gray-200 sm:tw-pt-5">
									<label for="first-name" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 sm:tw-mt-px sm:tw-pt-2">
										Theme
									</label>
									<div class="tw-mt-1 sm:tw-mt-0 sm:tw-col-span-2">
										<select id="theme" name="general[theme]" autocomplete="theme-name" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue focus:border-blue sm:text-sm">
											<option value="default">Default</option>
										</select>
									</div>
								</div>

								<div class="sm:tw-grid sm:tw-grid-cols-3 sm:tw-gap-4 sm:tw-items-start sm:tw-border-t sm:tw-border-gray-200 sm:tw-pt-5">
									<label for="last-name" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 sm:tw-mt-px sm:tw-pt-2">
										Position
									</label>
									<div class="tw-mt-1 sm:tw-mt-0 sm:tw-col-span-2">
										<select id="position" name="general[position]" autocomplete="position-name" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue focus:border-blue sm:text-sm">
											<option value="left" <?php selected('left', $options['general']['position'], true); ?>>Left</option>
											<option value="middle" <?php selected('middle', $options['general']['position'], true); ?>>Middle</option>
											<!-- <option value="right" <?php // selected('right', $options['general']['position'], true);
																									?>>Right</option> -->
										</select>
									</div>
								</div>

								<!-- <div class="sm:tw-grid sm:tw-grid-cols-3 sm:tw-gap-4 sm:tw-items-start sm:tw-border-t sm:tw-border-gray-200 sm:tw-pt-5">
								<label for="email" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 sm:tw-mt-px sm:tw-pt-2">
									Email address
								</label>
								<div class="tw-mt-1 sm:tw-mt-0 sm:tw-col-span-2">
									<input id="email" name="email" type="email" autocomplete="email" class="tw-block tw-max-w-lg tw-w-full tw-shadow-sm focus:tw-ring-indigo-500 focus:tw-border-indigo-500 sm:tw-text-sm tw-border-gray-300 tw-rounded-md">
								</div>
								</div> -->

								<!-- <div class="sm:tw-grid sm:tw-grid-cols-3 sm:tw-gap-4 sm:tw-items-start sm:tw-border-t sm:tw-border-gray-200 sm:tw-pt-5">
									<label for="country" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 sm:tw-mt-px sm:tw-pt-2">
										Allow Employees timezone
									</label>
									<div class="tw-mt-1 sm:tw-mt-0 sm:tw-col-span-2">
										<select id="employee-timezone" name="general[employee-timezone]" autocomplete="employee-timezone" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue focus:border-blue sm:text-sm">
											<option>Select</option>
											<option value="yes">Yes</option>
											<option value="no">No</option>
										</select>
									</div>
								</div> -->

							</div>

						</div>

						<div x-show="tab == 'labels'" class="tw-bg-white tw-py-6 tw-px-4 tw-space-y-6 sm:tw-p-6">
							<div>
								<h3 class="tw-text-lg tw-leading-6 tw-font-medium tw-text-gray-900">Text & Labels</h3>
								<p class="tw-mt-1 tw-text-sm tw-text-gray-500">Change the text of your bookslots here.</p>
							</div>

							<div class="tw-grid tw-grid-cols-6 tw-gap-6">
								<div class="tw-col-span-6 sm:tw-col-span-4">
									<label for="form_title" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700">Form Title</label>
									<input type="text" name="labels[form_title]" id="form_title" value="<?php echo isset($options['labels']['form_title']) ? esc_attr($options['labels']['form_title']) : ''; ?>" autocomplete="family-name" class="tw-mt-1 tw-block tw-w-full tw-border tw-border-gray-300 tw-rounded-md tw-shadow-sm tw-py-2 tw-px-3 focus:tw-outline-none focus:tw-ring-blue focus:tw-border-blue sm:tw-text-sm">
								</div>

								<div class="tw-col-span-6 sm:tw-col-span-3">
									<label for="services" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700">Service (Singular)</label>
									<input type="text" name="labels[service]" id="services" value="<?php echo isset($options['labels']['service']) ? esc_attr($options['labels']['service']) : ''; ?>" autocomplete="family-name" class="tw-mt-1 tw-block tw-w-full tw-border tw-border-gray-300 tw-rounded-md tw-shadow-sm tw-py-2 tw-px-3 focus:tw-outline-none focus:tw-ring-blue focus:tw-border-blue sm:tw-text-sm">
								</div>

								<div class="tw-col-span-6 sm:tw-col-span-3">
									<label for="services" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700">Service (Plural)</label>
									<input type="text" name="labels[services]" id="services" value="<?php echo isset($options['labels']['services']) ? esc_attr($options['labels']['services']) : ''; ?>" autocomplete="family-name" class="tw-mt-1 tw-block tw-w-full tw-border tw-border-gray-300 tw-rounded-md tw-shadow-sm tw-py-2 tw-px-3 focus:tw-outline-none focus:tw-ring-blue focus:tw-border-blue sm:tw-text-sm">
								</div>

								<div class="tw-col-span-6 sm:tw-col-span-3">
									<label for="employees" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700">Employee (singular)</label>
									<input type="text" name="labels[employee]" id="employees" value="<?php echo isset($options['labels']['employee']) ? esc_attr($options['labels']['employee']) : ''; ?>" autocomplete="employees" class="tw-mt-1 tw-block tw-w-full tw-border tw-border-gray-300 tw-rounded-md tw-shadow-sm tw-py-2 tw-px-3 focus:tw-outline-none focus:tw-ring-indigo-500 focus:tw-border-indigo-500 sm:tw-text-sm">
								</div>

								<div class="tw-col-span-6 sm:tw-col-span-3">
									<label for="employees" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700">Employees (plural)</label>
									<input type="text" name="labels[employees]" id="employees" value="<?php echo isset($options['labels']['employees']) ? esc_attr($options['labels']['employees']) : ''; ?>" autocomplete="employees" class="tw-mt-1 tw-block tw-w-full tw-border tw-border-gray-300 tw-rounded-md tw-shadow-sm tw-py-2 tw-px-3 focus:tw-outline-none focus:tw-ring-indigo-500 focus:tw-border-indigo-500 sm:tw-text-sm">
								</div>
							</div>
						</div>

						<div class="tw-px-4 tw-py-3 tw-bg-gray-50 tw-text-right sm:tw-px-6">
							<button type="submit" class="tw-bg-blue tw-border tw-border-transparent tw-rounded-md tw-shadow-sm tw-py-2 tw-px-4 tw-inline-flex tw-justify-center tw-text-sm tw-font-medium tw-text-white hover:tw-bg-blue focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-offset-2 focus:tw-ring-blue">Save</button>
						</div>
					</div>

					<input type="hidden" name="action" value="bookslots_update_options">
					<?php wp_nonce_field('bookslots_update_options', '___bookslots_nonce'); ?>
				</form>
			</div>
		</div>


	</div>










</div>