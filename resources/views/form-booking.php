<div id="bookslots-app">
	<div x-data="bookslots" class="tw-bg-gray-100 tw-max-w-sm tw-p-5 tw-rounded-lg tw-relative <?php echo esc_attr( $data['position_css'] ); ?>" x-ref="bookingForm">
		<h4 class="tw-pt-3 tw-pb-6"><?php esc_html_e( Bookslots\Includes\get_label( 'form_title' )); ?></h4>
		<form x-show="!confirmed" action="" @submit.prevent="createBooking">

			<!-- Select a Service Field -->
			<div class="tw-mb-6">
				<label for="" class="tw-inline-block tw-text-gray-700 tw-font-bold tw-mb-2 tw-sr-only"><?php esc_html_e( Bookslots\Includes\get_label( 'services' ) ); ?></label>
				<select x-model="form.service" @change="errorMessages.employees = ''" name="" id="" class="tw-bg-white tw-h-10 tw-w-full tw-border-none tw-rounded-lg">
					<option value=""><?php esc_html_e( 'Select ', 'bookslots' ); ?><?php esc_html_e( Bookslots\Includes\get_label( 'service' ) ); ?></option>
					<template x-for="service in services" :key="service.ID">
						<option :value="service.ID" x-text="`${service.post_title} (${service.duration} minutes)`"></option>
					</template>
				</select>
				<div x-show="errorMessages.employees" class="tw-text-red-500 tw-font-semibold tw-text-sm tw-mt-1">
					<span x-text="errorMessages.employees"></span>
				</div>
			</div>


			<!-- Select a Provider field -->
			<div class="tw-mb-6" :class="{'tw-opacity-25' : employees.length < 1}">
				<label for="" class="tw-inline-block tw-text-gray-700 tw-font-bold tw-mb-2 tw-sr-only"><?php esc_html_e( Bookslots\Includes\get_label( 'employees' ) ); ?></label>
				<select x-model="form.employee" name="" id="" class="tw-bg-white tw-h-10 tw-w-full tw-border-none tw-rounded-lg" :disabled="employees.length < 1">
				<option value=""><?php esc_html_e( 'Select ', 'bookslots' ); ?><?php esc_html_e( Bookslots\Includes\get_label( 'employee' ) ); ?></option>
					<template x-for="employee in employees" :key="employee.ID">
						<option :value="employee.ID" x-text="employee.name"></option>
					</template>
				</select>

			</div>

			<!-- Calendar -->
			<div class="tw-mb-6" :class="{'tw-opacity-25' : !showCalender()}">
				<div class="tw-bg-white tw-rounded-lg">
					<div class="tw-flex tw-items-center tw-justify-center tw-relative">
						<!-- Left Arrow button -->
						<button @click="decrementCalendarWeek()" :disabled="!showCalender()" type="button" class="tw-bg-none hover:tw-bg-transparent focus:tw-bg-transparent tw-p-4 tw-absolute tw-left-0 tw-top-0">
							<svg xmlns="http://www.w3.org/2000/svg" class="tw-h-6 tw-w-6 tw-text-gray-300 hover:tw-text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
							</svg>
						</button>

						<!-- Month & Year -->
						<div x-text="monthYear" class="tw-text-lg tw-font-semibold tw-p-4">
						</div>

						<!-- Right Arrow button -->
						<button @click="incrementCalendarWeek()" :disabled="!showCalender()" type="button" class="tw-bg-none hover:tw-bg-transparent focus:tw-bg-transparent tw-p-4 tw-absolute tw-right-0 tw-top-0">
							<svg xmlns="http://www.w3.org/2000/svg" class="tw-h-6 tw-w-6 tw-text-gray-300 hover:tw-text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
							</svg>
						</button>
					</div>

					<!-- Days -->
					<div class="tw-flex tw-justify-between tw-items-center tw-px-3 tw-border-b tw-border-gray-200 tw-pb-2">
						<template x-for="(day, index) in weekInterval" :key="index">
							<button @click="selectDay(index)" :disabled="!showCalender()" type="button" class="tw-group tw-bg-none hover:tw-bg-transparent focus:tw-bg-transparent tw-text-center tw-cursor-pointer focus:tw-outline-none">
								<div x-text="day.word" class="tw-text-xs tw-leading-none tw-mb-2 tw-text-gray-700"></div>
								<div x-text="day.number" class="tw-text-lg tw-leading-none tw-p-1 tw-rounded-full tw-w-8 tw-h-8 tw-group-hover:tw-bg-gray-200 group-hover:tw-bg-gray-200 group-hover:tw-text-gray-800 tw-outline-none tw-text-gray-800 tw-flex tw-items-center tw-justify-center" :class="{'tw-bg-gray-200': form.day != undefined && day.timestamp == form.day.timestamp}">
								</div>
							</button>
						</template>
					</div>

					<!-- Time Slots -->
					<div x-show="slots.length" class="tw-overflow-y-scroll no-scrollbar" style="max-height: 13rem;">
						<!-- <div x-show="slots.length > 1" class="tw-overflow-y-scroll" style="max-height: 13rem;"> -->
						<template x-for="(slot, index) in slots" :key="index">
							<div>
								<!-- <p>hello</p> -->
								<input type="radio" name="time" :id="`time_`+slot.timestamp" :value="slot.timestamp" x-model="form.time" class="tw-sr-only" />
								<label for="" @click="form.time = slot.timestamp" class="tw-w-full tw-text-left focus:tw-outline-none tw-px-4 tw-py-2 tw-flex tw-cursor-pointer tw-items-center tw-border-b tw-border-gray-100 tw-justify-between">
									<span x-text="slot.time"></span>
									<svg x-show="slot.timestamp == form.time" xmlns="http://www.w3.org/2000/svg" class="tw-h-5 tw-w-5 tw-text-gray-700 tw-cursor-pointer" viewBox="0 0 20 20" fill="currentColor">
										<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
									</svg>
								</label>
							</div>
						</template>
					</div>

					<div x-show="slots.length < 1" class="tw-text-center tw-text-gray-700 tw-px-4 tw-py-2">
						<?php esc_html_e( 'No available slots', 'bookslots' ); ?>
					</div>

				</div>
			</div>

			<template x-if="hasDetailsToBook()">
				<div class="tw-mb-6 tw-space-y-2 tw-text-base">
					<div class="tw-text-gray-700 tw-font-semibold tw-mb-2 ">
						<?php esc_html_e( 'You\'re ready to book', 'bookslots' ); ?>
					</div>

					<div x-text="`${selected.service.post_title} (${selected.service.duration} minutes) with ${selected.employee.name} on ${new Date(form.time*1000)}`" class="tw-border-gray-300 tw-border-t tw-py-2 font-sm">
					</div>

				</div>
			</template>



			<button type="submit" :class="{'tw-opacity-100' : hasDetailsToBook()}" class="tw-rounded-lg tw-opacity-25 tw-bg-indigo-500 tw-text-white tw-text-center tw-font-bold tw-px-4 tw-w-full tw-h-11">
				<?php esc_html_e( 'Book now', 'bookslots' ); ?>
			</button>

		</form>


		<div x-show="confirmed" class="tw-flex tw-items-end sm:tw-items-center tw-justify-center tw-min-h-full tw-p-4 tw-text-center sm:tw-p-0">
			<div class="tw-relative tw-bg-white tw-rounded-lg tw-px-4 tw-pt-5 tw-pb-4 tw-text-left tw-overflow-hidden tw-shadow-xl tw-transform tw-transition-all sm:tw-my-8 sm:tw-max-w-sm sm:tw-w-full sm:tw-p-6">
				<div>
					<div class="tw-mx-auto tw-flex tw-items-center tw-justify-center tw-h-12 tw-w-12 tw-rounded-full tw-bg-green-100">
						<!-- Heroicon name: outline/check -->
						<svg class="tw-h-6 tw-w-6 tw-text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
						</svg>
					</div>
					<div class="tw-mt-3 tw-text-center sm:tw-mt-5">
						<h3 class="tw-text-lg tw-leading-6 tw-font-medium tw-text-gray-900" id="modal-title">Booking successful</h3>
						<div class="tw-mt-2">
							<p class="tw-text-sm tw-text-gray-500" x-text="`${selected.service.post_title} (${selected.service.duration} minutes) with ${selected.employee.name} on ${new Date(form.time*1000)}`"></p>
							<!-- <div x-text="`${selected.service.post_title} (${selected.service.duration} minutes) with ${selected.employee.name} on ${new Date(form.time*1000)}`" class="tw-border-gray-300 tw-border-b tw-border-t tw-py-2 font-sm"> -->

						</div>
					</div>
				</div>
				<div class="tw-mt-5 sm:tw-mt-6">
					<button @click="reset" type="button" class="tw-inline-flex tw-justify-center tw-w-full tw-rounded-md tw-border tw-border-transparent tw-shadow-sm tw-px-4 tw-py-2 tw-bg-indigo-600 tw-text-base tw-font-medium tw-text-white hover:tw-bg-indigo-700 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-offset-2 focus:tw-ring-indigo-500 sm:tw-text-sm">Book again</button>
				</div>
			</div>
		</div>





		<!-- <template  x-if="loading"> -->
			<div x-cloak x-show="loading" class="tw-bg-opacity-75 tw-absolute tw-bg-gray-300 tw-w-full tw-h-full tw-top-0 tw-left-0 tw-flex tw-justify-center tw-items-center">
				<svg class="tw-w-12 tw-h-12" version="1.1" id="L3" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
					<circle fill="none" stroke="#fff" stroke-width="4" cx="50" cy="50" r="44" style="opacity:0.8;" />
					<circle fill="#fff" class="tw-text-indigo-500" stroke="currentColor" stroke-width="3" cx="8" cy="54" r="6">
						<animateTransform attributeName="transform" dur="2s" type="rotate" from="0 50 48" to="360 50 52" repeatCount="indefinite" />

					</circle>
				</svg>
			</div>
		<!-- </template> -->

	</div>

</div>
