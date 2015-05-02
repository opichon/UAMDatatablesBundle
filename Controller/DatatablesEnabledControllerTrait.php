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

        if ($filter_type = $this->getFilterType()) {
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

    abstract protected function getFilters(Request $request);

    abstract protected function getSearchColumns(Request $request);

    abstract protected function getSortColumns(Request $request);

    abstract protected function getDefaultSortOrder(Request $request);

    protected function getFilterType()
    {
        return;
    }

    protected function getLimit(Request $request)
    {
        return min(100, $request->query->get('length', $this->getDefaultLimit()));
    }

    protected function getOffset(Request $request)
    {
        return max($request->query->get('start', 0), $this->getDefaultOffset());
    }

    protected function getDefaultLimit()
    {
        return 10;
    }

    protected function getDefaultOffset()
    {
        return 0;
    }

    protected function processEntities(PropelCollection $entities, Request $request)
    {
        return $entities;
    }

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
