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
use Symfony\Component\HttpFoundation\File\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
/**
 * Class EmployeeController
 * @package AppBundle\Controller
 *
 * @RouteResource("Employee")
 */
class EmployeeController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets a tree of Employees
     *
     * @return array
     *
     * @ApiDoc(
     *     section="Employee",
     *     output="AppBundle\Entity\Employee",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     *
     * @Route("/", name="index")
     * @Method("GET")
     */

    public function cgetAction(Request $request)
    {
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
     * Gets a collection of Employees
     *
     * @return array
     *
     * @ApiDoc(
     *     section="Employee",
     *     output="AppBundle\Entity\Employee",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     *
     * @Route("/all", name="all")
     * @Method("GET")
     */

    public function allGetAction(Request $request)
    {

        $queryBuilder = $this->getEmployeeRepository()->searchQuery();
        if ($request->query->getAlnum('filter')) {
            $queryBuilder->where('e.name LIKE :name')
                ->orwhere('e.surname LIKE :name')
                ->orwhere('e.salary LIKE :name')
                ->orwhere('e.position LIKE :name')
                ->orwhere('e.employmentDate LIKE :name')
                ->setParameter('name', '%' . $request->query->getAlnum('filter') . '%');
        }
        $query = $queryBuilder->getQuery();
        $paginator  = $this->get('knp_paginator');
        $employees = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );
        return $this->render('Employee/all.html.twig', [
            'employees' => $employees,
        ]);
    }
    /**
     * Add a new employee
     * @param Request $request
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     section="Employee",
     *     output="AppBundle\Entity\Employee",
     *     statusCodes={
     *         201 = "Returned when a new employee has been successful created",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("/create", name="create")
     * @Method({"POST", "GET"})
     */
    public function postAction(Request $request)
    {
        $form = $this->createForm(EmployeeType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $employee = $form->getData();
            $file = $employee->getImage();
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

            $file->move(
                $this->getParameter('image_directory'),
                $fileName
            );

            $employee->setImage($fileName);

            $em->persist($employee);
            $em->flush();
            return $this->redirectToRoute('all');
        }
        return $this->render('Employee/create.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * Update employee
     * @param Request $request
     * @param Employee $employee
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     section="Employee",
     *     input="AppBundle\Form\Type\EmployeeType",
     *     output="AppBundle\Entity\Employee",
     *     statusCodes={
     *         204 = "Returned when an existing employee has been successful updated",
     *         400 = "Return when errors",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("/edit/{id}", name="edit")
     * @Method({"POST","GET"})
     */
    public function patchAction(Request $request, Employee $employee)
    {
        if($employee->getImage()!=null)
            $employee->setImage(new File($this->getParameter('image_directory').'/'.$employee->getImage()));

        $form = $this->createForm('AppBundle\Form\Type\EmployeeType', $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $employee->getImage();
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();


            $file->move(
                $this->getParameter('image_directory'),
                $fileName
            );
            $employee->setImage($fileName);


            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('all', array('id' => $employee->getId()));
        }
        return $this->render('Employee/edit.html.twig', array(
            'employee' => $employee,
            'form' => $form->createView()
        ));

    }

    /**
     * Delete employee
     * @param int $employee
     * @return View
     *
     * @ApiDoc(
     *     section="Employee",
     *     statusCodes={
     *         204 = "Returned when an existing Employee has been successful deleted",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("/delete/{employee}", name="delete")
     * @Method({"DELETE", "GET"})
     */
    public function deleteAction($employee)
    {
        if ($employee === null) {
            return $this->redirectToRoute('all');
        }
        $em = $this->getDoctrine()->getManager();
        $delEmployee = $em->getRepository('AppBundle:Employee')->findOneById($employee);
        if (!$delEmployee) {
            throw $this->createNotFoundException('No livre found for id '.$employee);
        }
        $em->remove($delEmployee);
        $em->flush();
        return $this->redirectToRoute('all');
    }

    /**
     * @return EmployeeRepository
     */
    private function getEmployeeRepository()
    {
        return $this->get('crv.doctrine_entity_repository.employee');
    }
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }
}
