<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Employee;
use AppBundle\Entity\Repository\EmployeeRepository;
use AppBundle\Form\Type\EmployeeType;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class EmployeeController
 * @package AppBundle\Controller
 *
 * @RouteResource("Employee")
 */
class EmployeeController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets an individual Employee
     *
     * @param int $id
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\Employee",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function getAction($id)
    {
        $employee= $this->getEmployeeRepository()->createFindOneByIdQuery($id)->getOneOrNullResult();
        if ($employee === null) {
            return new Response(sprintf('Dont exist employee with id %s', $id));
        }
        return $employee;
    }

    /**
     * Gets a collection of Employees (for use filter(name, surname, age): /employees?filter= )
     *
     * @return array
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\Employee",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     *
     * @Route("/", name="index")
     */

    public function cgetAction(Request $request)
    {
//        $em = $this->getDoctrine()->getManager();
//        $repo = $em->getRepository('AppBundle:Employee');
//        $options = array(
//            'decorate' => true,
//            'rootOpen' => '<ul>',
//            'rootClose' => '</ul>',
//            'childOpen' => '<li>',
//            'childClose' => '</li>'
//        );
//        $employees = $repo->childrenHierarchy(
//            null, /* starting from root nodes */
//            true, /* false: load all children, true: only direct */
//            $options,
//            true,//include node
//            false
//        );
//        return $this->render('Employee/index.html.twig', array('employees' => $employees));
        $queryBuilder = $this->getEmployeeRepository()->searchQuery();
        if ($request->query->getAlnum('filter')) {
            $queryBuilder->where('e.name LIKE :name')
                ->orwhere('e.surname LIKE :name')
                ->orwhere('e.salary LIKE :name')
                ->setParameter('name', '%' . $request->query->getAlnum('filter') . '%');
        }
        $query = $queryBuilder->getQuery();
        $paginator  = $this->get('knp_paginator');
        $employees = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 100)
        );
        return $this->render('Employee/index.html.twig', [
            'employees' => $employees,
        ]);
    }

    /**
     * Add a new employee
     * @param Request $request
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\Employee",
     *     statusCodes={
     *         201 = "Returned when a new employee has been successful created",
     *         404 = "Return when not found"
     *     }
     * )
     * * @Route("/loco", name="loco")
     */
    public function postAction(Request $request)
    {

    }

    /**
     * Totally update employee
     * @param Request $request
     * @param int     $id
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\EmployeeType",
     *     output="AppBundle\Entity\Employee",
     *     statusCodes={
     *         204 = "Returned when an existing Employee has been successful updated",
     *         400 = "Return when errors",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function putAction(Request $request, $id)
    {

        $employee = $this->getEmployeeRepository()->find($id);

        if ($employee === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(EmployeeType::class, $employee, [
            'csrf_protection' => false,]);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $routeOptions = [
            'id' => $employee->getId(),
            '_format' => $request->get('_format'),
        ];

        $id=$employee->getId();
        $this->routeRedirectView('', $routeOptions, Response::HTTP_OK);
        return $this->getEmployeeRepository()->createFindOneByIdQuery($id)->getOneOrNullResult();


        return $this->routeRedirectView('', $routeOptions, Response::HTTP_OK);

    }


    /**
     * Update employee
     * @param Request $request
     * @param int     $id
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\EmployeeType",
     *     output="AppBundle\Entity\Employee",
     *     statusCodes={
     *         204 = "Returned when an existing employee has been successful updated",
     *         400 = "Return when errors",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function patchAction(Request $request, $id)
    {
        /**
         * @var $employee Employee
         */
        $employee = $this->getEmployeeRepository()->find($id);
        if ($employee === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(EmployeeType::class, $employee, [
            'csrf_protection' => false,
        ]);
        $form->submit($request->request->all(), false);
        if (!$form->isValid()) {
            return $form;
        }
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        $routeOptions = [
            'id' => $employee->getId(),
            '_format' => $request->get('_format'),
        ];

        $id=$employee->getId();
        $this->routeRedirectView('', $routeOptions, Response::HTTP_NO_CONTENT);
        return $this->getEmployeeRepository()->createFindOneByIdQuery($id)->getOneOrNullResult();

        return $this->routeRedirectView('', $routeOptions, Response::HTTP_NO_CONTENT);

    }


    /**
     * Delete employee
     * @param int $id
     * @return View
     *
     * @ApiDoc(
     *     statusCodes={
     *         204 = "Returned when an existing Employee has been successful deleted",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function deleteAction($id)
    {
        $employee = $this->getEmployeeRepository()->deleteQuery($id)->getResult();
        if ($employee == 0) {
            return new View("Doent exist $id");
        }
        return new View("Deleted user $id");
    }

    /**
     * @return EmployeeRepository
     */
    private function getEmployeeRepository()
    {
        return $this->get('crv.doctrine_entity_repository.employee');
    }
}
