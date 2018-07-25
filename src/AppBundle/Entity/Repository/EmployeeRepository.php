<?php

namespace AppBundle\Entity\Repository;

class EmployeeRepository extends \Doctrine\ORM\EntityRepository
{
    public function searchQuery()
    {
        return $this->_em->getRepository('AppBundle:Employee')->createQueryBuilder('e');
    }

}
