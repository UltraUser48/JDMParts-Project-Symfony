<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CartProject;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Cartproject controller.
 *
 * @Route("cartproject")
 */
class CartProjectController extends Controller
{
    /**
     * Lists all cartProject entities.
     *
     * @Route("/view", name="cartproject_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cartProjects = $em->getRepository('AppBundle:CartProject')->findAll();

        return $this->render('cartproject/index.html.twig', array(
            'cartProjects' => $cartProjects,
        ));
    }



    /**
     * Finds and displays a cartProject entity.
     *
     * @Route("/{id}", name="cartproject_show")
     * @Method("GET")
     */
    public function showAction(CartProject $cartProject)
    {
        $deleteForm = $this->createDeleteForm($cartProject);

        return $this->render('cartproject/show.html.twig', array(
            'cartProject' => $cartProject,
            'delete_form' => $deleteForm->createView(),
        ));
    }



    /**
     * Deletes a cartProject entity.
     *
     * @Route("/{id}", name="cartproject_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, CartProject $cartProject)
    {
        $form = $this->createDeleteForm($cartProject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cartProject);
            $em->flush();
        }

        return $this->redirectToRoute('cart_list');
    }

    /**
     * Creates a form to delete a cartProject entity.
     *
     * @param CartProject $cartProject The cartProject entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CartProject $cartProject)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cartproject_delete', array('id' => $cartProject->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
