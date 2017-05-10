<?php

namespace AppBundle\Form;

use AppBundle\AppBundle;
use AppBundle\Entity\Category;
use AppBundle\Repository\CategoryRepository;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ProjectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add('title')
                ->add('description', TextareaType::class)
                ->add('country', ChoiceType::class, [
                    'choices' => array_flip(Intl::getRegionBundle()->getCountryNames())
                ])
               ->add('category', EntityType::class, array(
                   'class' => 'AppBundle:Category',
                   'query_builder' => function (CategoryRepository $er) {
                       return $er->createQueryBuilder('c');
                   },
                   'choice_label' => 'name'
               ))
                ->add('image_form', FileType::class,  [
                    'data_class' => null,
                    'required' => false
                ] )
                ->add('phone', TextType::class)
                ->add('deadline', DateType::class)
                ->add('stock', TextType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Project'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_project';
    }


}
