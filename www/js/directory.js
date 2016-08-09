define([
	'jquery',
	'wdn',
	'modernizr',
	'require',
	'notice',
	'tooltip',
	'./vendor/jsrender.js'
], function($, WDN, Modernizr, require) {
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
	var mainSelector = '#maincontent';
	var annotateSelector = '#annotateTemplate';
	var mainStates = ['searching', 'single', 'single-dept'];
	var $searcher;
	var $results;
	var $filters;
	var departments = [];
	var affiliations = [];
	var modalReady = false;
	var initialMainState;
	var currentMainState;
	var documentTitleSuffix = '';
	var resultsContainerSelector = '.results-container';
	var emptyFilterClass = 'empty-filters';

	var filters = {
		initialize : function() {
			var $filterContainer = $('.filters', $filters);
			var $options = filters.clear();
			var $resultLists = $('.results', $results);
			var $summary = $('.summary', $results);
			var $total = $('.ppl_Sresult', $results);

			if (!$resultLists.length || !$total.length) {
				$filters.closest(resultsContainerSelector).addClass(emptyFilterClass);
				return;
			}

			$filterContainer.addClass('loading');
			$filters.closest(resultsContainerSelector).removeClass(emptyFilterClass);

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
			departments.sort();
			affiliations.sort();

			filters.buildFilters(departments, 'department');
			filters.buildFilters(affiliations, 'affiliation');

			if ($(window).width() >= 768) {
				$options.slideDown(100);
				$options.attr('aria-expanded', 'true');
				$('.toggle', $filters).text('(Collapse)');
			} else {
				$options.slideUp(100);
				$options.attr('aria-expanded', 'false');
				$('.toggle', $filters).text('(Expand)');
			}

			$filterContainer.removeClass('loading');

			if (!$summary.length) {
				$summary = $($.templates('#summaryTemplate').render())
					.append(filters.generateAllSummaryOption())
					.prependTo($results);
			}

			departments = [];
			affiliations = [];

			//Hide the filters if there is only a few results (on mobile)
			$filters.removeClass('few-results');
			$filters.removeClass('many-results');

			if ($total.length <= 10) {
				$filters.addClass('few-results');
			} else {
				$filters.addClass('many-results');
			}
		},

		clear: function() {
			return $('.filter-options', $filters).empty();
		},

		generateAllSummaryOption: function() {
			var tmpl = $.templates('#summaryAllTemplate');
			return $(tmpl.render());
		},

		buildFilters : function(array, type) {
			var $optionContainer = $('#filters_' + type);
			var $optionList = $('ol', $optionContainer);
			var tmpl = $.templates('#filterOptionTemplate');
			var options = [];

			if (!$optionList.length) {
				$optionList = $($.templates('#filterOptionListTempalte').render({type:type}))
					.appendTo($optionContainer);
			}

			$.each(array, function(key, value){
				options.push($(tmpl.render({type:filters.scrubDept(value.toLowerCase()), label:value})));
			});

			$optionList.append(options);
		},

		action : function(checkbox) {
			var checked = [];
			var resultsSelector = '.result_head, div.affiliation, div.results ul li';
			var filterElement = 'input';
			var stateProperty = 'checked';
			var activeFilterSelector = filterElement + ':' + stateProperty;
			var allFilterClass = 'filterAll';
			var allFilterSelector = '.' + allFilterClass;
			var $optionGroup = checkbox.closest('.filter-options');
			var filterState = checkbox[0].checked;

			var showState = function() {
				var $checkedFilters = $(activeFilterSelector, $filters).not(allFilterSelector);

				if (!$checkedFilters.length) {
					// return to show everything
					$(resultsSelector, $results).show();
					return;
				}

				// selectively show records
				$(resultsSelector, $results).hide();
				$checkedFilters.each(function(){
					var value = $(this).attr('value');
					var id = $(this).attr('id');

					if (this.checked) {
						// make sure the corresponding content is shown.
						$('li.' + value, $results).show()
							.closest('.affiliation').show();
						checked.push(id);
					}
				});
			};

			var showAll = function(full) {
				var $scope = $optionGroup;
				if (full) {
					$scope = $filters;
				}
				$(filterElement, $scope).not(allFilterSelector).prop(stateProperty, false);
				$(allFilterSelector, $scope).prop(stateProperty, true);
				showState();
			};

			if ((checkbox.hasClass(allFilterClass) && filterState) || !$(activeFilterSelector, $optionGroup).length) {
				showAll();
			} else {
				$(allFilterSelector, $optionGroup).prop(stateProperty, false);
				showState();
			}

			var $summary = $('.summary', $results);
			$('.selected-options, .operator', $summary).remove();

			if (checked.length < 1) {
				//nothing in the array, therefore it's ALL
				showAll(true);
				$summary.append(filters.generateAllSummaryOption());
			} else {
				//at least one id exists in the array
				var summaryOptions = [];
				var tmpl = $.templates('#summaryFilterTemplate');
				$.each(checked, function(key, value) {
					var $selected = $('#' + value);

					if (!$selected.length) {
						return;
					}

					var $legend = $selected.closest('.filter-options').prev('button');
					var templateData = {
						filterType: $legend.clone().children().remove().end().text(),
						filterValue: $selected.attr('value'),
						filterLabel: $selected.siblings('label').text()
					};

					summaryOptions.push(document.createTextNode(' '));
					summaryOptions.push(tmpl.render(templateData));
					summaryOptions.push(document.createTextNode(' '));
				});

				$summary.append(summaryOptions);
				$('.operator:last-child', $summary).remove();
			}
		},

		scrubDept : function(string) {
			return string.split(' ').join('').replace(/&|,/gi, '');
		}
	};

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
		})).appendTo($('.vcard-tools', $vcard));
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
			url = serviceURL + 'hcards/' + recordId;
		}

		return $.ajax({url: url});
	};

	var loadFullRecord = function(recordType, liRecord) {
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
			$overview.slideDown();
			$loadedChild.slideUp();
			liRecord.removeClass('selected');

			return;
		}

		// reset the current and selected states
		$('li.current', $results).removeClass('current');
		liRecord.addClass('selected current');

		if ($loadedChild.length) {
			// we already loaded the record
			$overview.slideUp();
			$loadedChild.slideDown();
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

			// load annotation tool for people records
			if (recordType !== 'org') {
				addAnnotateTool(infoData, $card);
			}

			$overview.slideUp();
			$card.slideDown();
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
		$container.on('click', 'button.icon-print', function(e) {
			if (currentMainState === 1) {
				// allow the event to bubble to the printer
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

		$container.on('click', '.icon-qr-code', function() {
			var self = this;
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

		$container.on('click', '.wdn_annotate', function(e) {
			var $this = $(this);

			e.preventDefault();

			$this.qtip({
				overwrite: false,
				content: {
					text: $('<iframe>', {
						src: this.href,
						scrolling: 'no',
						"class": 'wdn_annotate_note',
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
		$filters = $('#filters');

		//on submit of the search form, redirect to hashchange
		$searcher.submit(function(eventObject) {
			$("#search-notice").slideUp(function() {
				$(this).empty();
			});

			var query = $('#q').val().trim();

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

		$filters.on('click', 'button', function (e) {
			var $header = $(this);
			var $container = $header.next();
			var $toggle = $('.toggle', $header);

			$container.slideToggle(100, function () {
				if ($container.is(":visible")) {
					//Expanded
					$toggle.text("(Collapse)");
					$container.attr('aria-expanded', 'true');
					$container.focus();
				} else {
					//Collapsed
					$toggle.text("(Expand)");
					$container.attr('aria-expanded', 'false');
				}
			});
		});

		$filters.on('click', 'input', function(e) {
			filters.action($(this));
		});
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
			$modalClose = $('<button>', {"class": 'wdn-icon-cancel'})
				.click(function() {
					closeModalAndRestoreContent();
				})
				.append($('<span>', {"class": 'wdn-text-hidden'}).text('Close'));
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

		$editableListings.on('click', '.icon-pencil', function(e) {
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
		require(['./vendor/jquery.sticky-kit.js'], function() {
			var checkSticky = function() {
				$(document.body).trigger('sticky_kit:recalc');

				if (Modernizr.mq('only screen and (min-width: 768px)')) {
					$sidebar.stick_in_parent({spacer:false});
				} else {
					$sidebar.trigger('sticky_kit:detach');
				}
			};
			$(window).on('resize', checkSticky);
			checkSticky();
		});
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
								var tmpl = $.templates(searchNoticeSelector);
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
				$(document).on('click', 'button.icon-print', function(e) {
					setTimeout(window.print, 10);
					e.preventDefault();
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
					var deleteDepartmentWarning = 'Are you sure? This will permanently delete this department and all its children.';

					initialMainState = 2;
					setMainState(initialMainState);
					bindResultsListeners($employees);
					bindRecordListeners($employees);
					createStickyKit($summarySection);

					$modal = $('#modal_edit_form');
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

					$(document).on('click', function(e) {
						if (!$(e.target).closest(modalContentSelector).length) {
							closeModalAndRestoreContent();
						}
					});

					$summarySection.on('click', '.vcard-tools .icon-pencil', function(e) {
						showModalForm($editBox, '.forms', this);
						return false;
					}).on('click', '.vcard-tools .icon-trash', function(e) {
						if (!confirm(deleteDepartmentWarning)) {
							return false;
						}
					}).on('click', '.aliases .icon-trash', function() {
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
					}).on('click', '.users .icon-trash', function() {
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

					$modal.on('click.listings', '.icon-trash', function(e) {
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
				} else if ($('.record-container .vcard').length) {
					// single person record state loaded
					initialMainState = 1;
					setMainState(initialMainState);
					var $vcard = $('.record-container .vcard');
					addAnnotateTool($vcard.data('uid'), $vcard);
					bindRecordListeners($('.record-container'));

					var $knowledgeSummary = $('.record-container .directory-knowledge-summary');

					if ($knowledgeSummary.length) {
						createStickyKit($knowledgeSummary);
					}
				}
			});
		}
	};

	return plugin;
});
