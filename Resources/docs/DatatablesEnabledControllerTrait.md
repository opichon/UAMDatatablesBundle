DatatablesEnabledControllerTrait
================================

Requirements
------------

* Propel

Usage
----

* Create an entity manager
* In your controller:
	* Use the `DatatablesEnabledControllerTrait` in your controller
	* Implement the `getEntitymanager` method; it shoudl return ana instance of your entity manager class defined above
* Create your `index.html.twig` template
* Create your `list.json.twig` template
* Create your javascript logic


The entity manager
------------------

The entity manager is reponsible for retrieving records form the database. You can either:

* extend the `UAM\Bundle\DatatablesBundle\Propel\AbstractEntityManager` class, or:
* use the `UAM\Bundle\DatatablesBundle\Propel\EntityManagerTrait` in your own entity manager class.

Naturally, you can define your entity manager instance as a service.

You need to implement the following 4 abstract methods:

* `getQuery`
* `getSearchColumns`
* `getSortColumns`
* `getDefaultSortOrder`

Optionally, if need be, you can override the following methods:

* `getFilters`
* `getDefaultLimit`
* `getMaxLimit`
* `getDefaultOffset`
* `processEntities`
* `getExtraTemplateParameters`
* `getFilterType`

### Abstract methods that need to be implemented

#### getListQuery

This method should return a propel query object that will be used to retrieve the relevant entities from the database.

#### getSearchColumns

This method should return an array of SQL criteria.

If you are using the default searching provided by the dataTables plugin (single search widget), then the array keys are arbitrary.

If you are using per-column or multiple filters, then the array keys should be the names of the filter widgets on the page (i.e. the widgets defined in the form type returned by `getFilterType` method).

The array values should be Propel SQL conditions that are consistent with the Propel query defined in the getListQuery method.

For complex searching, the array value can be an array of conditions. These conditions will be OR'ed.

``` php
return array(
    'name' => array(
       'Person.Surname LIKE "%%%s%%"',
       'Person.GivenNames LIKE "%%%s%%"'
    ),
    'email' => 'Person.Email LIKE "%%%s%%"'
);
```

#### getSortColumns

This method should return an array of sort conditions.

The array keys should be the index of each column on which sorting is enabled. This must be consistent with the `orderable` settings of the dataTables plugin. Note that this method does NOT define the ordering in the dataTables plugin. It merely implements the sorting server-side if sorting is enabled in the dataTables plugin/

The array values should be Propel SQL field names that are consistent with the Propel query defined in the getListQuery method.

For complex sorting, array values can be an array of field names. These sorting criteria will be applied in the order in which they are defined.

``` php
return array(
    1 => array(
        'Person.Surname',
        'Person.GivenNames'
    ),
    2 => 'Person.Email'
);
```

#### getDefaultSortOrder

### Other methods that may be overridden if required

The `DatatablesEnabledControllerTrait` defines a number of convenience methods that you can override if required:

#### getFilters

Defines the query parameters that holding the search criteria.

The default value is `_search`, which is the name of the dataTables plugin's default search widget.

#### getDefaultLimit

Defines the default value for the SQL limit. Current value is 10.

#### getMaxLimit

Defines the maximum value for the SQL limit. This is a safety feature to avoid overburdening
the server. Current value is 100.

#### getDefaultOffset

Define the default value for the SQL offset. Current value is 0.

#### processEntities

Provides some optional processing of the entities returned by the Propel query before they are passed to the template.

#### getExtraTemplateParameters

Allows you to pass some additional parameters to the template in the `index` and `list` actions.

#### getFilterType

Defines the form type to be used to create custom filters on the page. These filters are typically used as per-column filters and displayed in each column header.

If this methods returns a non-null value, a form will be automatically created by the `index` action and passed to the template as a parameter named `filter`. You can then use this parameter in your `index.html.twig` template to render the form's widgets.

By default this methods returns `null` (i.e. no custom filtering).

The controller
--------------

### Use the DatatablesEnabledControllerTrait

Update your controller to use the trait:

``` php
<?php

namespace My\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UAM\Bundle\DatatablesBundle\Controler\UANDatatablesEnabledControllerTrait;

class EntityController extends Controller
{
	use DatatableEnabledControllerTait;

    // ...
}
```

### Implement the `getEntityManager` method

This method should return an instance of the entty manager class you defined earlier. If you;ve defined it as a service, just return the service from the container.

### The `index` action

