<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Intl;


class CategoryController extends Controller
{
    /**
     *
     * @Route("/category/{category}", name="category_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function CategoryAction($category,Request $request){

        $qb=$this->getDoctrine()->getManager()->getRepository("AppBundle:Project")->createQueryBuilder('p');


        $cat =$this->getDoctrine()->getManager()->getRepository('AppBundle:Category')->find($category);

        $calc = $this->get('price_calculator');

        $max_promotion = $this->get('promotion_manager')->getGeneralPromotion();


        $qb->select('p')
            ->where($qb->expr()->eq('p.category', ':category'))
            ->setParameters(['category' => $cat]);


        /**
         *@var $knp \Knp\Component\Pager\Paginator
         *
         */
        $knp = $this->get('knp_paginator');
        $resultprojects = $qb->getQuery()->getResult();

        $result = $knp->paginate($resultprojects,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',6)
        );
dump($resultprojects);

       return $this->render('categories/show.html.twig', array(
           'projects'=> $result,
           'max_promotion' => $max_promotion,
           'calc' => $calc,
            'user'      => $this->getUser(),
           'countries' => Intl::getRegionBundle()->getCountryNames(),

        ));
    }
    /**
     *
     * @Route("/category", name="category_show")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function CategoryShow(){




        return $this->render('categories/view.html.twig', array(
            'user'      => $this->getUser(),
            'countries' => Intl::getRegionBundle()->getCountryNames(),

        ));
    }
}

