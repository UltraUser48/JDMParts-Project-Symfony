<?php

namespace AppBundle\Service;

use AppBundle\Entity\Project;
use Doctrine\ORM\EntityManager;
use AppBundle\Repository\PromotionRepository;

class PriceCalculator
{
    /** @var  PromotionManager */
    protected $manager;

    public function __construct(PromotionManager $manager) {
        $this->manager= $manager;
    }


    /**
     * @param Project $project
     *
     * @return float
     */
    public function calculate($project)
    {
        $category    = $project->getCategory();
        $category_id = $category->getId();

        $promotion = $this->manager->getGeneralPromotion();

        if($this->manager->hasCategoryPromotion($category)){
            $promotion = $this->manager->getCategoryPromotion($category);
        }


        return $project->getPrice() - $project->getPrice() * ($promotion / 100);
    }
}