The `DatatablesEnabledControllerTrait` adds an `index` action to your controller. This action is responsible for displaying the `index.html.twig` template, which contains the table structure (table headers, footers, and empty body).

Most of the time, you shouldn't have to override this method. Override the other convenience methods in the controller and entity manager class instead.

However, if you like to define your routes via annotations in the controller, then you can override the actions as follows (the route name and url are entirely up to you, of course):

``` php

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UAM\Bundle\DatatablesBundle\Controller\DatatablesEnabledControllerTrait;

class EntityController extends Controller
{
    use DatatablesEnabledControllerTrait {
        indexAction as baseIndexAction;
    }

    /**
     * @Route("/entities", name="entities")
     */
    public function indexAction(Request $request)
    {
        return $this->indeAction($request);
    }
}
```

### The `list` action

The `DatatablesEnabledControllerTrait` adds a `list` action to your controller. This action is reponsible for returning the JSON-formatted record data in response to the dataTables plugin's ajax request.

Most of the time, you shouldn't have to override this method. Override the other convenience methods in the controller and entity manager class instead.

However, if you like to define your routes via annotations on the controller action method, then you can override this method as follows (the route name and url are up to you, of course):

``` php

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UAM\Bundle\DatatablesBundle\Controller\DatatablesEnabledControllerTrait;

class EntityController extends Controller
{
    use DatatablesEnabledControllerTrait {
        listAction as baseListAction;
    }

    /**
     * @Route("/entity/list", name="entity_list")
     */
    public function listAction(Request $request)
    {
        return $this->listAction($request);
    }
}
```

The `index.html.twig` template
------------------------------

The `index.html.twig` template should display the table's structure (headers, footers and empty body). It should also include the relevant datatables assets. For convenience, the UAMDatatablesBundle provides some partials that can be included in the `index` template:

``` twig
{# index.html.twig #}

{% include "UAMDatatablesBundle:Datatables:head_style.html.twig" %}

{# template content here #}

{% include "UAMDatatablesBundle:Datatables:foot_script.html.twig" %}
```

The `list.json.twig` template
-----------------------------

The `list.json.twig` template contains the record data in JSON format. It should extend the UAMDatatablesBundle's `list.json.twig` template. You only need to override the `data` block. This block defines the data for each database record. Each row is available as a variable named `entity`.

``` twig
{# list.json.twig #}

{% block data %}
{% spaceless %}
{
	{% set person = entity %}

	"DT_RowId": "{{ 'person_' ~ person.id }}",
	"DT_RowClass": "{{ 'person' }}",

	"id": {{ person.id }},
	"surname": "{{ person.surname }}",
	"given_names": "{{ person.givenNames }}",
	"email": "{{ person.email }}"
}
{% endspaceless %}
{% endblock %}
```

Javascript logic
----------------

To tie everything together, your page needs some javascript logic. By default, this is provided as the `uamdatatables.js` jquery plugin. Include the following snippet in your `index.html.twig` template:

``` javascript
<script>
var uamdatatables: {
	ajax: {
		url: "{{ path('route_to_list_action') }}",
	},
	columnDefs: [
	],
	columns: [
		// your column definitions (see Datatables documentation)
	]
};
</script>
```

Only the `ajax.url` option is required. The `columnDefs` and `columns` options are only required if you return object data in the `list.json.twig` template. Other options supported by the `dataTables` plugin can be included here.

IMPORTANT: You need to add the `uamdatatables` CSS class to a top-level element in your page for the `uamdatatables` jquery plugin to work. This top-level element must be an ancestor of the table used by the `dataTables` plugin.

Alternatively, you can invoke the javascript logic as follows:

``` javascript
<script>
$( document ).ready(function ( e ) {
    $( ".someclass" ).uamdatatables({
        ajax: "{{ path('route_to_list_action') }}",
        // ...
    });
});
</script>
```

Sorting
-------

The enable sorting, simply implement the `getSortColumns` and `getDefaultsortOrder` methods in your entity manager class.

Searching
---------

To enable searching, simply implement the `getSearchColumns` in yhour entity manager class.

To implement per-column filters, you will need to do the following:

* create a form type to define the filters
* implement the `getFilterType` method in your entity manager class to return an isntance of this type.
* update your `index.html.twig` template and render the filter's widgets in the relevant header cells.

The `Datatables EnabledControllerTrait`'s `index` action will automatically include a form based on the filters form type defined above as a parameter named `filter` in the `index.html.twig` template.