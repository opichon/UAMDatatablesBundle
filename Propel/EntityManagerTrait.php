<?php

namespace UAM\Bundle\DatatablesBundle\Propel;

use ModelCriteria;
use PropelCollection;
use Symfony\Component\HttpFoundation\Request;

trait EntityManagerTrait
{
    /**
     * Returns the Propel query to be used to retrieve records from the database,
     *
     * @param Request $request the current request
     * @return ModelCriteria The Propel query to use for retrieving records in the database
     */
    abstract protected function getQuery(Request $request);

    /**
     * Defines the conditions for filtering the database records. This method
     * must return an array of SQL conditions indexed by filter name. For example:
     *
     * ```
     *    return array(
     *        'name' => array(
     *            'Person.Surname LIKE "%%%s%%"',
     *            'Person.GivenNames LIKE "%%%s%%"'
     *        ),
     *        'email' => 'Person.Email LIKE "%%%s%%"'
     *    );
     * ```
     *
     * This method is invoked by the search method. The array keys are expected
     * to match the name of a widget in the filter type used on the page. The
     * array values are expected to be Propel SQL conditions that are consistent
     * with the query definedin the getListQuery method.
     *
     * @param Request $request The current request
     * @return Array the search conditions
     * @see DatatablesEnabledControllerTrait::search()
     */
    abstract protected function getSearchColumns(Request $request);

    /**
     * Defines the conditions for sorting the database records. The return value must be
     * an array of column names indexed by column number. For example:
     *
     * ```
     *     return array(
     *         1 => array(
     *             'Person.Name',
     *             'Person.GivenNames'
     *         )
     *         2 => 'Person.Email'
     *     );
     * ```
     *
     * This method is invoked by the getSortOrder method. The array keys are
     * expected to match the indexes of cou,ns in the table. The array values
     * are expected to be consistent with the Propel query defined in getListQuery.
     *
     * @param Request $request the current request
     * @return Array the search conditions
     * @see DatablesEnabledControllerTrait::getSortOrder()
     */
    abstract protected function getSortColumns(Request $request);

    /**
     * Defines the default sort order to apply to the database records. The method
     * must return an array.
     *
     * @param Request $request the current request
     * @return Array the default sort order
     * @see DatatablesEnabledControllerTrait::getSortOrder()
     * @see DatatablesEnabledControllerTrait::sort()
     */
    abstract protected function getDefaultSortOrder(Request $request);

    /**
     * @inheritDoc
     */
    public function getTotalCount(Request $request)
    {
        $query = $this->getQuery($request);

        return $query->count();
    }

    /**
     * @inheritDoc
     */
    public function getFilteredCount(Request $request)
    {
        $query = $this->getQuery($request);

        $this->search(
            $query,
            $request
        );

        return $query->count();
    }

    /**
     * @inheritDoc
     */
    public function getEntities(Request $request)
    {
        $query = $this->getQuery($request);

        $this->search(
            $query,
            $request
        );

        $this->sort($query, $request);

        $limit = $this->getLimit($request);
        $offset = $this->getOffset($request);

        $entities = $query
            ->setLimit($limit)
            ->setOffset($offset)
            ->find();

        return $this->processEntities($entities, $request);
    }

    /**
     * @inheritdoc
     */
    public function getFilterType(Request $request)
    {
        return;
    }

    /**
     * Extracts the filters from the request's query parameters.
     *
     * @param Request $request the current request
     * @return Array the query parameters containng the filters
     */
    protected function getFilters(Request $request)
    {
        if ($type = $this->getFilterType($request)) {
            return $request->query->get($type->getName());
        }
        return $request->query->get('search')['value'];
    }


    /**
     * Defines the limit for the Propel query.
     *
     * @param Request $request the current request
     * @return int the propel query limit
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
     * Defines the offset for the Propel query.
     *
     * @param Request $request the current request
     * @return int the propel query offset
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
     * @return int 10
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
     * @return int 100
     */
    protected function getMaxLimit(Request $request)
    {
        return 100;
    }

    /**
     * Returns the default offset value.
     *
     * @param Request $request the current request
     * @return int 0;
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
     * @param PropelCollection $entities the result of the propel query
     * @param Request $request the current request
     * @return PropelCollection|Array the processed entities
     */
    protected function processEntities(PropelCollection $entities, Request $request)
    {
        return $entities;
    }

    /**
     * Filters the database records. This method should be considered final for
     * all practical purposes.
     *
     * @param ModelCriteria $query the Propel query
     * @param Request $request the current request
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
     *
     * @param ModelCriteria $query the Propel query
     * @param Request $request the current request
     * @return ModelCriteria the Propel query
     */
    protected function sort(ModelCriteria $query, Request $request)
    {
        $order = $this->getSortOrder($request);

        foreach ($order as $setting) {
            $column = $setting[0];
            $direction = $setting[1];
            $query->orderBy($column, $direction);
        }

        return $query;
    }

    /**
     * Computes the sort order. This method should be considered final for
     * all practical purposes.
     *
     * @param Request $request the current request
     * @return Array the sort order
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
