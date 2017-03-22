<?php

/**
 * @copyright 2015 United Asian Management Limited. All rights reserved
 * @license MIT
 */

namespace UAM\Bundle\DatatablesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Provides convenience methods for implementing an index page listing database
 * records as entities using the dataTables jquery plugin with server-side data.
 *
 * @Template()
 */
class DatatablesController extends Controller
{
    use DatatablesControllerTrait;
}
