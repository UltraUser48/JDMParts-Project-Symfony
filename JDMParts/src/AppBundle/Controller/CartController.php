<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;

use AppBundle\Entity\CartProject;
use AppBundle\Entity\Project;
use Doctrine\ORM\Mapping\Id;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Test\Fixture\Entity\Shop\Product;

class CartController extends Controller
{


    /**
     * @Route("/cart/add", name="cart_add")
     */
    public function addAction(Request $request)
    {

        $manager = $this->getDoctrine()->getManager();
        $session = $this->get('session');

        $cart = $this->getDoctrine()->getRepository('AppBundle:Cart')->find($session->get('id_cart', false));

        $id_cart = $session->get('id_cart', false);

        if (!$id_cart || $cart->isBought()) {
            $cart = new Cart();
            $cart->setUserid($this->getUser());
            $cart->setDateCreated(new \DateTime());
            $cart->setDateUpdated(new \DateTime());

            $manager->persist($cart);
            $manager->flush();

            $session->set('id_cart', $cart->getId());
        }

        $cart = $this->getDoctrine()->getRepository('AppBundle:Cart')->find($session->get('id_cart', false));

        $products = $request->get('projects');


        foreach ($products as $id_project) {
            $project = $this->getDoctrine()->getRepository('AppBundle:Project')->find($id_project);

            if ($project) {


                $cp = $this->getDoctrine()->getRepository('AppBundle:CartProject')->findOneBy([
                    'cart' => $cart,
                    'project' => $project
                ]);

                if (!$cp) {
                    $cp = new CartProject();
                    $cp->setCart($cart);
                    $cp->setProject($project);
                    $cp->setQty(1);
                } else {
                    $cp->setQty($cp->getQty() + 1);
                }


                $manager->persist($cp);
            }
        }

        $cart->setDateUpdated(new \DateTime());

        $manager->persist($cart);

        $manager->flush();


        return $this->redirectToRoute('cart_list');
    }


    /**
     * @Route("/cart/checkout", name="cart_checkout")
     * @Template()
     *
     */
    public function CheckoutAction(Request $request)
    {

        $manager = $this->getDoctrine()->getManager();
        $session = $this->get('session');


        $id_cart = $session->get('id_cart', false);


        $cart = $this->getDoctrine()->getRepository('AppBundle:Cart')->find($session->get('id_cart', false));

      $cart->setBought(true);

        $cart->setDateUpdated(new \DateTime());

        $manager->persist($cart);

        $manager->flush();


       // return $this->redirectToRoute('cart_checkout');
    }




    /**
     * @Route("/cart/list", name="cart_list")
     * @Template()
     *
     */
    public function listAction(Request $request)
    {
        $session = $this->get('session');
        $cart = $this->getDoctrine()->getRepository('AppBundle:Cart')->find($session->get('id_cart', false));
        $cartprojects = $this->getDoctrine()->getRepository('AppBundle:CartProject')->findBy(array('cart' => $cart));

        for ($i = 0; $i < count($cartprojects); $i++) {
            echo $cartprojects[$i]->getQty();
        }



        $calc = $this->get('price_calculator');


        $max_promotion = $this->get('promotion_manager')->getGeneralPromotion();


        if($cart->isBought()){
            return $this->render("cart/empy.html.twig" ) ;

        }

        if(!$cart && $cart->isBought()){
           return $this->render("cart/empy.html.twig" ) ;

        }


        return [
            'cartprojects'=>$cartprojects,
            'cart' => $cart,
            'max_promotion' => $max_promotion,
            'calc' => $calc
        ];
    }


    /**
     * @Route("/cart/bought", name="cart_bought")
     * @Template()
     *
     */
    public function BoughtlistAction(Request $request)
    {
        $cart = $this->getDoctrine()->getRepository('AppBundle:Cart')->findBy(array('bought'=> true));





        return [
            'cart' => $cart,

        ];
    }


    /**
     * @Route("/cart/remove", name="cart_bought")
     * @Template()
     *
     */
    public function RemoveAction(Request $request)
    {
        $session = $this->get('session');

        $cart = $this->getDoctrine()->getRepository('AppBundle:Cart')->find($session->get('id_cart', false));

        $products = $request->get('projects');

        foreach ($products as $id_project) {

            $project = $this->getDoctrine()->getRepository('AppBundle:Project')->find($id_project);
            $cart = $this->getDoctrine()->getRepository('AppBundle:Cart')->find($session->get('id_cart', false));

            $cp = $this->getDoctrine()->getRepository('AppBundle:CartProject')->findOneBy([
                'cart' => $cart,
                'project' => $project
            ]);

            dump($cp);
        }




        return [
            'cart' => $cart,

        ];
    }

}