<?php
/**
 * Central class for connecting to resources used by VuFind.
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2011.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind2
 * @package  Connection
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace VuFind\Connection;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Central class for connecting to resources used by VuFind.
 *
 * @category VuFind2
 * @package  Connection
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Manager
{
    protected static $serviceLocator;

    /**
     * Set the service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator Locator to register
     *
     * @return void
     */
    public static function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        self::$serviceLocator = $serviceLocator;
    }

    /**
     * Connect to the index.
     *
     * @param string $type Index type to connect to (null for standard Solr).
     * @param string $core Index core to use (null for default).
     * @param string $url  Connection URL for index (null for config.ini default).
     *
     * @return object
     */
    public static function connectToIndex($type = null, $core = null, $url = null)
    {
        $configArray = self::$serviceLocator->get('VuFind\Config')->get('config');

        // Load config.ini settings for missing parameters:
        if ($type == null) {
            $type = 'Solr';
        }
        if ($url == null) {
            // Load appropriate default server URL based on index type:
            $url = ($type == 'SolrStats' && isset($configArray->Statistics->solr))
                ? $configArray->Statistics->solr : $configArray->Index->url;
        }

        // Set appropriate default core if necessary:
        if (empty($core) && $type == 'Solr') {
            $core = isset($configArray->Index->default_core)
                ? $configArray->Index->default_core : "biblio";
        }

        // Construct the object appropriately based on the $core setting:
        $class = 'VuFind\Connection\\' . $type;
        if (empty($core)) {
            $index = new $class($url);
        } else {
            $index = new $class($url, $core);
        }

        // Set the service locator:
        $index->setServiceLocator(self::$serviceLocator);

        // Inject dependencies with the standard initializer:
        if (null !== self::$serviceLocator) {
            \VuFind\ServiceManager\Initializer::initInstance(
                $index, self::$serviceLocator
            );
        }

        return $index;
    }
}
