<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Intl\Intl;

class UsersController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));


    }


    /**
     * Lists all project entities.
     *
     * @Route("/user", name="user_profile")
     * @Method("GET")
     */
    public function profileAction()
    {
        $em = $this->getDoctrine()->getManager();

        //$user = $em->getRepository('AppBundle:User')->findBy();

        return $this->render('user/profile.html.twig', array(
            'user'      => $this->getUser(),
            'countries' => Intl::getRegionBundle()->getCountryNames(),
        ));
    }


    /**
     * Lists all project entities.
     *
     * @Route("/admin/users", name="admin_users")
     * @Method("GET")
     */
    public function AdminAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();


        return $this->render('user/adminview.html.twig', array(
            'user'      => $this->getUser(),
            'countries' => Intl::getRegionBundle()->getCountryNames(),
            'users' =>  $users,
        ));
    }

}
