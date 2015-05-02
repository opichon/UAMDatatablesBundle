<?php

namespace UAM\Bundle\DatatablesBundle\Controller;

use ModelCriteria;
use PropelCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Template()
 */
trait DatatablesEnabledControllerTrait
{
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

    public function listAction(Request $request)
    {
        $query = $this->getListQuery($request);

        $total_count = $query->count();

        $this->search(
            $query,
            $request
        );

        $filtered_count = $query->count();

        $this->sort($query, $request);

        $limit = $this->getLimit($request);
        $offset = $this->getOffset($request);

        $entities = $query
            ->setLimit($limit)
            ->setOffset($offset)
            ->find();

        $entities = $this->processEntities($entities, $request);

        return array_merge(
            $this->getExtraTemplateParameters($request),
            array(
                'total_count' => $total_count,
                'filtered_count' => $filtered_count,
                'entities' => $entities,
            )
        );
    }

    abstract protected function getListQuery(Request $request);

    /**
     * Defines the conditions for filtering the database records. This method
     * must return an array of SQL conditions indexed by filter name. For example:
     *    return array(
     *        'name' => array(
     *            'Person.Surname LIKE "%%%s%%"',
     *            'Person.GivenNames LIKE "%%%s%%"'
     *        ),
     *        'email' => 'Person.Email LIKE "%%%s%%"'
     *    );
     *
     * This method is invoked by the search method. The array keys are expected
     * to match the name of a widget in the filter type used on the page. The
     * array values are expected to be Propel SQL conditions that are consistent
     * with the query definedin the getListQuery method.
     *
     * @param Request $request The current request
     * @return Array
     * @see search
     */
    abstract protected function getSearchColumns(Request $request);

    /**
     * Defines the conditions for sorting the database records. The return value must be
     * an array of column names indexed by column number. For example:
     *     return array(
     *         1 => array(
     *             'Person.Name',
     *             'Person.GivenNames'
     *         )
     *         2 => 'Person.Email'
     *     );
     *
     * This method is invoked by the getSortOrder method. The array keys are
     * expected to match the indexes of cou,ns in the table. The array values
     * are expected to be consistent with the Propel query defined in getListQuery.
     *
     * @param Request $request the current request
     * @return Array
     * @see getSortOrder
     */
    abstract protected function getSortColumns(Request $request);

    /**
     * Defines the default sort order to apply to the database records. The method
     * must return an array.
     *
     * @param Request $request the current request
     * @return Array
     * @see getSortOrder, sort
     */
    abstract protected function getDefaultSortOrder(Request $request);

    protected function getFilters(Request $request)
    {
        return $request->query->get('_search');
    }

    /**
     * Defines a collection of additional parameters to be passed to the template.
     * The parameters defined in this method's reurn value will be added to the
     * parameters passed to the template in the `index` and `list` actions.
     *
     * @param Request $request the current request
     * @return Array
     * @see indexAction, listAction
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
    */
    protected function getFilterType(Request $request)
    {
        return;
    }

    /**
     * @param Request $request the current request
     */
    protected function getLimit(Request $request)
    {
        return min(
            $this->getMaxLimit($request),
            $request->query->get(
                'length',
                $this->getDefaultLimit($request)
            )
        );
    }

    /**
     * @param Request $request the current request
     */
    protected function getOffset(Request $request)
    {
        return max(
            $request->query->get('start', 0),
            $this->getDefaultOffset($request)
        );
    }

    /**
     * Returns the default limit.
     *
     * @param Request $request the current request
     * @return 10
     */
    protected function getDefaultLimit(Request $request)
    {
        return 10;
    }

    /**
     * Defines the maximum value for the query limit. This is a safety feature
     * to avoid overburdening the server.
     *
     * @param Request $request the current request
     * @return 100
     */
    protected function getMaxLimit(Request $request)
    {
        return 100;
    }

    /**
     * Returns the default offset value.
     *
     * @param Request $request the current request
     * @return 0;
     */
    protected function getDefaultOffset(Request $request)
    {
        return 0;
    }

    /**
     * Defines some optional processing of entities before they are passed to
     * the template. This method is invoked in the `listAction` after the
     * query has been run and before the entities are passed to the template.
     *
     * @param Request $request the current request
     */
    protected function processEntities(PropelCollection $entities, Request $request)
    {
        return $entities;
    }

    /**
     * Filters the database records. This method should be considered final for
     * all practical purposes.
     */
    protected function search(ModelCriteria $query, Request $request)
    {
        $filters = $this->getFilters($request);

        if (empty($filters)) {
            return $query;
        }

        $conditions = array();

        $columns = $this->getSearchColumns($request);

        if (is_array($filters)) {
            foreach ($columns as $name => $condition) {
                if (!array_key_exists($name, $filters)) {
                    continue;
                }

                $value = trim($filters[$name]);

                if (empty($value) && !is_numeric($value)) {
                    continue;
                }

                $query->condition(
                    'search_'.$name,
                    sprintf($condition, $value)
                );

                $conditions[] = 'search_'.$name;
            }

            if (!empty($conditions)) {
                return $query->where($conditions, 'and');
            }
        } else {
            $value = trim($filters);

            foreach ($columns as $name => $condition) {
                $query->condition(
                    'search_'.$name,
                    sprintf($condition, $value)
                );

                $conditions[] = 'search_'.$name;
            }

            if (!empty($conditions)) {
                return $query->where($conditions, 'or');
            }
        }
    }

    /**
     * Sorts the database records. This method should be considered final for
     * all practical purposes.
     */
    protected function sort(ModelCriteria $query, Request $request)
    {
        $order = $this->getSortOrder($request);

        foreach ($order as $setting) {
            $column = $setting[0];
            $direction = $setting[1];
            $query->orderBy($column, $direction);
        }

        return $this;
    }

    /**
     * Computes the sort order. This method should be considered final for
     * all practical purposes.
     *
     * @param Request $request the current request
     *
     * @return Array
     */
    protected function getSortOrder(Request $request)
    {
        $sort = array();

        $order = $request->query->get('order', array());

        $columns = $this->getSortColumns($request);

        foreach ($order as $setting) {
            $index = $setting['column'];

            if (array_key_exists($index, $columns)) {
                $column = $columns[$index];

                if (!is_array($column)) {
                    $column = array($column);
                }

                foreach ($column as $c) {
                    $sort[] = array(
                        $c,
                        $setting['dir'],
                    );
                }
            }
        }

        // Default sort order
        if (empty($sort)) {
            $sort = $this->getDefaultSortOrder($request);
        }

        return $sort;
    }
}
