<?php
namespace Swissbib\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Format physical descriptions data array
 * Fetch all relevant data and build a comma separated list
 *
 */
class PhysicalDescriptions extends AbstractHelper
{

	/**
	 * Format integer with thousand separator
	 *
	 * @param    Array        $physicalDescriptions
	 * @return    String
	 */
	public function __invoke(array $physicalDescriptions)
	{
		$infos = array();
		$types = array('extent', 'details', 'dimensions', 'company', 'material_multiple');

		foreach ($physicalDescriptions as $physicalDescription) {
			foreach ($types as $type) {
				if (isset($physicalDescription[$type])) {
					if (is_array($physicalDescription[$type])) {
						$infos = array_merge($infos, $physicalDescription[$type]);
					} else {
						$infos[] = $physicalDescription[$type];
					}
				}
			}
		}

		return implode('; ', $infos);
	}

}