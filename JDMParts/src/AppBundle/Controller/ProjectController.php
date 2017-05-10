<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Project;
use AppBundle\Entity\Stock;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Project controller.
 *
 * @Route("project")
 */
class ProjectController extends Controller
{
    /**
     * Lists all project entities.
     *
     * @Route("/", name="project_index")
     * @Method("GET")
     *  @throws \LogicException
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $calc = $this->get('price_calculator');

        $max_promotion = $this->get('promotion_manager')->getGeneralPromotion();


        /**
         *@var $knp \Knp\Component\Pager\Paginator
         *
         */
        $knp = $this->get('knp_paginator');
        $projects = $em->getRepository('AppBundle:Project')->findAll();

        $result = $knp->paginate($projects,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',6)
            );

        return $this->render('project/index.html.twig', array(
            'projects'  => $result,
            'max_promotion' => $max_promotion,
            'calc' => $calc,
            'user'      => $this->getUser(),
            'countries' => Intl::getRegionBundle()->getCountryNames(),

        ));
    }

    /**
     * Creates a new project entity.
     *
     * @Route("/new", name="project_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $project = new Project();
        $form    = $this->createForm('AppBundle\Form\ProjectType', $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setDateCreated(new \DateTime());
            $project->setDateUpdated(new \DateTime());
            $project->setPublished(true);
            $project->setUser($this->getUser());
            $stock = new Stock();
            $stock->setProject($project);
            $stock->setCount('100');


            /** @var UploadedFile $file */
            $file = $project->getImageForm();

            if (!$file) {
                $form->get('image_form')->addError(new FormError('Image is required'));
            } else {
                $filename = md5($project->getTitle() . '' . $project->getDateCreated()->format('Y-m-d H:i:s'));

                $file->move(
                    $this->get('kernel')->getRootDir() . '/../web/images/project/',
                    $filename
                );

                $project->setImage($filename);

                $em = $this->getDoctrine()->getManager();




                $em->persist($project);
                $em->persist($stock);

                $em->flush();

                $this->get('session')->getFlashBag()->add('success', 'Project is created successfully!');

                return $this->redirectToRoute('project_show', array('id' => $project->getId()));
            }
        }

        $choices = $this->getDoctrine()->getManager()->getRepository('AppBundle:Category')->findAll();

        return $this->render('project/new.html.twig', array(
            'project' => $project,
            'form'    => $form->createView(),
            'choices' => $choices
        ));
    }

    /**
     * Finds and displays a project entity.
     *
     * @Route("/{id}", name="project_show")
     * @Method("GET")
     */
    public function showAction(Project $project)
    {
        $calc = $this->get('price_calculator');

        $max_promotion = $this->get('promotion_manager')->getGeneralPromotion();

        $deleteForm = $this->createDeleteForm($project);

        return $this->render('project/show.html.twig', array(
            'project'     => $project,
            'max_promotion' => $max_promotion,
            'calc' => $calc,
            'delete_form' => $deleteForm->createView(),
            'countries'   => Intl::getRegionBundle()->getCountryNames()
        ));
    }

    /**
     * Displays a form to edit an existing project entity.
     *
     * @Route("/{id}/edit", name="project_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Project $project)
    {

        if ($project->getUser()->getId() != $this->getUser()->getId() &&
            !$this->isGranted('ROLE_ADMIN', $this->getUser())
        ) {
            $this->get('session')->getFlashBag()->add('error', 'You are not the owner of this project');

            return $this->redirectToRoute('project_index');
        }

        $deleteForm = $this->createDeleteForm($project);
        $editForm   = $this->createForm('AppBundle\Form\ProjectType', $project);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $project->setDateUpdated(new \DateTime());


            if ($project->getImageForm() instanceof UploadedFile) {
                /** @var UploadedFile $file */
                $file = $project->getImageForm();

                $filename = md5($project->getTitle() . '' . $project->getDateCreated()->format('Y-m-d H:i:s'));

                $file->move(
                    $this->get('kernel')->getRootDir() . '/../web/images/project/',
                    $filename
                );

                $project->setImage($filename);
            }


            $this->getDoctrine()->getManager()->flush();


            $this->get('session')->getFlashBag()->add('success', 'Project is edited successfully!');

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/edit.html.twig', array(
            'project'     => $project,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a project entity.
     *
     * @Route("/{id}", name="project_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Project $project)
    {
        if ($project->getUser()->getId() != $this->getUser()->getId() &&
            !$this->isGranted('ROLE_ADMIN', $this->getUser())
        ) {
            $this->get('session')->getFlashBag()->add('error', 'You are not the owner of this project');

            return $this->redirectToRoute('project_index');
        }
        $form = $this->createDeleteForm($project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($project);
            $em->flush();
        }

        return $this->redirectToRoute('project_index');
    }

    /**
     * Creates a form to delete a project entity.
     *
     * @param Project $project The project entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Project $project)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('project_delete', array('id' => $project->getId())))
                    ->setMethod('DELETE')
                    ->getForm();
    }


    /**
     * @Route("/manage/projects", name="admin_manage_projects")
     */
    public function manageProjectsAction()
    {

        $em = $this->getDoctrine()->getManager();

        $projects = $em->getRepository('AppBundle:Project')->findAll();

        return $this->render('project/manage.html.twig', array(
            'projects'  => $projects,
            'user'      => $this->getUser(),
            'countries' => Intl::getRegionBundle()->getCountryNames(),

        ));
    }


    /**
     * Deletes a project entity.
     *
     * @Route("/manage/projects/{id}/delete", name="admin_project_delete")
     * @Method("GET")
     */
    public function manageDeleteAction(Request $request, Project $project)
    {


        $em = $this->getDoctrine()->getManager();
        $em->remove($project);
        $em->flush();


        return $this->redirectToRoute('admin_manage_projects');
    }
}
