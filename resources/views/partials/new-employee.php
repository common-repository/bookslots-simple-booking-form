<!-- This example requires Tailwind CSS v2.0+ -->
<div x-data="bookslots.employees" x-show="open" @e-new-employee.window="prepareNew()" @e-edit-employee.window="prepareEdit($event.detail.id)" class="tw-relative tw-z-10" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
  <!-- Background backdrop, show/hide based on slide-over state. -->
  <div class="tw-fixed tw-inset-0"></div>
  <form @submit.prevent="submitForm">

    <div class="tw-fixed tw-inset-0 tw-overflow-hidden">
      <div class="tw-absolute tw-inset-0 tw-overflow-hidden">
        <div class="tw-pointer-events-none tw-fixed tw-inset-y-0 tw-right-0 tw-flex tw-max-w-full tw-pl-10" style="top: 32px;">

          <div x-show="open" x-transition:enter="tw-transform tw-transition tw-ease-in-out tw-duration-500 sm:tw-duration-700" x-transition:enter-start="tw-translate-x-full" x-transition:enter-end="tw-translate-x-0" x-transition:leave="tw-transform tw-transition tw-ease-in-out tw-duration-500 sm:tw-duration-700" x-transition:leave-start="tw-translate-x-0" x-transition:leave-end="tw-translate-x-full" class="tw-w-screen tw-max-w-2xl tw-pointer-events-auto" x-description="Slide-over panel, show/hide based on slide-over state." @click.away="open = false, refresh()">
            <div class="tw-flex tw-h-full tw-flex-col tw-divide-y tw-divide-gray-200 tw-bg-white tw-shadow-xl">
              <div class="tw-flex tw-min-h-0 tw-flex-1 tw-flex-col tw-overflow-y-scroll tw-py-0" style="background: #f0f0f1">
                <div class="tw-px-4 sm:tw-px-6 tw-py-6 tw-bg-white">
                  <div class="tw-flex tw-items-start tw-justify-between">

                    <template x-if="mode == 'new'">
                      <div>
                        <h2 class="tw-text-lg tw-font-medium tw-text-gray-900" id="slide-over-title">New Employee</h2>
                        <p class="tw-mt-1 tw-max-w-2xl tw-text-sm tw-text-gray-500">
                          This information will be displayed publicly so be careful what you share.
                        </p>
                      </div>
                    </template>

                    <template x-if="mode == 'edit'">
                      <div>
                        <h2 class="tw-text-lg tw-font-medium tw-text-gray-900" id="slide-over-title">Edit Employee</h2>
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
                              User
                            </label>
                            <div class="tw-relative tw-mt-1 sm:tw-mt-0 sm:tw-col-span-2">
                              <input x-show="selectedUsers.length == 0" x-model="userSearch" type="text" name="title" id="title" autocomplete="given-name" class="tw-max-w-lg tw-block tw-w-full tw-shadow-sm focus:tw-ring-indigo-500 focus:tw-border-indigo-500 sm:tw-max-w-xs sm:tw-text-sm tw-border-gray-300 tw-rounded-md">
                              <p x-show="selectedUsers.length == 0" class="tw-mt-2 tw-text-xs tw-text-gray-500">Search for a WordPress user by typing at least two characters.</p>

                              <div x-show="errorMessages.title" class="tw-text-red-500 tw-font-semibold tw-text-sm tw-mt-1">
                                <small x-text="errorMessages.title"></small>
                              </div>

                              <ul x-cloak="" class="" x-show="selectedUsers">
                                <template x-for="member in selectedUsers" :key="member.ID">
                                  <li class="tw-flex tw-items-center tw-mb-0 tw-text-sm tw-font-medium tw-text-gray-700 sm:tw-mt-px sm:tw-pt-1">
                                    <span class="tw-mr-3" x-text="member.name"></span>
                                    <a href="#0" x-on:click="removeUser(member)">
                                      <svg xmlns="http://www.w3.org/2000/svg" class="tw-w-6 tw-h-6 tw-text-gray-700 hover:tw-text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                      </svg>
                                    </a>
                                  </li>
                                </template>
                              </ul>

                              <div x-show="users.length > 0 && userSearch.length >= 2" @click.away="closeUserSearchDropdown()" class="tw-absolute tw-left-0 tw-w-full tw-mt-2 tw-origin-top-right tw-bg-white tw-rounded-md tw-shadow-lg tw-ring-1 tw-ring-black tw-ring-opacity-5 focus:tw-outline-none tw-z-50" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                <div class="tw-py-1" role="none">
                                  <template x-for="user in users">
                                    <a x-text="user.name" @click.prevent="addUser(user)" href="#" class="tw-block tw-px-4 tw-py-2 tw-text-sm tw-text-gray-700 hover:tw-text-wp-blue hover:tw-border-transparent" role="menuitem" tabindex="-1" id="menu-item-0"></a>
                                  </template>
                                </div>
                              </div>


                            </div>
                          </div>

                        </div>
                      </div>

                      <!-- <div class="tw-py-8 tw-space-y-6 sm:tw-pt-10 sm:tw-space-y-5"> -->
                      <div x-show="selectedUsers.length > 0" class="tw-py-8 tw-space-y-6 sm:tw-pt-10 sm:tw-space-y-5">
                        <div>
                          <h3 class="tw-text-lg tw-leading-6 tw-font-medium tw-text-gray-900">
                            Schedule
                          </h3>
                          <p class="tw-mt-1 tw-max-w-2xl tw-text-sm tw-text-gray-500">
                            Set the availability of this employee
                          </p>
                        </div>
                        <div class="tw-space-y-6 sm:tw-space-y-5">

                          <div class="tw-py-5 tw-px-1">


                            <!-- Type -->
                            <!-- <div class=" tw-space-y-1 tw-px-0 sm:tw-space-y-0 sm:tw-grid sm:tw-grid-cols-3 sm:tw-gap-4 sm:tw-px-0 sm:tw-py-3">
                              <div>
                                <label for="project-description" class="tw-block tw-text-sm tw-font-medium tw-text-gray-900 sm:tw-mt-px sm:tw-pt-1">Timezone</label>
                              </div>
                              <div class="sm:tw-col-span-2">

                                <select x-model="form.schedule.timezone" name="timezone" id="timezone" class="tw-border-gray-300 sm:tw-text-sm tw-bg-white tw-w-full tw-rounded-lg">
                                  <?php // echo wp_timezone_choice(); ?>
                                </select>

                                <div x-show="getNested(errorMessages, 'schedule','availability', 'datetype')" class="tw-error-text">
                                  <small x-text="getNested(errorMessages, 'schedule','availability', 'datetype')"></small>
                                </div>
                              </div>
                            </div> -->

                            <fieldset class="tw-mb-4">
                              <div class="tw-bg-white tw-rounded-md tw--space-y-px tw-flex">

                                <label @click="scheduleType = 'availability'" class="tw-rounded-tl-md tw-rounded-bl-md tw-relative tw-border tw-p-4 tw-flex tw-cursor-pointer tw-bg-white tw-border-gray-200 tw-z-10 tw-w-1/2" x-state:on="Checked" x-state:off="Not Checked" :class="{ 'tw-bg-wp-blue tw-bg-opacity-10 tw-border-wp-blue tw-border-opacity-30 tw-z-10': scheduleType === 'availability' }">
                                  <div class="tw-ml-3 tw-flex tw-flex-col">
                                    <span id="privacy-setting-0-label" class="tw-block tw-text-sm tw-font-medium tw-text-gray-900" x-state:on="Checked" x-state:off="Not Checked" :class="{ 'tw-text-wp-blue': scheduleType === 'availability', 'tw-text-gray-900': !(scheduleType === 'availability') }">
                                      Availbility
                                    </span>
                                    <span id="privacy-setting-0-description" class="tw-block tw-text-xs tw-text-gray-700">Operational hours</span>
                                  </div>
                                </label>

                                <label @click=" scheduleType='unavailability'" class=" tw-rounded-tr-md tw-border-l-0 tw-rounded-br-md tw-relative tw-border tw-p-4 tw-flex tw-cursor-pointer tw-bg-white tw-border-gray-200 tw-z-10 tw-w-1/2" x-state:on="Checked" x-state:off="Not Checked" :class="{ 'tw-bg-wp-blue tw-bg-opacity-10 tw-border-wp-blue tw-border-opacity-30 tw-z-10 tw-border-l': scheduleType === 'unavailability' }">
                                  <div class="tw-ml-3 tw-flex tw-flex-col">
                                    <span id="privacy-setting-0-label" class="tw-block tw-text-sm tw-font-medium tw-text-gray-900" x-state:on="Checked" x-state:off="Not Checked" :class="{ 'tw-text-wp-blue': scheduleType === 'unavailability', 'tw-text-gray-900': !(scheduleType === 'unavailability') }">
                                      Unavailbility
                                    </span>
                                    <span id="privacy-setting-0-description" class="tw-block tw-text-xs tw-text-gray-700">
                                      Breaks, time-offs and holidays
                                    </span>
                                  </div>
                                </label>
                              </div>
                            </fieldset>

                            <!-- Availability -->
                            <div x-show=" scheduleType=='availability'">
                              <!-- Type -->
                              <div class=" tw-space-y-1 tw-px-0 sm:tw-space-y-0 sm:tw-grid sm:tw-grid-cols-3 sm:tw-gap-4 sm:tw-px-0 sm:tw-py-3">
                                <div>
                                  <label for="project-description" class="tw-block tw-text-sm tw-font-medium tw-text-gray-900 sm:tw-mt-px sm:tw-pt-1">Type</label>
                                </div>
                                <div class="sm:tw-col-span-2">
                                  <select x-model="form.schedule.availability.datetype" name="" id="" class="tw-border-gray-300 sm:tw-text-sm tw-bg-white tw-w-full tw-rounded-lg">
                                    <option value="singleday">Single Day</option>
                                    <option value="multidays">Multi Days</option>
                                    <option value="everyday">Every Day</option>
                                  </select>
                                  <div x-show="getNested(errorMessages, 'schedule','availability', 'datetype')" class="tw-error-text">
                                    <small x-text="getNested(errorMessages, 'schedule','availability', 'datetype')"></small>
                                  </div>
                                </div>
                              </div>

                              <!-- Date -->
                              <div x-show="form.schedule.availability.datetype == 'singleday'" class="tw-space-y-1 sm:tw-grid sm:tw-grid-cols-3 tw-px-0 sm:tw-space-y-0 sm:tw-gap-4 sm:tw-px-0 sm:tw-py-3">
                                <div>
                                  <label for="project-description" class="tw-block tw-text-sm tw-font-medium tw-text-gray-900 sm:tw-mt-px sm:tw-pt-1">Date</label>
                                </div>
                                <div class="sm:tw-col-span-2">
                                  <div class="tw-mt-1 tw-relative tw-rounded-md tw-shadow-sm tw-w-1/2">
                                    <input x-model=form.schedule.availability.startdate type="text" class="focus:tw-ring-wp-blue focus:tw-border-wp-blue tw-block tw-w-full tw-pr-10 sm:tw-text-sm tw-border-gray-300 tw-rounded-md datepicker" data-model="schedule.startdate" placeholder="Start Date">
                                    <div class="tw-absolute tw-inset-y-0 tw-right-0 tw-pr-3 tw-flex tw-items-center tw-pointer-events-none">

                                      <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-5 tw-w-5 tw-text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                      </svg>
                                    </div>
                                  </div>
                                  <div x-show="getNested(errorMessages, 'schedule','availability', 'startdate')" class="tw-error-text">
                                    <small x-text="getNested(errorMessages, 'schedule','availability', 'startdate')"></small>
                                  </div>
                                </div>
                              </div>





                              <div x-show="form.schedule.availability.datetype == 'multidays'" class="tw-space-y-1 sm:tw-grid sm:tw-grid-cols-3 tw-px-0 sm:tw-space-y-0 sm:tw-gap-4 sm:tw-px-0 sm:tw-py-3">
                                <div>
                                  <label for="project-description" class="tw-block tw-text-sm tw-font-medium tw-text-gray-900 sm:tw-mt-px sm:tw-pt-1">Date Range</label>
                                </div>
                                <div class="sm:tw-col-span-2">

                                  <div class="tw-flex tw-space-x-3">
                                    <div class="tw-mt-1 tw-relative tw-rounded-md tw-shadow-sm tw-w-1/2">
                                      <input x-model="form.schedule.availability.startdate" type="text" class="focus:tw-ring-wp-blue focus:tw-border-wp-blue tw-block tw-w-full tw-pr-10 sm:tw-text-sm tw-border-gray-300 tw-rounded-md datepicker" data-model="schedule.start" placeholder="Start Date">
                                      <div class="tw-absolute tw-inset-y-0 tw-right-0 tw-pr-3 tw-flex tw-items-center tw-pointer-events-none">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-5 tw-w-5 tw-text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                      </div>
                                    </div>
                                    <div class="tw-mt-1 tw-relative tw-rounded-md tw-shadow-sm tw-w-1/2">
                                      <input x-model="form.schedule.availability.enddate" type="text" class="focus:tw-ring-wp-blue focus:tw-border-wp-blue tw-block tw-w-full tw-pr-10 sm:tw-text-sm tw-border-gray-300 tw-rounded-md datepicker" data-model="schedule.end" placeholder="End Date">
                                      <div class="tw-absolute tw-inset-y-0 tw-right-0 tw-pr-3 tw-flex tw-items-center tw-pointer-events-none">
                                        <!-- Heroicon name: solid/question-mark-circle -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-5 tw-w-5 tw-text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                      </div>
                                    </div>
                                  </div>


                                  <div class="tw-flex tw-space-x-3">
                                    <div x-show="getNested(errorMessages, 'schedule','availability', 'startdate')" class="tw-error-text tw-w-1/2">
                                      <small x-text="getNested(errorMessages, 'schedule','availability', 'startdate')"></small>
                                    </div>
                                    <div x-show="getNested(errorMessages, 'schedule','availability', 'enddate')" class="tw-error-text tw-w-1/2" style="margin-left: auto;">
                                      <small x-text="getNested(errorMessages, 'schedule','availability', 'enddate')"></small>
                                    </div>
                                  </div>
                                </div>
                              </div>









                              <!-- Time -->
                              <div x-show="form.schedule.availability.datetype !== 'everyday'" class="tw-space-y-1 sm:tw-grid sm:tw-grid-cols-3 tw-px-0 sm:tw-space-y-0 sm:tw-gap-4 sm:tw-px-0 sm:tw-py-3">
                                <div>
                                  <label for="project-description" class="tw-block tw-text-sm tw-font-medium tw-text-gray-900 sm:tw-mt-px sm:tw-pt-1">Time</label>
                                </div>
                                <div class="sm:tw-col-span-2">

                                  <div class="tw-flex tw-space-x-3 tw-place-items-center">
                                    <span>Start</span>

                                    <div class="tw-mt-1 tw-relative tw-rounded-md tw-w-1/2">
                                      <!-- <span>Start</span> -->
                                      <input x-model="form.schedule.availability.starttime" type="time" class="focus:tw-ring-wp-blue focus:tw-border-wp-blue tw-block tw-w-full tw-pr-10 sm:tw-text-sm tw-border-gray-300 tw-rounded-md" placeholder="Start Time">
                                      <div class="tw-absolute tw-inset-y-0 tw-right-0 tw-pr-3 tw-flex tw-items-center tw-pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-5 tw-w-5 tw-text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                      </div>


                                    </div>

                                    <span>End</span>
                                    <div class="tw-mt-1 tw-relative tw-rounded-md tw-w-1/2">
                                      <input x-model="form.schedule.availability.endtime" type="time" class="focus:tw-ring-wp-blue focus:tw-border-wp-blue tw-block tw-w-full tw-pr-10 sm:tw-text-sm tw-border-gray-300 tw-rounded-md" placeholder="End Time">
                                      <div class="tw-absolute tw-inset-y-0 tw-right-0 tw-pr-3 tw-flex tw-items-center tw-pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-5 tw-w-5 tw-text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                      </div>

                                    </div>
                                  </div>


                                  <div class="tw-flex tw-space-x-3">
                                    <div x-show="getNested(errorMessages, 'schedule','availability', 'starttime')" class="tw-error-text tw-w-1/2">
                                      <small x-text="getNested(errorMessages, 'schedule','availability', 'starttime')"></small>
                                    </div>
                                    <div x-show="getNested(errorMessages, 'schedule','availability', 'endtime')" class="tw-error-text tw-w-1/2" style="margin-left: auto;">
                                      <small x-text="getNested(errorMessages, 'schedule','availability', 'endtime')"></small>
                                    </div>
                                  </div>
                                </div>
                              </div>


                              <!-- Week Days -->
                              <template x-if="form.schedule.availability.datetype == 'everyday'">
                                <div class="tw-flex tw-flex-col tw-mt-4">
                                  <div class="tw--my-2 tw-overflow-x-auto sm:tw--mx-6 lg:tw--mx-8">
                                    <div class="tw-py-2 tw-align-middle tw-inline-block tw-min-w-full sm:tw-px-6 lg:tw-px-8">
                                      <div class="tw-shadow tw-overflow-hidden tw-border-b tw-border-gray-200 sm:tw-rounded-lg">
                                        <table class="tw-min-w-full tw-divide-y tw-divide-gray-200">

                                          <tbody class="tw-bg-white tw-divide-y tw-divide-gray-200">

                                            <template x-for="(day, index) in form.schedule.availability.days">

                                              <tr :class="{'tw-bg-gray-50': !day.status, 'tw-bg-white': day.status}">
                                                <td class="tw-px-3 tw-py-2 tw-whitespace-nowrap">
                                                  <div class="tw-flex tw-items-center">
                                                    <div class="tw-flex-shrink-0 tw-px-1 tw-py-2 tw-whitespace-nowrap">
                                                      <input x-model="day.status" :id="day.name" type="checkbox" class="w-4 tw-h-4 tw-text-wp-blue tw-border-gray-300 tw-rounded focus:tw-ring-wp-blue tw-bg-transparent">
                                                    </div>
                                                    <div class="tw-ml-4">
                                                      <label :for="day.name" x-text="day.name" class="tw-text-sm tw-font-medium tw-text-gray-900 tw-capitalize">
                                                        Monday
                                                      </label>
                                                    </div>
                                                  </div>
                                                </td>
                                                <td class="tw-px-6 tw-py-2 tw-whitespace-nowrap">
                                                  <div class="tw-flex tw-space-x-3">
                                                    <div x-show="day.status" class="tw-mt-1 tw-relative tw-rounded-md tw-shadow-sm tw-w-1/2">
                                                      <input x-model="day.start" type="time" class="focus:tw-ring-wp-blue focus:tw-border-wp-blue tw-block tw-w-full tw-pr-10 sm:tw-text-sm tw-border-gray-300 tw-rounded-md" data-model="schedule.start" placeholder="Start Date">
                                                      <div class="tw-absolute tw-inset-y-0 tw-right-0 tw-pr-3 tw-flex tw-items-center tw-pointer-events-none">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-5 tw-w-5 tw-text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                      </div>
                                                    </div>
                                                    <div x-show="day.status" class="tw-mt-1 tw-relative tw-rounded-md tw-shadow-sm tw-w-1/2">
                                                      <input x-model="day.end" type="time" class="focus:tw-ring-wp-blue focus:tw-border-wp-blue tw-block tw-w-full tw-pr-10 sm:tw-text-sm tw-border-gray-300 tw-rounded-md" data-model="schedule.start" placeholder="Start Date">
                                                      <div class="tw-absolute tw-inset-y-0 tw-right-0 tw-pr-3 tw-flex tw-items-center tw-pointer-events-none">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-5 tw-w-5 tw-text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                      </div>
                                                    </div>
                                                  </div>


                                                  <div class="tw-flex tw-space-x-3">
                                                    <div x-show="getNested(errorMessages, 'schedule','availability', 'days', index, 'start')" class="tw-error-text">
                                                      <small x-text="getNested(errorMessages, 'schedule','availability', 'days', index, 'start')"></small>
                                                    </div>
                                                    <div x-show="getNested(errorMessages, 'schedule','availability', 'days', index, 'end')" class="tw-error-text">
                                                      <small x-text="getNested(errorMessages, 'schedule','availability', 'days', index, 'end')"></small>
                                                    </div>
                                                  </div>

                                                </td>

                                              </tr>
                                            </template>

                                            <!-- More people... -->
                                          </tbody>
                                        </table>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </template>
                            </div>

                            <!-- Unavailability -->
                            <div x-show="scheduleType == 'unavailability'">

                              <ul class="tw-divide-gray-200 tw-divide-y">
                                <template x-for="(slot, index) in form.schedule.unavailability.slots" :key="index">
                                  <li class="tw-py-4 tw-flex tw-space-x-3">

                                    <div class="tw-mt-1 tw-w-1/3">
                                      <div class="tw-relative tw-rounded-md tw-shadow-sm">

                                        <div>
                                          <select x-model="slot.datetype" name="" id="" class="tw-border-gray-300 sm:tw-text-sm tw-bg-white tw-w-full tw-rounded-lg">
                                            <option value="singleday">Single Day</option>
                                            <option value="monday">Monday</option>
                                            <option value="tuesday">Tuesday</option>
                                            <option value="wednesday">Wednesday</option>
                                            <option value="thursday">Thursday</option>
                                            <option value="friday">Friday</option>
                                            <option value="saturday">Saturday</option>
                                            <option value="sunday">Sunday</option>
                                            <option value="everyday">Every Day</option>
                                          </select>
                                        </div>
                                      </div>
                                    </div>

                                    <div x-show="slot.datetype == 'singleday'" class="tw-mt-1 tw-w-1/2">
                                      <div class="tw-relative tw-rounded-md tw-shadow-sm">

                                        <input x-model="slot.startdate" type="text" class="focus:tw-ring-wp-blue focus:tw-border-wp-blue tw-block tw-w-full tw-pr-10 sm:tw-text-sm tw-border-gray-300 tw-rounded-md datepicker" placeholder="Start Date">

                                        <div class="tw-absolute tw-inset-y-0 tw-right-0 tw-pr-3 tw-flex tw-items-center tw-pointer-events-none">
                                          <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-5 tw-w-5 tw-text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                          </svg>
                                        </div>
                                      </div>
                                    </div>

                                    <div class="tw-mt-1 tw-w-1/3">
                                      <div class="tw-relative tw-rounded-md tw-shadow-sm">
                                        <input x-model="slot.starttime" type="time" class="focus:tw-ring-wp-blue focus:tw-border-wp-blue tw-block tw-w-full tw-pr-10 sm:tw-text-sm tw-border-gray-300 tw-rounded-md" placeholder="HH:MM" id="dp1627458104236">
                                        <div class="tw-absolute tw-inset-y-0 tw-right-0 tw-pr-3 tw-flex tw-items-center tw-pointer-events-none">
                                          <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-5 tw-w-5 tw-text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                          </svg>
                                        </div>
                                      </div>

                                      <div x-show="getNested(errorMessages, 'schedule','unavailability', 'slots', index, 'starttime')" class="tw-error-text tw-w-1/2">
                                        <small x-text="getNested(errorMessages, 'schedule','unavailability', 'slots', index, 'starttime')"></small>
                                      </div>
                                    </div>

                                    <div class="tw-mt-1 tw-w-1/3">
                                      <div class="tw-relative tw-rounded-md tw-shadow-sm">
                                        <input x-model="slot.endtime" type="time" class="focus:tw-ring-wp-blue focus:tw-border-wp-blue tw-block tw-w-full tw-pr-10 sm:tw-text-sm tw-border-gray-300 tw-rounded-md" placeholder="HH:MM" id="dp1627458104236">
                                        <div class="tw-absolute tw-inset-y-0 tw-right-0 tw-pr-3 tw-flex tw-items-center tw-pointer-events-none">

                                          <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-5 tw-w-5 tw-text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                          </svg>
                                        </div>
                                      </div>
                                      <div x-show="getNested(errorMessages, 'schedule','unavailability', 'slots', index, 'endtime')" class="tw-error-text tw-w-1/2">
                                        <small x-text="getNested(errorMessages, 'schedule','unavailability', 'slots', index, 'endtime')"></small>
                                      </div>
                                    </div>
                                  </li>
                                </template>

                              </ul>

                              <p class="tw-flex tw-justify-center tw-mt-10">
                                <a @click.prevent="addNewUnavailability()" class="tw-flex tw-items-center" href="#0">
                                  <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-6 tw-w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                  </svg>
                                  <span class="tw-text-sm">Add New</span>
                                </a>
                              </p>

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