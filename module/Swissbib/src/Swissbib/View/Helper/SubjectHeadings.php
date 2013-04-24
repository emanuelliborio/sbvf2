<?php
namespace Swissbib\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * view script for rendering subject headings of all vocabulairies, including local ones
 *
 * @category swissbib_VuFind2
 * @package  ViewHelpers
 * @author   Oliver Schihin oliver.schihin@unibas.ch
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */

class SubjectHeadings extends AbstractHelper
{

	public function __invoke($subjectHeadings, $resultItem = null)
	{
		return '<h4>from the view helper SubjectHeadings</h4>';
//        foreach ($subjectHeadings as $heading) {
//            if ($heading['@ind2'] === '0') {
//                $lcsh = '<p>asdf</p>';
//                return $lcsh;
//            }
//        }
	}
}

/**
<? foreach ($subjectHeadings as $heading):
?>
<? // if ($heading['@ind2'] === '0') :?>
	<!--    <h4>LCSH</h4>-->
	<!--    <p>--><?//=$this->escapeHtml($heading['600a'])?><!--</p>-->
	<!--    --><? // endif; ?>
<? // if ($heading['@ind2'] === '7' && $heading['6502'] === 'gnd') :?>
	<!--    <h4>GND-Schlagworte</h4>-->
	<!--    <p>--><?//=$this->escapeHtml($heading['650a'])?><!--</p>-->
	<!--    --><? // endif; ?>
<? // if ($heading['@ind2'] === '7' && $heading['6512'] === 'ids zbz') :?>
	<!--    <h4>ZB-Schlagwörter</h4>-->
	<!--    <p>--><?//=$this->escapeHtml($heading['651a'])?><!--</p>-->
	<!--    --><? // endif; ?>
<? // endforeach; ?>
 */
