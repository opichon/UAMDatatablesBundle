DatatablesEnabledControllerTrait
================================

Requirements
------------

* Propel

Usage
----

### The controller

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
}
```

### Override the required abstract methods

The `DatatablesEnabledControllerTrait` defines a number of abstract methods, which you must override in your controller:

##### getListQuery

This method should return a propel query object that will be used to retrieve the relevant entities from the database.

##### getSearchColumns

This method should return an array of SQL criteria.

The array keys should be the names of the filter widgets on the page (i.e. either `_search` by default, or the widgets definedin the form type returned by `getFilterType` method).

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

##### getSortColumns

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

##### getDefaultSortOrder

### Methods to be overridden optionnally

The `DatatablesEnabledControllerTrait` defines a number of convenience methods that you can override if required:

##### getFilters

Defines the query parameters that holding the search criteria.

The default value is `_search`, which is the name of the dataTables plugin's default search widget.

##### getDefaultLimit

Defines the default value for the SQL limit. Current value is 10.

##### getMaxLimit

Defines the maximum value for the Saql limit. This is a safety feature ot avid overburdening
the server. Current value is 100.

##### getDefaultOffset

Define the default value for the SQL offset. Current value is 0.

##### processEntities

Provides some optional processingof the entities returned by the Propel query before they are passed to the template.

##### getExtraTemplateParameters 

Allows you to pass some additional parameters to the template in the `index` and `list` actions.

##### getFilterType

Defines the form type to be used to create custom filters on the page. These filters are typically used as per-column filters and displayed in each column header.

If this methods returns a non-null value, a form will be automatically created by the `index` action and passed to the template as a parameter named `filter`. You can then use this parameter in your `index.html.twig` template to render the form's widgets.

By default this methods returns `null` (i.e. no custom filtering).

##### indexAction

Most of the time, you shouldn't have to override this method. Override theother convenience methods instead. 

However, if you like to define your routes via annotations on the controller action method, then you can override this method as follows:

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

##### listAction

Most of the time, you shouldn't have to override this method. Override theother convenience methods instead. 

However, if you like to define your routes via annotations on the controller action method, then you can override this method as follows:

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

Sorting
-------

The dataTables plugin provides support for sorting database records.

Searching
---------

The dataTables plugin provides support for searching database records. 
