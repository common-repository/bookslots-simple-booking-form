<!-- This example requires Tailwind CSS v2.0+ -->
<div x-data="bookslots.services" x-show="open" @e-open-new-service.window="open = true" @e-edit-service.window="prepareEdit($event.detail.id)" class="tw-relative tw-z-10" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
  <!-- Background backdrop, show/hide based on slide-over state. -->
  <div class="tw-fixed tw-inset-0"></div>
  <form @submit.prevent="submitForm">

	<div class="tw-fixed tw-inset-0 tw-overflow-hidden">
	  <div class="tw-absolute tw-inset-0 tw-overflow-hidden">
		<div class="tw-pointer-events-none tw-fixed tw-inset-y-0 tw-right-0 tw-flex tw-max-w-full tw-pl-10" style="top: 32px;">

		  <div x-show="open" x-transition:enter="tw-transform tw-transition tw-ease-in-out tw-duration-500 sm:tw-duration-700" x-transition:enter-start="tw-translate-x-full" x-transition:enter-end="tw-translate-x-0" x-transition:leave="tw-transform tw-transition tw-ease-in-out tw-duration-500 sm:tw-duration-700" x-transition:leave-start="tw-translate-x-0" x-transition:leave-end="tw-translate-x-full" class="tw-w-screen tw-max-w-2xl tw-pointer-events-auto" x-description="Slide-over panel, show/hide based on slide-over state." @click.away="open = false">
			<div class="tw-flex tw-h-full tw-flex-col tw-divide-y tw-divide-gray-200 tw-bg-white tw-shadow-xl">
			  <div class="tw-flex tw-min-h-0 tw-flex-1 tw-flex-col tw-overflow-y-scroll tw-py-0" style="background: #f0f0f1">
				<div class="tw-px-4 sm:tw-px-6 tw-py-6 tw-bg-white">
				  <div class="tw-flex tw-items-start tw-justify-between">

					<template x-if="mode == 'new'">
					  <div>
						<h2 class="tw-text-lg tw-font-medium tw-text-gray-900" id="slide-over-title">New Service</h2>
						<p class="tw-mt-1 tw-max-w-2xl tw-text-sm tw-text-gray-500">
						  This information will be displayed publicly so be careful what you share.
						</p>
					  </div>
					</template>

					<template x-if="mode == 'edit'">
					  <div>
						<h2 class="tw-text-lg tw-font-medium tw-text-gray-900" id="slide-over-title">Edit Service</h2>
						<p class="tw-mt-1 tw-max-w-2xl tw-text-sm tw-text-gray-500">
						  This information will be displayed publicly so be careful what you share.
						</p>
					  </div>
					</template>


					<div @click="open = false" class="tw-ml-3 tw-flex tw-h-7 tw-items-center">
					  <button type="button" class="tw-rounded-md tw-bg-white tw-text-gray-400 hover:tw-text-gray-500 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-indigo-500">
						<span class="tw-sr-only">Close panel</span>
						<!-- Heroicon name: outline/x -->
						<svg class="tw-h-6 tw-w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
						  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
						</svg>
					  </button>
					</div>
				  </div>
				</div>
				<div class="tw-relative tw-my-6 tw-flex-1 tw-px-4 sm:tw-px-0 tw-w-3/4 tw-mx-auto">
				  <div class="tw-space-y-8 tw-divide-y tw-divide-gray-200">
					<div class="tw-space-y-8 tw-divide-y tw-divide-gray-200 sm:tw-space-y-5">
					  <div>

						<div class="tw-mt-6 sm:tw-mt-5 tw-space-y-6 sm:tw-space-y-5">
						  <div class="sm:tw-grid sm:tw-grid-cols-3 sm:tw-gap-4 sm:tw-items-start sm:tw-pt-5">
							<label for="title" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 sm:tw-mt-px sm:tw-pt-2">
							  Title
							</label>
							<div class="tw-mt-1 sm:tw-mt-0 sm:tw-col-span-2">
							  <input x-model="form.title" type="text" name="title" id="title" autocomplete="given-name" class="tw-max-w-lg tw-block tw-w-full tw-shadow-sm focus:tw-ring-indigo-500 focus:tw-border-indigo-500 sm:tw-max-w-xs sm:tw-text-sm tw-border-gray-300 tw-rounded-md">
							  <div x-show="errorMessages.title" class="tw-text-red-500 tw-font-semibold tw-text-sm tw-mt-1">
								<small x-text="errorMessages.title"></small>
							  </div>
							</div>
						  </div>

						  <div class="sm:tw-grid sm:tw-grid-cols-3 sm:tw-gap-4 sm:tw-items-start sm:tw-border-t sm:tw-border-gray-200 sm:tw-pt-5">
							<label for="description" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 sm:tw-mt-px sm:tw-pt-2">
							  Description
							</label>
							<div class="tw-mt-1 sm:tw-mt-0 sm:tw-col-span-2">
							  <textarea x-model="form.description" id="description" name="description" rows="3" class="tw-max-w-lg tw-shadow-sm tw-block tw-w-full focus:tw-ring-indigo-500 focus:tw-border-indigo-500 sm:tw-text-sm tw-border tw-border-gray-300 tw-rounded-md"></textarea>
							  <p class="tw-mt-2 tw-text-sm tw-text-gray-500">Write a few sentences about yourself.</p>
							  <div x-show="errorMessages.description" class="tw-text-red-500 tw-font-semibold tw-text-sm tw-mt-1">
								<small x-text="errorMessages.description"></small>
							  </div>
							</div>
						  </div>

						  <div class="sm:tw-grid sm:tw-grid-cols-3 sm:tw-gap-4 sm:tw-items-start sm:tw-border-t sm:tw-border-gray-200 sm:tw-pt-5">
							<label for="duration" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 sm:tw-mt-px sm:tw-pt-2">
							  Duration
							</label>
							<div class="tw-mt-1 sm:tw-mt-0 sm:tw-col-span-2">
							  <input x-model="form.duration" type="number" name="duration" id="duration" autocomplete="given-name" class="tw-max-w-lg tw-block tw-w-full tw-shadow-sm focus:tw-ring-indigo-500 focus:tw-border-indigo-500 sm:tw-max-w-xs sm:tw-text-sm tw-border-gray-300 tw-rounded-md">
							  <div x-show="errorMessages.duration" class="tw-text-red-500 tw-font-semibold tw-text-sm tw-mt-1">
								<small x-text="errorMessages.duration"></small>
							  </div>
							</div>
						  </div>

						  <div class="sm:tw-grid sm:tw-grid-cols-3 sm:tw-gap-4 sm:tw-items-start sm:tw-border-t sm:tw-border-gray-200 sm:tw-pt-5">
							<label for="interval" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 sm:tw-mt-px sm:tw-pt-2">
							  Interval
							</label>
							<div class="tw-mt-1 sm:tw-mt-0 sm:tw-col-span-2">
							  <input x-model="form.interval" type="number" name="interval" id="interval" autocomplete="given-name" class="tw-max-w-lg tw-block tw-w-full tw-shadow-sm focus:tw-ring-indigo-500 focus:tw-border-indigo-500 sm:tw-max-w-xs sm:tw-text-sm tw-border-gray-300 tw-rounded-md">
							  <div x-show="errorMessages.interval" class="tw-text-red-500 tw-font-semibold tw-text-sm tw-mt-1">
								<small x-text="errorMessages.interval"></small>
							  </div>
							</div>
						  </div>

						</div>
					  </div>

					  <div class="tw-py-8 tw-space-y-6 sm:tw-pt-10 sm:tw-space-y-5">
						<div class="sm:tw-border-b sm:tw-border-gray-200 tw-pb-3">
						  <h3 class="tw-text-lg tw-leading-6 tw-font-medium tw-text-gray-900">
							<?php esc_html_e( Bookslots\Includes\get_label( 'employees' ) ); ?>
						  </h3>
						  <p class="tw-mt-1 tw-max-w-2xl tw-text-sm tw-text-gray-500">
							This service is provided by these people
						  </p>
						</div>



						<ul x-cloak="" class="tw-pt-1" x-show="selectedEmployees">
						  <template x-for="member in selectedEmployees" :key="member.id">
							<li class="tw-flex tw-items-center tw-mb-2">
							  <span class="tw-mr-3 tw-text-sm" x-text="member.name"></span>
							  <a href="#0" x-on:click="removeEmployee(member.id)">
								<svg xmlns="http://www.w3.org/2000/svg" class="tw-w-6 tw-h-6 tw-text-gray-700 hover:tw-text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
								  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
								</svg>
							  </a>
							</li>
						  </template>
						</ul>



						<div class="tw-space-y-6 sm:tw-space-y-5 ">

						  <div class="sm:tw-grid sm:tw-grid-cols-3 sm:tw-gap-4 sm:tw-items-start  sm:tw-pt-5">
							<label for="employee" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 sm:tw-mt-px sm:tw-pt-2">
							  Employee
							</label>
							<div class="tw-mt-1 sm:tw-mt-0 sm:tw-col-span-2">
							  <select @change="addEmployee($event.target.value)" id="employee" name="employee" autocomplete="employee-name" class="tw-max-w-lg tw-block focus:tw-ring-indigo-500 focus:tw-border-indigo-500 tw-w-full tw-shadow-sm sm:tw-max-w-xs sm:tw-text-sm tw-border-gray-300 tw-rounded-md">
								<option value="">Select Employee</option>
								<template x-for="employee in employees">
								  <option :value="employee.id" x-text="employee.name"></option>
								</template>
							  </select>
							  <div x-show="errorMessages.employees" class="tw-text-red-500 tw-font-semibold tw-text-sm tw-mt-1">
								<small x-text="errorMessages.employees"></small>
							  </div>
							</div>
						  </div>

						</div>
					  </div>
					</div>
				  </div>

				  <!-- /End replace -->
				</div>
			  </div>
			  <div x-show="!success" class="tw-flex tw-flex-shrink-0 tw-justify-end tw-px-4 tw-py-1">
				<button x-show="mode == 'new'" type="submit" class="tw-ml-4 tw-inline-flex tw-justify-center tw-py-2 tw-px-4 tw-text-sm tw-font-medium tw-text-wp-blue focus:tw-outline-none focus:tw-ring-0 focus:tw-ring-offset-0">
				  <svg x-show="submitLoading" class="tw-animate-spin tw--ml-1 tw-mr-3 tw-h-5 tw-w-5 tw-text-wp-blue" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
					<circle class="tw-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
					<path class="tw-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
				  </svg>
				  Add
				</button>
				<button x-show="mode == 'edit'" type="submit" class="tw-ml-4 tw-inline-flex tw-justify-center tw-py-2 tw-px-4 tw-text-sm tw-font-medium tw-text-wp-blue focus:tw-outline-none focus:tw-ring-0 focus:tw-ring-offset-0">
				  <svg x-show="submitLoading" class="tw-animate-spin tw--ml-1 tw-mr-3 tw-h-5 tw-w-5 tw-text-wp-blue" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
					<circle class="tw-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
					<path class="tw-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
				  </svg>
				  Update
				</button>
			  </div>


			  <div x-show="success" class="tw-rounded-md tw-bg-green-50 tw-p-3">
				<div class="tw-flex">
				  <div class="tw-flex-shrink-0">
					<svg class="tw-h-4 tw-w-4 tw-text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
					  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
					</svg>
				  </div>
				  <div class="tw-ml-3">
					<p class="tw-text-xs tw-font-medium tw-text-green-800">
					  Success! Refreshing page ...
					</p>
				  </div>
				</div>
			  </div>

			</div>
		  </div>

		</div>
	  </div>
	</div>
  </form>

</div>
