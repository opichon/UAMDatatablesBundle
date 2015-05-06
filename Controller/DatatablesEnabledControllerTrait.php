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

        if ($filter_type = $this->getFilterType($request)) {
            $filter = $this->createForm($filter_type);

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
     * Defines the form type to be used to create custom filters on the page.
     * These filters are typically used as per-column filters and displayed in
     * each column header.
     *
     * If this methods returns a non-null value, a form will be automatically
     * created by the `index` action and passed to the template as a parameter
     * named `filter`. You can then use this parameter in your `index.html.twig`
     * template to render the form's widgets.
     *
     * @param Request $request the current request
     * @return FormType|null the form type for creating filters
     */
    protected function getFilterType(Request $request)
    {
        return;
    }

}
