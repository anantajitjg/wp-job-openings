/* global awsmJobsPublic */

'use strict';

jQuery(function($) {
	var rootWrapperSelector = '.awsm-job-wrap';
	var wrapperSelector = '.awsm-job-listings';

	/* ========== Job Search and Filtering ========== */

	var filterSelector = '.awsm-filter-wrap';
	var currentUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
	var triggerFilter = true;

	function awsmJobFilters($rootWrapper) {
		var $wrapper = $rootWrapper.find(wrapperSelector);
		var $filterForm = $rootWrapper.find(filterSelector + ' form');
		var formData = $filterForm.serializeArray();
		var listings = $wrapper.data('listings');
		var specs = $wrapper.data('specs');
		formData.push({
			name: 'listings_per_page',
			value: listings
		});
		if (typeof specs !== 'undefined') {
			formData.push({
				name: 'shortcode_specs',
				value: specs
			});
		}
		if (triggerFilter) {

			// stop the duplicate requests.
			triggerFilter = false;

			// now, make the request.
			$.ajax({
				url: $filterForm.attr('action'),
				beforeSend: function() {
					$wrapper.addClass('awsm-jobs-loading');
				},
				data: formData,
				type: $filterForm.attr('method')
			}).done(function(data) {
				$wrapper.html(data);
				var $searchControl = $rootWrapper.find('.awsm-job-search');
				if ($searchControl.length > 0) {
					if ($searchControl.val().length > 0) {
						$rootWrapper.find('.awsm-job-search-btn').addClass('awsm-job-hide');
						$rootWrapper.find('.awsm-job-search-close-btn').removeClass('awsm-job-hide');
					} else {
						$rootWrapper.find('.awsm-job-search-btn').removeClass('awsm-job-hide');
					}
				}
				$(document).trigger('awsmjobs_filtered_listings', [ $rootWrapper, data ]);
			}).fail(function(xhr) {
				// eslint-disable-next-line no-console
				console.log(xhr);
			}).always(function() {
				$wrapper.removeClass('awsm-jobs-loading');
				triggerFilter = true;
			});
		}
	}

	function filterCheck($filterForm) {
		var check = false;
		if ($filterForm.length > 0) {
			var $filterOption = $filterForm.find('.awsm-filter-option');
			$filterOption.each(function() {
				if ($(this).val().length > 0) {
					check = true;
				}
			});
		}
		return check;
	}

	function searchJobs($elem) {
		var $rootWrapper = $elem.parents(rootWrapperSelector);
		var searchQuery = $rootWrapper.find('.awsm-job-search').val();
		$rootWrapper.find(wrapperSelector).data('search', searchQuery);
		if (searchQuery.length === 0) {
			$rootWrapper.find('.awsm-job-search-icon-wrapper').addClass('awsm-job-hide');
		}
		setPaginationBase($rootWrapper, 'jq', searchQuery);
		if (awsmJobsPublic.deep_linking.search) {
			var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
			updateQuery('jq', searchQuery, $paginationBase.val());
		}
		awsmJobFilters($rootWrapper);
	}

	if ($(rootWrapperSelector).length > 0) {
		$(rootWrapperSelector).each(function() {
			var $currentWrapper = $(this);
			var $filterForm = $currentWrapper.find(filterSelector + ' form');
			if (awsmJobsPublic.is_search.length > 0 || filterCheck($filterForm)) {
				triggerFilter = true;
				awsmJobFilters($currentWrapper);
			}
		});
	}

	var updateQuery = function(key, value, url) {
		url = typeof url !== 'undefined' ? url : currentUrl;
		url = url.split('?')[0];
		var searchParams = new URLSearchParams(document.location.search);
		if (searchParams.has('paged')) {
			searchParams.delete('paged');
		}
		if (value.length > 0) {
			searchParams.set(key, value);
		} else {
			searchParams.delete(key);
		}
		var modQueryString = searchParams.toString();
		if (modQueryString.length > 0) {
			modQueryString = '?' + modQueryString;
		}
		window.history.replaceState({}, '', url + modQueryString);
	};

	var setPaginationBase = function($rootWrapper, key, value) {
		var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
		if ($paginationBase.length > 0) {
			var splittedURL = $paginationBase.val().split('?');
			var queryString = '';
			if (splittedURL.length > 1) {
				queryString = splittedURL[1];
			}
			var searchParams = new URLSearchParams(queryString);
			if (value.length > 0) {
				searchParams.set(key, value);
			} else {
				searchParams.delete(key);
			}
			$paginationBase.val(splittedURL[0] + '?' + searchParams.toString());
			$rootWrapper.find('input[name="paged"]').val(1);
		}
	};

	$(filterSelector + ' .awsm-filter-option').on('change', function(e) {
		e.preventDefault();
		var $elem = $(this);
		var $selected = $elem.find('option:selected');
		var $rootWrapper = $elem.parents(rootWrapperSelector);
		var currentSpec = $elem.parents('.awsm-filter-item').data('filter');
		var slug = $selected.data('slug');
		slug = typeof slug !== 'undefined' ? slug : '';
		setPaginationBase($rootWrapper, currentSpec, slug);
		if (awsmJobsPublic.deep_linking.spec) {
			var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
			updateQuery(currentSpec, slug, $paginationBase.val());
		}
		awsmJobFilters($rootWrapper);
	});

	$(filterSelector + ' .awsm-job-search-btn').on('click', function() {
		searchJobs($(this));
	});

	$(filterSelector + ' .awsm-job-search-close-btn').on('click', function() {
		var $elem = $(this);
		$elem.parents(rootWrapperSelector).find('.awsm-job-search').val('');
		searchJobs($elem);
	});

	$(filterSelector + ' .awsm-job-search').on('keypress', function(e) {
		if (e.which == 13) {
			e.preventDefault();
			searchJobs($(this));
		}
	});

	/* ========== Job Listings Load More ========== */

	$(wrapperSelector).on('click', '.awsm-jobs-pagination .awsm-load-more-btn, .awsm-jobs-pagination a.page-numbers', function(e) {
		e.preventDefault();
		var $triggerElem = $(this);
		var isDefaultPagination = $triggerElem.hasClass('awsm-load-more-btn');
		var paged = 1;
		var wpData = [];
		var $mainContainer = $triggerElem.parents(rootWrapperSelector);
		var $listingsContainer = $mainContainer.find(wrapperSelector);
		var $paginationWrapper = $triggerElem.parents('.awsm-jobs-pagination');
		var listings = $listingsContainer.data('listings');
		var specs = $listingsContainer.data('specs');
		var lang = $listingsContainer.data('lang');
		var searchQuery = $listingsContainer.data('search');

		if (isDefaultPagination) {
			$triggerElem.prop('disabled', true);
			paged = $triggerElem.data('page');
			paged = (typeof paged == 'undefined') ? 1 : paged;
		} else {
			$triggerElem.parents('.page-numbers').find('.page-numbers').removeClass('current').removeAttr('aria-current');
			$triggerElem.addClass('current').attr('aria-current', 'page');
		}
		$paginationWrapper.addClass('awsm-jobs-pagination-loading');

		// filters
		var $filterForm = $mainContainer.find(filterSelector + ' form');
		if (filterCheck($filterForm)) {
			var $filterOption = $filterForm.find('.awsm-filter-option');
			wpData = $filterOption.serializeArray();
		}

		if (! isDefaultPagination) {
			var paginationBaseURL = $triggerElem.attr('href');
			var splittedURL = paginationBaseURL.split('?');
			var queryString = '';
			if (splittedURL.length > 1) {
				var searchParams = new URLSearchParams(splittedURL[1]);
				paged = searchParams.get('paged');
				searchParams.delete('paged');
				if (searchParams.toString().length > 0) {
					queryString = '?' + searchParams.toString();
				}
			}
			paginationBaseURL = splittedURL[0] + queryString;
			wpData.push({
				name: 'awsm_pagination_base',
				value: splittedURL[0] + queryString
			});
			if (awsmJobsPublic.deep_linking.pagination) {
				updateQuery('paged', paged, paginationBaseURL);
			}
		}

		// taxonomy archives
		if (awsmJobsPublic.is_tax_archive) {
			var taxonomy = $listingsContainer.data('taxonomy');
			var termId = $listingsContainer.data('termId');
			if (typeof taxonomy !== 'undefined' && typeof termId !== 'undefined') {
				wpData.push({
					name: 'awsm_job_spec[' + taxonomy + ']',
					value: termId
				});
			}
		}

		wpData.push({
			name: 'action',
			value: 'loadmore'
		}, {
			name: 'paged',
			value: paged
		});
		if (typeof listings !== 'undefined') {
			wpData.push({
				name: 'listings_per_page',
				value: listings
			});
		}
		if (typeof specs !== 'undefined') {
			wpData.push({
				name: 'shortcode_specs',
				value: specs
			});
		}
		if (typeof lang !== 'undefined') {
			wpData.push({
				name: 'lang',
				value: lang
			});
		}
		if (typeof searchQuery !== 'undefined') {
			wpData.push({
				name: 'jq',
				value: searchQuery
			});
		}

		// now, handle ajax
		$.ajax({
			url: awsmJobsPublic.ajaxurl,
			data: $.param(wpData),
			type: 'POST',
			beforeSend: function() {
				if (isDefaultPagination) {
					$triggerElem.text(awsmJobsPublic.i18n.loading_text);
				} else {
					$listingsContainer.addClass('awsm-jobs-loading');
				}
			}
		}).done(function(data) {
			if (data) {
				var effectDuration = $paginationWrapper.data('effectDuration');
				$paginationWrapper.remove();
				if (isDefaultPagination) {
					$listingsContainer.append(data);
				} else {
					$listingsContainer.html(data);
					$listingsContainer.removeClass('awsm-jobs-loading');
					if (typeof effectDuration !== 'undefined') {
						effectDuration = isNaN(effectDuration) ? effectDuration : Number(effectDuration);
						$('html, body').animate({
							scrollTop: $mainContainer.offset().top - 25
						}, effectDuration);
					}
				}
			} else {
				$triggerElem.remove();
			}
			$(document).trigger('awsmjobs_load_more', [ $triggerElem, data ]);
		}).fail(function(xhr) {
			// eslint-disable-next-line no-console
			console.log(xhr);
		});
	});

	/* ========== Custom select box - selectric ========== */

	function awsmDropDown($elem) {
		if ('selectric' in awsmJobsPublic.vendors && awsmJobsPublic.vendors.selectric) {
			$elem.selectric({
				onInit: function(select, selectric) {
					var id = select.id;
					var $input = $(selectric.elements.input);
					$(select).attr('id', 'selectric-' + id);
					$input.attr('id', id);
				},
				arrowButtonMarkup: '<span class="awsm-selectric-arrow-drop">&#x25be;</span>',
				customClass: {
					prefix: 'awsm-selectric',
					camelCase: false
				}
			});
		}
	}
	awsmDropDown($('.awsm-job-select-control'));
	awsmDropDown($('.awsm-filter-item select'));
});
