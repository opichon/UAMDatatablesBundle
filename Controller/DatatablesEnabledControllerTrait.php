<?php

/**
 * @copyright 2015 United Asian Management Limited. All rights reserved.
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
trait DatatablesEnabledControllerTrait
{
    abstract protected function getEntityManager();

    /**
     * Index action. Displays the table structure (headers and empty body).
     *
     * @param Request $request Thecurrent request
     * @return Array An array of template parameters. These include:
     * * `filter`: If {@see DatatablesEnabledControllerTrait::getFilterType()} returns a non-null value, the form based on this type
     * * If {@see DatatablesEnabledControllerTrait::getExtraTemplateParameters()} returns a non-empty array, these parameters are
     * included
     * @uses DatatablesEnabledControllerTrait::getFilterType()
     * @uses DatatablesEnabledControllerTrait::getExtraTemplateParameters()
     */
    public function indexAction(Request $request)
    {
        $parameters = array();

        if ($filter = $this->getFilter($request)) {
            $parameters['filter'] = $filter->createView();
        }

        return array_merge(
            $this->getExtraTemplateParameters($request),
            $parameters
        );
    }

    /**
     * List action. Returns the records data in JSON format. This action is the target
     * of the dataTables plugin's ajax request for obtaining server-side data.
     *
     * @param Request $request the current request
     * @return Array An array of template parameters. These include:
     * * `entities`: the PropelCollection or array of entities returned by the Propel query
     * * `total_count`: the total number of records (before any filters are applied)
     * * `filtered_count`: the number of records after filters are applied
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
     * @return Array additional parameters to be passed to the template
     * @see DatablesEnabledControllerTrait::indexAction()
     * @see DatablesEnabledControllerTrait::listAction()
     */
    protected function getExtraTemplateParameters(Request $request)
    {
        return array();
    }

    /**
     * Returns a form that will be used by the `index` action to create a view
     * which is then passed to the `index.html.twig` template as a parameter
     * named `filter`.
     *
     * @param Request $request the current request
     * @return Symfony\Component\Form\FormInterface|null a form
     * @uses EntityManagerInterface::getFilterType
     */
    protected function getFilter(Request $request)
    {
        if ($filter_type = $this->getEntityManager()->getFilterType($request)) {
            return $this->createForm($filter_type);
        }
    }
}
