<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CartProject
 *
 * @ORM\Table(name="cart_project")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CartProjectRepository")
 */
class CartProject
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Cart
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cart" , inversedBy="cartProjects")
     *  @ORM\JoinColumn(name="cart_id", referencedColumnName="id")
     *
     */
    private $cart;

    /**
     * @var \stdClass
     *
     *@ORM\ManyToOne(targetEntity="AppBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @var int
     *
     * @ORM\Column(name="qty", type="integer")
     */
    private $qty;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cart
     *
     * @param \stdClass $cart
     *
     * @return CartProject
     */
    public function setCart($cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * Get cart
     *
     * @return \stdClass
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Set project
     *
     * @param \stdClass $project
     *
     * @return CartProject
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \stdClass
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set qty
     *
     * @param integer $qty
     *
     * @return CartProject
     */
    public function setQty($qty)
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * Get qty
     *
     * @return int
     */
    public function getQty()
    {
        return $this->qty;
    }
}

