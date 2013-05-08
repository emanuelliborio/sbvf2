<?php

namespace Swissbib\Controller;

use Zend\Session\Container as SessionContainer;

use Swissbib\Controller\Helper\Search as SearchHelper;
use Swissbib\Controller\SearchController;

class SummonController extends SearchController
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->useResultScroller = false;
		$this->forceTabKey       = 'summon';

		parent::__construct();
	}



	/**
	 * Handle an advanced search
	 *
	 * @return mixed
	 */
	public function advancedAction()
	{
		// Standard setup from base class:
		$view = parent::advancedAction();

		// Set up facet information:
		$view->facetList = $this->processAdvancedFacets(
			$this->getAdvancedFacets()->getFacetList(), $view->saved
		);

		return $view;
	}



	/**
	 * Home action
	 *
	 * @return mixed
	 */
	public function homeAction()
	{
		return $this->createViewModel(
			array('results' => $this->getHomePageFacets())
		);
	}



	/**
	 * Search action -- call standard results action
	 *
	 * @return mixed
	 */
	public function searchAction()
	{
		return $this->resultsAction();
	}



	/**
	 * Return a Search Results object containing advanced facet information.  This
	 * data may come from the cache.
	 *
	 * @return \VuFind\Search\Summon\Results
	 */
	protected function getAdvancedFacets()
	{
		// Check if we have facet results cached, and build them if we don't.
		$cache = $this->getServiceLocator()->get('VuFind\CacheManager')
				->getCache('object');
		if (!($results = $cache->getItem('summonSearchAdvancedFacets'))) {
			$results = $this->getResultsManager()->get('Summon');
			$params  = $results->getParams();
			$params->addFacet('Language,or,1,20');
			$params->addFacet('ContentType,or,1,20', 'Format');

			// We only care about facet lists, so don't get any results:
			$params->setLimit(0);

			// force processing for cache
			$results->getResults();

			$cache->setItem('summonSearchAdvancedFacets', $results);
		}

		// Restore the real service locator to the object (it was lost during
		// serialization):
		$results->restoreServiceLocator($this->getServiceLocator());
		return $results;
	}



	/**
	 * Return a Search Results object containing homepage facet information.  This
	 * data may come from the cache.
	 *
	 * @return \VuFind\Search\Summon\Results
	 */
	protected function getHomePageFacets()
	{
		// For now, we'll use the same fields as the advanced search screen.
		return $this->getAdvancedFacets();
	}



	/**
	 * Process the facets to be used as limits on the Advanced Search screen.
	 *
	 * @param array  $facetList    The advanced facet values
	 * @param object $searchObject Saved search object (false if none)
	 *
	 * @return array               Sorted facets, with selected values flagged.
	 */
	protected function processAdvancedFacets($facetList, $searchObject = false)
	{
		// Process the facets, assuming they came back
		foreach ($facetList as $facet => $list) {
			foreach ($list['list'] as $key => $value) {
				// Build the filter string for the URL:
				$fullFilter = $facet . ':"' . $value['value'] . '"';

				// If we haven't already found a selected facet and the current
				// facet has been applied to the search, we should store it as
				// the selected facet for the current control.
				if ($searchObject
						&& $searchObject->getParams()->hasFilter($fullFilter)
				) {
					$facetList[$facet]['list'][$key]['selected'] = true;
					// Remove the filter from the search object -- we don't want
					// it to show up in the "applied filters" sidebar since it
					// will already be accounted for by being selected in the
					// filter select list!
					$searchObject->getParams()->removeFilter($fullFilter);
				}
			}
		}
		return $facetList;
	}
}
