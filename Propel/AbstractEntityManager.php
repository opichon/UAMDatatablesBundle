<?php

namespace UAM\Bundle\DatatablesBundle\Propel;

use UAM\Bundle\DatatablesBundle\Model\EntityManagerInterface;

abstract class AbstractEntityManager implements EntityManagerInterface
{
    use EntityManagerTrait;
}
