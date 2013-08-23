UAMDatatablesBundle
===================

A symfony 2 bundle that provides a convenient way to include the [dataTables](http://www.datatables.net/) jquery plugin in your symfony 2 apps.

This bundle includes release 1.9.4 of the dataTables plugin. It also includes the plugin's bootstrap-related additions.

Installation
------------

### Step 1: Download UAMDatatablesBundle using composer

Add UAMDatatablesBundle in your composer.json:

```js
{
    "require": {
        "uam/datatables-bundle": "*"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update uam/datatables-bundle
```

Composer will install the bundle to your project's `vendor/uam/datatables-bundle` directory.

If your composer.json does not include the post-install or post-update `installAssets` script handler, then run the following command:

``` bash
$ php app/console assets:install
```

or 

``` bash
$ php app/console assets:install --symlink
```

### Step 2: Enable the bundle

Enable the bundle in the kernel:

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

The dataTables plugin's assets are now available under the `web/bundles/uamdatatables` directory.

Usage
-----

To learn how to use the dataTables jquery plugin itself, see the plugin's home page at [http://www.datatables.net](http://datatables.net).

To use the bundle itself, simply include the bundle's assets in your templates like you would any other bundle.

If you use assetic, you need to declare the UAMDatatablesBundle in your config file's `assetic` section.

Licence
-------

This bundle is licensed under the MIT license.

The dataTables jquery plugin is licensed under the MIT license.

Copyright
---------

This bundle is copyright [United Asian Management Limited](http://www.united-asian-management.com).

The dataTables jquery plugin is copyright [Allan Jardin (www.sprymedia.co.uk)](http://www.sprymedia.co.uk).

All rights reserved by their respective copyright holders.
