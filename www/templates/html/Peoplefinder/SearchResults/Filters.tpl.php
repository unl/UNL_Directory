<aside class="dcf-mb-6">
  <nav>
    <button id="skip_sidebar" class="dcf-btn dcf-btn-tertiary dcf-show-on-focus">Skip Sidebar</button>
  </nav>
  <form id="filter_form" class="dcf-form">
    <div class="dcf-d-flex dcf-flex-nowrap dcf-flex-row dcf-jc-between dcf-ai-center">
      <p class="dcf-mb-0 dcf-txt-lg">Filter Results</p>
      <button id="filter_reset" class="dcf-btn dcf-btn-tertiary" type="button">
        Clear
      </button>
    </div>

    <hr class="dcf-mt-3 dcf-mb-5">

    <fieldset id="affiliation_filter" class="dcf-collapsible-fieldset dcf-d-none" data-start-expanded="false">
      <legend>Affiliation</legend>
      <div class="directory-h-max-filter directory-filter-fieldset-contents dcf-overflow-y-auto">
      <div class="dcf-progress-spinner"></div>
        <ol class="dcf-list-bare dcf-mb-0"></ol>
      </div>
    </fieldset>

    <fieldset id="department_filter" class="dcf-collapsible-fieldset dcf-d-none" data-start-expanded="false">
      <legend>Department</legend>
      <div class="directory-h-max-filter directory-filter-fieldset-contents dcf-overflow-y-auto">
        <div class="dcf-progress-spinner"></div>
        <ol class="dcf-list-bare dcf-mb-0"></ol>
      </div>
    </fieldset>
  </form>
</aside>

<template id="filter_option_template">
  <li class="dcf-input-checkbox">
    <input id="" value="" type="checkbox"/>
    <label class="dcf-label" for=""></label>
  </li>
</template>

<template id="summary_template">
  <div id="search_summary" class="dcf-mb-4 dcf-d-none">
    <p class="dcf-txt-xs unl-font-sans unl-dark-gray dcf-bold dcf-mb-0">
      Displaying Search: <span id="search_query"></span> - <span id="total_results"></span>
    </p>

    <p id="affiliation_filter_summary_container" class="dcf-txt-xs unl-font-sans unl-dark-gray dcf-ml-2 dcf-mb-0 dcf-d-none">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" class="dcf-h-4 dcf-w-4 dcf-fill-current">
        <path d="M29.9,15.7c0,0,0-0.1-0.1-0.2c-0.1-0.2-0.2-0.4-0.4-0.6
          l-7.2-7.2c-0.7-0.7-1.7-0.7-2.4,0c-0.7,0.7-0.7,1.7,0,2.4l4.4,4.3H3.4V1.7C3.4,0.8,2.6,0,1.7,0
          S0,0.8,0,1.7v14.4c0,0.9,0.8,1.7,1.7,1.7h22.5l-4.4,4.3c-0.3,0.3-0.5,0.7-0.5,1.2
          s0.2,0.9,0.5,1.2c0.3,0.3,0.7,0.5,1.2,0.5s0.9-0.2,1.2-0.5l7.2-7.2c0.1-0.1,0.3-0.3,0.4-0.5
          c0-0.1,0.1-0.2,0.1-0.3c0-0.1,0-0.2,0-0.4C30,16,30,15.8,29.9,15.7z"></path>
        <g>
          <path fill="none" d="M0,0h30v30H0V0z"></path>
        </g>
      </svg>
      <span id="affiliation_filter_summary"></span>
    </p>

    <p id="department_filter_summary_container" class="dcf-txt-xs unl-font-sans unl-dark-gray dcf-ml-2 dcf-mb-0 dcf-d-none">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" class="dcf-h-4 dcf-w-4 dcf-fill-current">
        <path d="M29.9,15.7c0,0,0-0.1-0.1-0.2c-0.1-0.2-0.2-0.4-0.4-0.6
          l-7.2-7.2c-0.7-0.7-1.7-0.7-2.4,0c-0.7,0.7-0.7,1.7,0,2.4l4.4,4.3H3.4V1.7C3.4,0.8,2.6,0,1.7,0
          S0,0.8,0,1.7v14.4c0,0.9,0.8,1.7,1.7,1.7h22.5l-4.4,4.3c-0.3,0.3-0.5,0.7-0.5,1.2
          s0.2,0.9,0.5,1.2c0.3,0.3,0.7,0.5,1.2,0.5s0.9-0.2,1.2-0.5l7.2-7.2c0.1-0.1,0.3-0.3,0.4-0.5
          c0-0.1,0.1-0.2,0.1-0.3c0-0.1,0-0.2,0-0.4C30,16,30,15.8,29.9,15.7z"></path>
        <g>
          <path fill="none" d="M0,0h30v30H0V0z"></path>
        </g>
      </svg>
      <span id="department_filter_summary"></span>
    </p>
  </div>
</template>

