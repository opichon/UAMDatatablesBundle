<?php

/**
 * @copyright 2015 United Asian Management Limited. All rights reserved
 * @license MIT
 */

namespace UAM\Bundle\DatatablesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides convenience methods for implementing an index page listing database
 * records as entities using the dataTables jquery plugin with server-side data.
 *
 * @Template()
 */
trait DatatablesApiControllerTrait
{
    abstract protected function getEntityManager();

    /**
     * List action. Returns the records data in JSON format. This action is the target
     * of the dataTables plugin's ajax request for obtaining server-side data.
     *
     * @param Request $request the current request
     *
     * @return array An array of template parameters. These include:
     *               * `entities`: the PropelCollection or array of entities returned by the Propel query
     *               * `total_count`: the total number of records (before any filters are applied)
     *               * `filtered_count`: the number of records after filters are applied
     *
     * @uses getLimit()
     */
    public function listAction(Request $request)
    {
        $manager = $this->getEntityManager();

        $total_count = $manager->getTotalCount($request);

        $filtered_count = $manager->getFilteredCount($request);

        $entities = $manager->getEntities($request);

        return array_merge(
            $this->getExtraTemplateParameters($request),
            array(
                'total_count' => $total_count,
                'filtered_count' => $filtered_count,
                'entities' => $entities,
            )
        );
    }

    /**
     * Defines a collection of additional parameters to be passed to the template.
     * The parameters defined in this method's reurn value will be added to the
     * parameters passed to the template in the `index` and `list` actions.
     *
     * @param Request $request the current request
     *
     * @return array additional parameters to be passed to the template
     *
     * @see DatablesEnabledControllerTrait::indexAction()
     * @see DatablesEnabledControllerTrait::listAction()
     */
    protected function getExtraTemplateParameters(Request $request)
    {
        return array();
    }
}
