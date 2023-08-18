define([
	'jquery',
	'wdn',
	'require',
	'idm',
	'notice',
	'./vendor/jsrender.js',
	'./vendor/jquery.qtip.js'
], function($, WDN, require, idm) {
	"use strict";

	var serviceURL = 'https://directory.unl.edu/';
	var annotateServiceURL = 'https://annotate.unl.edu/';
	var originalSearch = '';
	var $progress = $('<progress>', {'class': 'loading'}).text('Loading...');
	var attempts = 1;
	var requestTimeout;
	var pendingRequest;
	var delayLoadIdicator = 250;
	var requestThrottleTime = 400;
	var transitionTime = 400;
	var noResultsTrigger = 'Sorry, no results could be found.';
	var searchNoticeSelector = '#noticeTemplate';
	var genericErrorSelector = '#genericErrorTemplate';
	var lengthErrorSelector = '#queryLengthTemplate';
	var mainSelector = '#dcf-main';
	var annotateSelector = '#annotateTemplate';
	var correctionButtonSelector = '#correctionButtonTemplate';
	var mainStates = ['searching', 'single', 'single-dept'];
	var $searcher;
	var $results;
	var departments = [];
	var affiliations = [];
	var modalReady = false;
	var initialMainState;
	var currentMainState;
	var documentTitleSuffix = '';
	var resultsContainerSelector = '.results-container';
	var emptyFilterClass = 'empty-filters';

	const filter_form = document.getElementById('filter_form');
	const filter_reset = document.getElementById('filter_reset');
	const affiliation_filter = document.getElementById('affiliation_filter');
	const department_filter = document.getElementById('department_filter');
	const skip_sidebar = document.getElementById('skip_sidebar');
	let search_summary;
	let summary_search_query;
	let summary_total_results;
	let affiliation_filter_summary_container;
	let affiliation_filter_summary;
	let department_filter_summary_container;
	let department_filter_summary;

	var filters = {
		initialize : function() {
			var $resultLists = $('.results', $results);
			var $total = $('.ppl_Sresult', $results);

			// If we do not have any results turn the filters off
			if (!$resultLists.length || !$total.length) {
				document.querySelector(resultsContainerSelector).classList.add(emptyFilterClass);
				return;
			}

			// Puts the loading spinner in the filters
			filter_form.classList.add('loading');

			// Removed the class to make the filters hidden
			document.querySelector(resultsContainerSelector).classList.remove(emptyFilterClass);

			// Loop through all the results and find the organizations and affiliations
			$('.results', $results).each(function() {
				if (!$(this).hasClass('departments')) {
					$('.organization-unit', this).each(function() {
						//find the departments from the people records
						var refDepartment = $(this).text().trim();
						var cleanValue = filters.scrubDept(refDepartment.toLowerCase());

						if ($.inArray(refDepartment, departments) < 0) {
							departments.push(refDepartment);
						}

						$(this).parents('.ppl_Sresult').addClass(cleanValue);
					});
					affiliations.push($(this).children('h2').eq(0).text());
				}
			});

			// Sort them
			departments.sort();
			affiliations.sort();

			// Populate the filters using the found values
			filters.buildFilterOptions(affiliation_filter, affiliations);
			filters.buildFilterOptions(department_filter, departments);

			// Removed loading spinner from filters
			filter_form.classList.remove('loading');
			
			// Clears our values found for the next query
			departments = [];
			affiliations = [];

			const summary_template = document.getElementById('summary_template');
			let summary_element = summary_template.content.cloneNode('true');
	
			$results.prepend(summary_element);
	
			search_summary = document.getElementById('search_summary');
			summary_search_query = document.getElementById('search_query');
			summary_total_results = document.getElementById('total_results');
			affiliation_filter_summary_container = document.getElementById('affiliation_filter_summary_container');
			affiliation_filter_summary = document.getElementById('affiliation_filter_summary');
			department_filter_summary_container = document.getElementById('department_filter_summary_container');
			department_filter_summary = document.getElementById('department_filter_summary');
	
			summary_search_query.innerText = decodeURI(getCleanHash().slice(2).replace('/', ' '));
			search_summary.classList.remove('dcf-d-none');
		},

		clear: function() {

			// Adds the loading spinner
			filter_form.classList.add('loading');

			// Gets the containers of the filter options
			const affiliation_filter_container = affiliation_filter.querySelector('ol');
			const department_filter_container = department_filter.querySelector('ol');
			if (affiliation_filter_container == null) { throw new Error('Missing affiliation Filter OL'); }
			if (department_filter_container == null) { throw new Error('Missing department Filter OL'); }

			// Clears our the filter options
			affiliation_filter_container.innerHTML = "";
			department_filter_container.innerHTML = "";

			// Removed the search summary and the filter summaries
			if (search_summary !== undefined) {
				search_summary.classList.add('dcf-d-none');
				affiliation_filter_summary_container.classList.remove('dcf-d-block');
				affiliation_filter_summary_container.classList.add('dcf-d-none');
				department_filter_summary_container.classList.remove('dcf-d-block');
				department_filter_summary_container.classList.add('dcf-d-none');
			}
		},

		generateAllSummaryOption: function() {
			var tmpl = $.templates('#summaryAllTemplate');
			return $(tmpl.render());
		},

		buildFilterOptions: function(filter_to_add_to, list_of_options) {

			// Gets the variables of the filters
			const filter_id = filter_to_add_to.id ?? 'filter';
			const filter_container = filter_to_add_to.querySelector('ol');
			const option_template = document.getElementById('filter_option_template');
			if (filter_container == null) { throw new Error('Missing Filter OL'); }
			if (option_template == null) { throw new Error('Missing Option Template'); }

			// Loops through each option and creates a new checkbox for the filters
			list_of_options.forEach((option, index) => {
				let option_element = option_template.content.cloneNode('true');
				let option_input = option_element.querySelector('input');
				let option_label = option_element.querySelector('label');
				if (option_input == null) { throw new Error('Missing Option Input In Template'); }
				if (option_label == null) { throw new Error('Missing Option Label In Template'); }

				const formattedText = filters.scrubDept(option.toLowerCase());

				// Populates the values
				option_input.id = filter_id + '_' + index + '_' + formattedText;
				option_input.value = formattedText;
				option_input.dataset.value = option;
				option_label.setAttribute('for', option_input.id);
				option_label.innerText = option;

				// Appends the option
				filter_container.append(option_element);
			});
		},

		action : function(checkbox) {
			// Gets the values of the checkboxes, and the results
			const checked_options = filter_form.querySelectorAll('input:checked');
			const result_elements = document.querySelectorAll('div.results ul li');
			const result_containers = document.querySelectorAll('.result_head, div.results.departments, div.results.affiliation');

			// Hide the filters
			affiliation_filter_summary_container.classList.add('dcf-d-none');
			department_filter_summary_container.classList.add('dcf-d-none');

			// No checked filter options
			if (checked_options.length === 0) {

				// Show everything
				result_elements.forEach((result_elem) => {
					result_elem.classList.remove('dcf-d-none');
				});
				result_containers.forEach((result_elem) => {
					result_elem.classList.remove('dcf-d-none');
				});

			// We do have a filter option checked
			} else {
				// Hide everything
				result_elements.forEach((result_elem) => {
					result_elem.classList.add('dcf-d-none');
				});

				result_containers.forEach((result_elem) => {
					result_elem.classList.add('dcf-d-none');
				});

				let affiliation_filters_used = [];
				let department_filters_used = [];

				checked_options.forEach((filter_option) => {
					const filter_original_value = filter_option.dataset.value;
					const filter_value = filter_option.value;
					const filter_id = filter_option.id;

					// Find all elements that 
					result_elements.forEach((result_elem) => {
						if (result_elem.classList.contains(filter_value)) {
							result_elem.classList.remove('dcf-d-none');
							result_elem.closest('.results.affiliation').classList.remove('dcf-d-none');
						}
					});

					// Check what kind of filter it is and add its original text to the list
					if (filter_id.startsWith('affiliation')) {
						affiliation_filters_used.push(filter_original_value);
					}
					if (filter_id.startsWith('department')) {
						department_filters_used.push(filter_original_value);
					}
				});

				// If we have any affiliation filters then show the text in the summary
				if (affiliation_filters_used.length > 0) {
					// Format the text all nice like
					let affiliation_text = affiliation_filters_used[0];
					if (affiliation_filters_used.length > 2) {
						const last_option = affiliation_filters_used.pop();
						affiliation_text = affiliation_filters_used.join(', ') + ', or ' + last_option;
					} else if (affiliation_filters_used.length == 2) {
						affiliation_text = affiliation_filters_used.join(' or ');
					}

					// Add the text and show the container
					affiliation_filter_summary.innerText = 'Affiliations: ' + affiliation_text;
					affiliation_filter_summary_container.classList.remove('dcf-d-none');
				}

				// If we have any affiliation filters then show the text in the summary
				if (department_filters_used.length > 0) {
					// Format the text all nice like
					let department_text = department_filters_used[0];
					if (department_filters_used.length > 2) {
						const last_option = department_filters_used.pop();
						department_text = department_filters_used.join(', ') + ', or ' + last_option;
					} else if (department_filters_used.length == 2) {
						department_text = department_filters_used.join(' or ');
					}

					// Add the text and show the container
					department_filter_summary.innerText = 'Departments: ' + department_text;
					department_filter_summary_container.classList.remove('dcf-d-none');
				}
			}

			// Update the total results
			updateNumResults();
		},

		scrubDept : function(string) {
			return string.split(' ').join('').replace(/&|,/gi, '');
		}
	};

	var updateNumResults = function() {
		//Always append the number of results
		var numResultText = $('div.results ul li:visible').length;
		if (numResultText === 1) {
			numResultText += ' total result';
		} else {
			numResultText += ' total results';
		}

		summary_total_results.innerText = numResultText;
	};

	/**
	 * [0:'searching', 1:'single', 2:'single-dept']
	 *
	 * @param state
	 */
	var setMainState = function(state) {
		currentMainState = state;
		if (typeof state !== 'undefined') {
			state = mainStates[state];
		}
		var $main = $(mainSelector);
		$main.removeClass(mainStates.join(' '));
		if (state) {
			$main.addClass(state);
		}
	};

	var displayOnlyRecord = function($vcard) {
		$('.record-single').empty().append($vcard);
	};

	var addAnnotateTool = function(uid, $vcard) {
		var tmpl = $.templates(annotateSelector);
		var params = {
			view: 'annotation',
			sitekey: 'directory',
			fieldname: uid
		};
		var annotateUrl = annotateServiceURL + '?' + $.param(params);

		$(tmpl.render({
			annotateUrl: annotateUrl,
			preferredName: $vcard.data('preferred-name'),
		})).appendTo($('.vcard-tools.primary', $vcard));
	};

	var addCorrectionTool = function(name, $vcard) {
		var tmpl = $.templates(correctionButtonSelector);

		var $html = $(tmpl.render({
			preferredName: name
		}));

		if ($vcard.hasClass('office') && $vcard.find('.department-correction').length) {
			$vcard.find('.department-correction').after($html);
		} else {
			//person record
			$vcard.find('.vcard-tools').after($html);
		}
	};

	var loadOnlyRecord = function(uid, preferredName, $vcard) {
		if (window.history.pushState) {
			window.history.pushState({uid: uid}, preferredName, serviceURL + 'people/' + uid);
		}

		setMainState(1);
		displayOnlyRecord($vcard);
	};

	var displaySearch = function() {
		setMainState();
	};

	var fetchRecord = function(recordType, recordId) {
		var url;

		if (recordType === 'org') {
			url = recordId + '/summary?format=partial';
		} else {
			url = serviceURL + 'hcards-full/' + recordId;
		}

		return $.ajax({url: url});
	};

	var loadFullRecord = function(recordType, liRecord, closeSelected) {
		var slidingSelector = '.vcard';
		var overviewSelector = '.overflow';
		var infoData;

		// remove any previous errors
		$('.error', liRecord).remove();

		if (recordType === 'org') {
			slidingSelector = '.departmentInfo';
		}

		var $loadedChild = liRecord.children(slidingSelector);
		var $overview = liRecord.children(overviewSelector);

		if (liRecord.hasClass('selected')) {
			console.log('li selected');

			// only do close if specified
			if (closeSelected) {
				$loadedChild.hide(0, function() {
					$overview.show('fast', function() { $(this).addClass('dcf-d-flex'); });
				});

				liRecord.removeClass('selected');

				//Send focus to the result for accessibility
				$('a:first', $overview).addClass('programmatically-focused').focus();
			}
			return;
		}

		// reset the current and selected states
		$('li.current', $results).removeClass('current');
		liRecord.addClass('selected current');

		if ($loadedChild.length) {
			// we already loaded the record
			console.log('alreaded loaded');
			$overview.hide(0, function() {
				$(this).removeClass('dcf-d-flex');
				$loadedChild.show('fast');
			});
			//Send focus to the result for accessibility
			$('a:first', $loadedChild).addClass('programmatically-focused').focus();
			return;
		}

		// delay showing a loading indicator
		var loadIndicatorTimeout;
		if (!liRecord.children('.loading').length) {
			 loadIndicatorTimeout = setTimeout(function() {
				liRecord.append($progress);
			}, delayLoadIdicator);
		}

		if (recordType === 'org') {
			infoData = liRecord.data('href');
		} else {
			infoData = liRecord.data('uid');
		}

		fetchRecord(recordType, infoData).then(function(data, textStatus) {
			if (textStatus !== 'success') {
				return;
			}

			var $card = $(data).hide();
			liRecord.append($card);

			//Add a close button
			var closeButton = $('<button>', {
				'class': 'close-full-record dcf-btn dcf-btn-tertiary',
				'aria-label': 'close this record'
			});
			closeButton.click(function() {
				//close
				console.log('close button clicked');
				loadFullRecord(recordType, liRecord, true);
				return false;
			});
			closeButton.text('X');

			$card.parent().find('.vcard:first').prepend(closeButton);

			// load annotation tool for people records
			if (recordType !== 'org') {
				addAnnotateTool(infoData, $card);
				addCorrectionTool($card.data('preferred-name'), $card);
			} else if ($('.department-correction', $card).length) {
				addCorrectionTool($card.data('preferred-name'), $card.find('.vcard'));
			}

			console.log('fetch record sliding');
			$overview.hide(0, function() {
				$(this).removeClass('dcf-d-flex');
				$card.show('fast');
			});

			//Send focus to the result for accessibility
			$('a:first', $card).addClass('programmatically-focused').focus();
			clearTimeout(loadIndicatorTimeout);
			liRecord.children('.loading').remove();
		}, function() {
			var tmpl = $.templates(genericErrorSelector);
			liRecord.append(tmpl.render());
			clearTimeout(loadIndicatorTimeout);
			liRecord.children('.loading').remove();
		});
	};

	var restoreRecordToResult = function() {
		var $vcard = $('.record-single .vcard');

		if (!$vcard.length) {
			return;
		}

		var $candidateResults = $('.ppl_Sresult.selected').filter(function() {
			if ($('.vcard', this).length) {
				return false;
			} else if ($(this).data('uid') == $vcard.data('uid')) {
				return true;
			}

			return false;
		});

		if ($candidateResults.length) {
			$candidateResults.append($vcard);
		}
	};

	var bindResultsListeners = function($container) {
		// listen for people result clicks
		$container.on('click', '.ppl_Sresult', function(e) {
			var $target = $(e.target);
			var $anchor = $target.closest('a');
			var $fn = $anchor.closest('.fn');

			if ($anchor.length && !$fn.length) {
				// allow vCard and non-name link clicks to bubble
				return;
			}

			if ($target.closest('.correction').length) {
				//Launch the correction modal
				launchCorrectionModal($target);
				return;
			}

			loadFullRecord('person', $(this));
			return false;
		});

		// listen for enter key on focused person
		$container.on('keydown', '.ppl_Sresult', function(e) {
			if (this !== e.target || e.which !== 13) {
				// allow keyboard to bubble
				return;
			}

			loadFullRecord('person', $(this));
		});
	};

	var bindDeptResultsListeners = function($container) {
		// listen for department result clicks
		$container.on('click', '.dep_result', function(e) {
			var $target = $(e.target);
			var $anchor = $target.closest('a');
			var $fn = $anchor.closest('.fn');

			if ($anchor.length && !$fn.length) {
				// allow vCard and non-name link clicks to bubble
				return;
			}

			if ($target.closest('.correction').length) {
				//Launch the correction modal
				launchCorrectionModal($target);
				return;
			}

			loadFullRecord('org', $(this));
			return false;
		});

		// listen for enter key on focused department
		$container.on('keydown', '.dep_result', function(e) {
			if (this !== e.target || e.which !== 13) {
				// allow keyboard to bubble
				return;
			}

			loadFullRecord('org', $(this));
		});
	};

	var bindRecordListeners = function ($container) {
		$container.on('click', '.dir-btn-print-vcard', function(e) {

			if (currentMainState === 1) {
				// allow the event to bubble to the printer
				window.print();
				return;
			}

			var $vcard = $(this).closest('.vcard');

			if (!$vcard.length) {
				// don't allow this to bubble to printer
				return false;
			}

			var uid = $vcard.data('uid');
			var preferredName = $vcard.data('preferred-name');

			loadOnlyRecord(uid, preferredName, $vcard);
			window.print();
			e.preventDefault();
		});

		$container.on('click', '.dir-btn-qr-code-vcard', function() {

			var self = $(this);

			var onReady = function() {
				modalReady = true;
				$(self).colorbox({open:true, photo:true});
			};

			if (modalReady) {
				onReady();
			} else {
				WDN.initializePlugin('modal', [onReady]);
			}

			return false;
		});

		$container.on('click', '.directory_annotate', function(e) {
			var $this = $(this);

			e.preventDefault();

			$this.qtip({
				overwrite: false,
				content: {
					text: $('<iframe>', {
						src: this.href,
						scrolling: 'no',
						"class": 'directory_annotate_note',
						title: 'Your notes'
					})
				},
				position: {
					my: 'top right',
					at: 'bottom right'
				},
				show: {
					event: e.type,
					ready: true
				},
				hide: 'unfocus'
			}, e);

			return;
		});
	};

	var isQueryHash = function(hash) {
		return (hash && hash.match(/^q\//));
	};

	var getCleanHash = function() {
		return window.location.hash.replace('#', '').trim();
	};

	var startFromSearch = function() {
		$searcher = $('#peoplefinder');
		$results = $('#results');

		//on submit of the search form, redirect to hashchange
		$searcher.submit(function(eventObject) {
			$("#search-notice").slideUp(function() {
				$(this).empty();
			});

			var query = $('#q').val().trim();

			if (search_summary !== undefined) {
				summary_search_query.innerText = query;
			}

			if (query.length) {
				var newHash = '#q/' + encodeURIComponent(query);
				//triggering a hash change will run through the searching function
				window.location.hash = newHash;
				originalSearch = query;
			}

			// give focus to the results
			$results.focus();

			// don't submit to the browser
			eventObject.preventDefault();
		});

		bindRecordListeners($results.add('.record-container'));
		bindResultsListeners($results);
		bindDeptResultsListeners($results);

		affiliation_filter.addEventListener('ready', () => {
			affiliation_filter.classList.remove('dcf-d-none');
			affiliation_filter.addEventListener('click', (e) => {
				if ( e.target.tagName.toUpperCase() === 'INPUT') {
					filters.action(e.target);
				}
			});
		});
	
		department_filter.addEventListener('ready', () => {
			department_filter.classList.remove('dcf-d-none');
			department_filter.addEventListener('click', (e) => {
				if ( e.target.tagName.toUpperCase() === 'INPUT') {
					filters.action(e.target);
				}
			});
		});
	
		// Set inputs to empty and submit if reset button is clicked
		filter_reset.addEventListener('click', (e) => {
			affiliation_filter.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
				checkbox.checked = false;
			});
			department_filter.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
				checkbox.checked = false;
			});
		});
	
		// This is needed because changing the page hash will remove results
		skip_sidebar.addEventListener('click', (e) => {
			document.getElementById('results').focus();
		});
	
		WDN.initializePlugin('collapsible-fieldsets');
	};

	var showOrLoadInitialState = function(forcedState) {
		if (!$('.help-container').length) {
			$.ajax({
				url: serviceURL,
				data: {format:'partial'},
				success: function(data) {
					setMainState(forcedState);
					$(mainSelector).empty().html(data);
					startFromSearch();

					if (getCleanHash()) {
						$(window).trigger('hashchange');
					}
				},
				error: function() {
					$(mainSelector).prepend('<p>Something went wrong. Please try again later.</p>');
				}
			});
		} else {
			setMainState(forcedState);
		}
	};

	var $modal;
	var $modalClose;
	var modalContentSelector = '.modal-content';
	var $modalContentRestoreContext = null;
	var $modalRestoreFocus = null;

	var showModalForm = function($context, formSelector, fromFocus) {
		var $modalContent = $(modalContentSelector, $modal);
		var $form = $(formSelector, $context);
		var $oldForm;

		if (!$modalClose) {
			$modalClose = $('<button>', {"class": 'cancel dcf-absolute dcf-pin-top dcf-pin-right dcf-mt-1 dcf-mr-1 dcf-btn dcf-btn-tertiary'})
				.click(function() {
					closeModalAndRestoreContent();
				}).html('<span aria-hidden="true">X</span>')
				.append($('<span>', {"class": 'dcf-sr-only'}).text('Close'));
		} else {
			$modalClose.detach();
		}

		$oldForm = $modalContent.children();
		$modalClose.appendTo($modalContent);

		if ($modalContentRestoreContext && $oldForm.length) {
			$modalContentRestoreContext.append($oldForm);
		}

		$modalContentRestoreContext = $context;
		$modalRestoreFocus = $(fromFocus);

		$form.appendTo($modalContent);

		$('html').css('overflow', 'hidden');
		$modal.attr('aria-expanded', 'true');
		$modal.addClass('show');
		setTimeout(function() {
			$modalContent.focus();
		}, 400);
	};

	var closeModalAndRestoreContent = function() {
		var $modalContent = $(modalContentSelector, $modal);
		var $oldForm;

		if (!$modal.hasClass('show')) {
			return;
		}

		if ($modalClose) {
			$modalClose.detach();
		}
		$oldForm = $modalContent.children();

		if ($modalContentRestoreContext) {
			$modalContentRestoreContext.append($oldForm);
			$modalContentRestoreContext = null;
		}

		$('html').css('overflow', '');
		$modal.attr('aria-expanded', 'false');
		$modal.removeClass('show');

		if ($modalRestoreFocus) {
			$modalRestoreFocus.focus();
			$modalRestoreFocus = null;
		}
	};

	var attachListingsEditing = function($editableListings) {
		WDN.initializePlugin('jqueryui', [function() {
			require(['./vendor/jquery.ui.touch-punch.js', './vendor/jquery.mjs.nestedSortable.js'], function() {
				var $sortform = $editableListings.closest('#listings').find('form.sortform');

				$editableListings.nestedSortable({
					revert: false,
					scroll: true,
					delay: 100,
					opacity: 0.45,
					tolerance: 'pointer',
					helper: 'clone',
					update: function(event, ui){
						var sortJson = JSON.stringify($editableListings.nestedSortable('toHierarchy'));
						$('[name="sort_json"]', $sortform).val(sortJson);
						$sortform.submit();
					},
					items: 'li.listing',
					handle: '.listingDetails',
					forcePlaceholderSize: true,
					placeholder: 'ui-placeholder',
					toleranceElement: '.listingDetails'
				});
			});
		}]);

		$editableListings.on('click', '.edit-button', function(e) {
			var self = this;
			var loadedSelector = '.edit';
			var contentSelector = '.forms';
			var $contextForModal = $(this).closest('.tools');
			var $loadTo = $('.form', $contextForModal);

			$(contentSelector, $contextForModal).removeClass('show-add');

			if (!$(loadedSelector, $loadTo).length) {
				$loadTo.load(this.href + '?' + $.param({format: 'partial'}), function() {
					showModalForm($contextForModal, contentSelector, self);
				});
			} else {
				showModalForm($contextForModal, contentSelector, self);
			}

			return false;
		});
	};

	// show hidden edit buttons (hidden with dcf-d-none)
	$(".edit-button").removeClass("dcf-d-none");

	var ajaxSubmitToDepartmentList = function(form, context, listClass, tmpl, data) {
		$.post(form.action + '?' + $.param({redirect: '0'}), $(form).serialize(), function() {
			var $newItem = $(tmpl.render(data)).hide();
			var $list = $('.' + listClass, context);

			if (!$list.length) {
				$list = $('<ul>', {"class": listClass}).insertBefore(form);
			}

			$list.append($newItem);
			$newItem.fadeIn(function() {
				$(document.body).trigger('sticky_kit:recalc');
			});
			form.reset();
		}).fail(function() {
			alert('Your request failed. Please check your input and try again.')
		}).always(function() {
			form.reset();
		});
	};

	var ajaxSubmitRemoveDepartmentList = function(form) {
		var $form = $(form);

		$.post(form.action + '?' + $.param({redirect: '0'}), $form.serialize(), function() {
			$form.closest('li').slideUp(function() {
				var $sortable = $(this).closest('.ui-sortable');

				$(this).remove();
				$(document.body).trigger('sticky_kit:recalc');

				if ($sortable.length) {
					$sortable.nestedSortable('refresh');
				}
			});
		});
	};

	var ajaxSubmitRefreshDepartment = function(form) {
		var $form = $(form);
		var options = {
			redirect: '0',
			render: 'summary',
			format: 'partial'
		};

		$.post(form.action + '?' + $.param(options), $form.serialize(), function(data) {
			$form.closest('.department-summary').find('.departmentInfo').replaceWith(data);
			$(document.body).trigger('sticky_kit:recalc');
			$(mainSelector).focus();
		});
	};

	var ajaxSubmitRefreshListing = function(form, redirect) {
		var $form = $(form);
		var options = {
			redirect: redirect || '0',
			render: 'listing',
			format: 'partial'
		};

		$.post(form.action + '?' + $.param(options), $form.serialize(), function(data) {
			var $listing = $form.closest('.listing');
			var $listings = $('#listings');
			var $list = $('.editing > ol.listings', $listings);

			if (!$listing.length) {
				$listing = $(data);

				if (!$list.length) {
					$list = $('<ol>', {"class": "listings"}).insertBefore($form.closest('.edit-tools'));
					$listing.appendTo($list);
					attachListingsEditing($list);
				} else {
					$listing.hide().appendTo($list).fadeIn();
					$listing.closest('.ui-sortable').nestedSortable('refresh');
				}
			} else {
				$listing.replaceWith(data);
				$listing.closest('.ui-sortable').nestedSortable('refresh');
			}
		});
	};

	var createStickyKit = function($sidebar) {
		/* Disable for now, not sure if will want in 5.0 template version of directory

		// TODO: Remove this function and all calls once determined not needed
		require(['./vendor/jquery.sticky-kit.js'], function() {
			var checkSticky = function() {
				$(document.body).trigger('sticky_kit:recalc');

				if (window.matchMedia('only screen and (min-width: 768px)').matches) {
					$sidebar.stick_in_parent({spacer:false});
				} else {
					$sidebar.trigger('sticky_kit:detach');
				}
			};
			$(window).on('resize', checkSticky);
			checkSticky();
		});
		*/
	};

	var launchCorrectionModal = function($target) {
		var $vcard = $target.closest('.vcard');
		var $context = $('.corrections-template');
		var $form = $('form', $context);
		var name =  idm.getDisplayName() || '';
		var email =  idm.getEmailAddress() || '';

		// Verify form and bail if missing
		if (!$form[0]) {
		  return;
		}

		//Initialize values
		$form[0].reset();
		if (name) {
			$('input[name="name"]', $form).val(name);
		}
		if (email) {
			$('input[name="email"]', $form).val(email);
		}
		$('input[name="source"]', $form).val($('.permalink', $vcard).attr('href'));
		if ($vcard.hasClass('office')) {
			$('input[name="kind"]', $form).val('office');
			$('input[name="id"]', $form).val($vcard.data('listing-id'));
		} else {
			$('input[name="kind"]', $form).val('person');
			$('input[name="id"]', $form).val($vcard.data('uid'));
		}

		//Initialize states
		$form.removeClass('dcf-d-none');
		$context.find('.success').addClass('dcf-d-none');

		//Show that modal!
		showModalForm($context, '.correction-form', $target);
	};

	var plugin = {
		queuePFRequest : function(q, resultsdiv, chooser, cn, sn) {
			var data = {format:'partial'};

			if (chooser) {
				data.chooser = 'true';
			}

			var rawQuery = q;
			var splitName = false;

			if (!cn && !sn) {
				data.q = q;
			} else {
				splitName = true;
				rawQuery = cn + ' ' + sn;
				data.cn = cn;
				data.sn = sn;
			}

			clearTimeout(requestTimeout);
			if (pendingRequest) {
				pendingRequest.abort();
			}

			var tmpl = $.templates(lengthErrorSelector);
			var $results = $('#' + resultsdiv);

			if (rawQuery.length > 3) {
				filters.clear();
				$results.empty().append($progress);
				requestTimeout = setTimeout(function() {
					pendingRequest = $.ajax({
						url: serviceURL,
						data: data,
						success: function(data, textStatus) {
							if (textStatus !== 'success') {
								return;
							}

							var splitQuery;
							var nextAttempt = function(firstName, lastName) {
								window.location.hash = 'q/' + firstName + '/' +lastName;
								summary_search_query.innerText = firstName + ' ' + lastName;

								var tmpl = $.templates({
										markup: searchNoticeSelector,
										allowCode: true
									});
								var search = {
									originalSearch: originalSearch,
									firstName: firstName,
									lastName: lastName
								};
								$("#search-notice").html(tmpl.render(search));
								attempts++;
							};

							if (data.indexOf(noResultsTrigger) >= 0 && originalSearch && attempts < 3) {
								if (!splitName && originalSearch.indexOf(' ') > 0) {
									//user did a simple search with a space, so try an advanced search
									splitQuery = originalSearch.split(' ',2);
									nextAttempt(splitQuery[0], splitQuery[1].substring(0,1));
									return; //We started a new attempt, so stop here
								} else if (splitName) {
									//user did an adavanced search, let's try first letter first name, whole last name
									if (attempts === 2) {
										//on our second attempt
										splitQuery = originalSearch.split(' ',2);
										nextAttempt(splitQuery[0].substring(0,1) ,splitQuery[1]);
									} else {
										//user did first search from advanced search
										splitQuery = originalSearch.split(' ',2);
										nextAttempt(splitQuery[0] ,splitQuery[1].substring(0,1));
									}
									return; //We started a new attempt, so stop here
								}
							}

							//we finally have results, or else we've abandonded the search options
							$results.html(data);

							// remove DOM-0 event listeners
							$('ul.pfResult li', $results).each(function(){
								$('.fn a', this).removeAttr('onclick');
							});

							$("#search-notice").slideDown(transitionTime);
							attempts = 1;

							filters.initialize();
							updateNumResults();
						},
						error: function(jqXHR, textStatus) {
							if (textStatus === 'abort') {
								return;
							}

							var tmpl = $.templates(genericErrorSelector);
							$results.html(tmpl.render());
						}
					});
				}, requestThrottleTime);
			} else if (rawQuery) {
				$results.html(tmpl.render());
			} else {
				$results.empty();
				setMainState();
			}
		},

		pfCatchUID : function(uid) {
			console.log('I caught ' + uid + '. You should create your own pfCatchUID function.');
			return false;
		},

		initialize: function(baseURL, annotateURL) {
			plugin.initialize = $.noop;
			serviceURL = baseURL;
			annotateServiceURL = annotateURL;

			// separate the document title into components and use the last 2 for title generation
			var titleSeparator = ' | ';
			documentTitleSuffix = titleSeparator + document.title.split(titleSeparator).slice(-2).join(titleSeparator);

			$(function() {
				WDN.initializePlugin('notice');

				// entry script element cleanup
				$('#main-entry').remove();
				$(annotateSelector).appendTo('body');

				// listen for hash change
				$(window).on('hashchange', function(eventObject){
					var hash = getCleanHash();
					if (hash && !isQueryHash(hash)) {
						return;
					} else if (!hash) {
						if (currentMainState === 0) {
							setMainState();
						}
						return;
					}

					hash = hash.split('/'); //hash[1]
					var splitName = false;
					var $q = $('#q');
					var query, sn, cn;

					if (hash.length >= 3){
						// if 3, then we're looking for first and last name individually.
						splitName = true;
						cn = decodeURIComponent(hash[1]);
						sn = decodeURIComponent(hash[2]);
						query = cn + ' ' + sn;
					} else {
						// it's all one search term.
						query = decodeURIComponent(hash[1]);
					}

					$q.val(query);

					setMainState(0);

					if (!splitName) {
						plugin.queuePFRequest(query, 'results');
					} else {
						plugin.queuePFRequest('', 'results', '', cn, sn);
					}
					document.title = 'Search for ' + query + documentTitleSuffix;

					return false;
				});

				// listen for state pops
				$(window).on('popstate', function(e) {
					var oEvent = e.originalEvent;

					if (oEvent.state) {
						if (oEvent.state.uid) {
							fetchRecord('person', oEvent.state.uid).then(function(data, textStatus) {
								if (textStatus !== 'success') {
									return;
								}

								var $card = $(data);
								addAnnotateTool(oEvent.state.uid, $card);
								addCorrectionTool($card.data('preferred-name'), $card);
								setMainState(1);
								displayOnlyRecord($card);
							}, function() {
								setMainState(0);
								var tmpl = $.templates(genericErrorSelector);
								$results.empty().append(tmpl.render());
							});
						} else {
							setMainState();
						}
					} else if (initialMainState === 2) {
						restoreRecordToResult();
						setMainState(initialMainState);
					} else {
						// if we are returning to a search
						var hash = getCleanHash();
						if (hash && isQueryHash(hash)) {
							restoreRecordToResult();
							showOrLoadInitialState(0);
							return;
						}

						if (currentMainState !== 0) {
							return;
						}

						showOrLoadInitialState();
					}
				});

				//trigger a hash change if a hash has been provided on load
				if (getCleanHash()) {
					$(window).trigger('hashchange');
				}

				// listen for print button clicks
				$(document).on('click', 'a.dir-btn-print-vcard', function(e) {
					setTimeout(window.print, 10);
					e.preventDefault();
				});

				$modal = $('#modal_edit_form');

				//Trap keyboard focus to the modal while it is open
				$(document).on('focusin', function(event) {
					var modal = $modal.get(0);
					if ($modal.hasClass('show') && !$.contains(modal, event.target) && modal != event.target) {
						event.stopPropagation();
						$modal.focus();
					}
				});

				$modal.on('keydown', function(e) {
					if (e.which === 27) {
						closeModalAndRestoreContent();
					}
				}).on('submit', 'form.edit', function(e) {
					var $forms = $(this).closest('.forms');

					e.preventDefault();
					closeModalAndRestoreContent();

					if ($forms.data('department-id')) {
						ajaxSubmitRefreshDepartment(this);
					} else if ($forms.data('listing-id')) {
						ajaxSubmitRefreshListing(this);
					} else {
						ajaxSubmitRefreshListing(this, '2');
					}
				}).on('submit', 'form.delete', function(e) {
					var $forms = $(this).closest('.forms');

					e.preventDefault();
					closeModalAndRestoreContent();

					// this should ONLY occur for listings
					if (!$forms.data('department-id')) {
						ajaxSubmitRemoveDepartmentList(this);
					}
				});

				if ($('#peoplefinder').length) {
					// default, help/search state loaded
					startFromSearch();
				} else if ($('.record-container .department-summary').length) {
					// single department record state loaded
					var $summarySection = $('.record-container .department-summary');
					var $editBox = $('#editBox');
					var $employees = $('#all_employees');
					var $listings = $('#listings');
					var $editableListings = $('.editing > ol.listings', $listings);
					var deleteDepartmentWarning = 'Are you sure? This will permanently delete this record and any child records.';

					initialMainState = 2;
					setMainState(initialMainState);
					bindResultsListeners($employees);
					bindRecordListeners($employees);
					createStickyKit($summarySection);

					$summarySection.on('click', '.vcard-tools .dir-btn-suggest-correction', function(e) {
						launchCorrectionModal($(e.target));
						return false;
					}).on('click', '.vcard-tools .dir-btn-delete', function(e) {
						if (!confirm(deleteDepartmentWarning)) {
							return false;
						}
					}).on('click', '.aliases .dir-btn-delete', function() {
						if (!confirm('Are you sure? This will delete the alias.')) {
							return false;
						}
					}).on('submit', '.aliases form.add', function(e) {
						var self = this;
						var tmpl = $.templates('#deparmentAliasTemplate');
						var $name = $('[name="name"]', this);
						var data = {
							url: this.action,
							name: $name.val(),
							department: $editBox.data('department-id')
						};

						e.preventDefault();
						ajaxSubmitToDepartmentList(this, e.delegateTarget, 'dept_aliases', tmpl, data);
					}).on('submit', '.aliases form.delete', function(e) {
						e.preventDefault();
						ajaxSubmitRemoveDepartmentList(this);
					}).on('click', '.users .dir-btn-delete', function() {
						if (!confirm('Are you sure? This will remove editing access for this user.')) {
							return false;
						}
					}).on('submit', '.users form.add', function(e) {
						var tmpl = $.templates('#departmentUserTemplate');
						var $name = $('[name="uid"]', this);
						var data = {
							url: this.action,
							userUrl: baseURL + 'people/' + $name.val(),
							uid: $name.val(),
							department: $editBox.data('department-id')
						};

						e.preventDefault();
						ajaxSubmitToDepartmentList(this, e.delegateTarget, 'dept_users', tmpl, data);
					}).on('submit', '.users form.delete', function(e) {
						e.preventDefault();
						ajaxSubmitRemoveDepartmentList(this);
					});

					if ($editableListings.length) {
						attachListingsEditing($editableListings);
					}

					$listings.on('click', '.listing-add', function(e) {
						var self = this;
						var loadedSelector = '.edit';
						var contentSelector = '.forms';
						var $contextForModal = $(this).closest('.edit-tools');
						var $loadTo = $('.form', $contextForModal);

						if (!$(loadedSelector, $loadTo).length) {
							$loadTo.load(this.href + '?' + $.param({format: 'partial'}), function() {
								showModalForm($contextForModal, contentSelector, self);
							});
						} else {
							showModalForm($contextForModal, contentSelector, self);
						}

						return false;
					}).on('submit', 'form.sortform', function(e) {
						e.preventDefault();
						$.post(this.action + '?' + $.param({redirect: 0}), $(this).serialize());
					});

					$modal.on('click.listings', '.dir-btn-delete', function(e) {
						if (!confirm(deleteDepartmentWarning)) {
							return false;
						}
					}).on('click.listings', '.listing-add', function(e) {
						var self = this;
						var $contextForModal = $(this).closest('.forms');
						var $loadTo = $('.add-form', $contextForModal);

						$loadTo.load(this.href + '?' + $.param({format: 'partial'}), function() {
							$contextForModal.addClass('show-add');
							$('input:visible', this).first().focus();
						});

						return false;
					});

					//Add the department correction tool
					if ($('.department-correction').length) {
						//This div only exists if the user does not have permission to edit already
						var $vcard = $summarySection.find('.vcard');
						addCorrectionTool($vcard.data('preferred-name'), $vcard);
					}
				} else if ($('.record-container .vcard').length) {
					// single person record state loaded
					initialMainState = 1;
					setMainState(initialMainState);
					var $vcard = $('.record-container .vcard');
					addAnnotateTool($vcard.data('uid'), $vcard);
					addCorrectionTool($vcard.data('preferred-name'), $vcard);
					bindRecordListeners($('.record-container'));

					var $knowledgeSummary = $('.record-container .directory-knowledge-summary');

					if ($knowledgeSummary.length) {
						createStickyKit($knowledgeSummary);
					}
				}

				$('button.correction').click(function(e){
					launchCorrectionModal($(e.target));
				});

				$('.corrections-template form').on('submit', function(e) {
					e.preventDefault();

					var $container = $(this).closest('.correction-form');
					$container.find('form').addClass('dcf-d-none');
					var $success = $container.find('.success');
					$success.text('Submitting...').removeClass('dcf-d-none').focus();

					$.post(this.action + '?' + $.param({format: 'json'}), $(this).serialize()).done(function() {
						$success.text('Thank you for your correction.').focus();
					}).fail(function(){
						$success.text('There was an error submitting the correction, please try again later.').focus();
					});
				});

				$('body').on('focusout', function(e) {
					var $target = $(e.target);
					if ($target.hasClass('programmatically-focused')) {
						$target.removeClass('programmatically-focused');
					}
				});
			});
		}
	};

	return plugin;
});
