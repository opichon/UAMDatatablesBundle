UAMDatatablesBundle
===================

A symfony 2 bundle that provides a convenient way to include the [dataTables](http://www.datatables.net/) jquery plugin in your symfony 2 apps.

This bundle includes release 1.10 of the dataTables plugin. It also includes the plugin's bootstrap-related additions.

Installation
------------

Add the bundle to your project's `composer.json`:

```json
{
    "require": {
        "uam/datatables-bundle": "~2.1",
        ...
    }
}
```

Run `composer install` or `composer update` to install the bundle:

``` bash
$ php composer.phar update uam/datatables-bundle
```


Enable the bundle in the app's kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new UAM\Bundle\DatatablesBundle\UAMDatatablesBundle(),
    );
}
```

If your composer.json does not include the post-install or post-update `installAssets` script handler, then run the following command:

``` bash
$ php app/console assets:install
```

or

``` bash
$ php app/console assets:install --symlink
```

Usage
-----

To learn how to use the dataTables jquery plugin itself, see the plugin's home page at [http://www.datatables.net](http://datatables.net).

To use the bundle itself, simply include the bundle's assets in your templates like you would any other bundle. The dataTables plugin's assets are available under the `web/bundles/uamdatatables` directory.

``` twig
# template.html.twig

{% stylesheets filter="cssrewrite"
    'bundles/uamdatatables/vendor/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css'
	'bundles/uamdatatables/vendor/datatables-plugins/integration/font-awesome/dataTables.fontAwesome.css'
	'bundles/uamdatatables/css/table.css'
%}
	<link href="{{ asset_url }}" type="text/css" rel="stylesheet" media="screen" />
{% endstylesheets %}

{% javascripts
	'bundles/uamdatatables/vendor/datatables/media/js/jquery.dataTables.min.js'
	'bundles/uamdatatables/vendor/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js'
%}
	<script src="{{ asset_url }}"></script>
{% endjavascripts %}
```

If you use assetic, you need to declare the UAMDatatablesBundle in your config file's `assetic` section.

What's included
---------------

The bundle currently includes the following assets:

* jquery dataTables plugin release 1.10.6
* dataTables/bootstrap integration plugin
* dataTables/font-awesome integration plugin

The bundle also includes a [DatatablesEnabledControllerTrait](Resources/docs/DatatablesEnabledContollerTrait.md) and associated assets to help you design pages that list DB-entities using dataTables.

How it works
-------------
The bundle's assets are managed via [bower](http://bower.io/) and [gulp](http://gulpjs.com/).

Adding more assets
------------------
If you need more dataTables assets than are currently included, follow this procedure:

* Fork the bundle
* Customize the bower configuration file (`Resources/config/bower.json`): 
	* Add the assets you require in the `overrides|datatables-plugins|main` section
	* Remove assets you do not require from the `overrides` section
* Run `gulp` from the bundle's `Resources\config` directory

Licence
-------

This bundle is licensed under the MIT license.

The dataTables jquery plugin is licensed under the MIT license.

Copyright
---------

This bundle is copyright [United Asian Management Limited](http://www.united-asian.com).

The dataTables jquery plugin is copyright [Allan Jardine (www.sprymedia.co.uk)](http://www.sprymedia.co.uk).

All rights reserved by their respective copyright holders.
