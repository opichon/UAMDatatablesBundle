<?php

namespace UAM\Bundle\DatatablesBundle\Model;

use Symfony\Component\HttpFoundation\Request;

interface EntityManagerInterface
{
    public function getTotalCount(Request $request);

    public function getFilteredCount(Request $request);

    public function getEntities(Request $request);

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
     *
     * @return Symfony\Component\Form\FormTypeInterface|null the form type for creating filters
     * @used0-by DatatablesEnabledControllerTrait::getFilter
     */
    public function getFilterType(Request $request);
}
