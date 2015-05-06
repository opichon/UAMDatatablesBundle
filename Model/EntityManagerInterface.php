<?php

namespace UAM\Bundle\DatatablesBundle\Model;

use Symfony\Component\HttpFoundation\Request;

interface EntityManagerInterface
{
    public function getTotalCount(Request $request);
    
    public function getFilteredCount(Request $request);
    
    public function getEntities(Request $request);
}
