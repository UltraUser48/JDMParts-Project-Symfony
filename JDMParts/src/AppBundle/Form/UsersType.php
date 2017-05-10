<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UsersType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class, [
            'required' =>  false,


        ])
                ->add('email', TextType::class,[
                'required' =>  false,



        ])
            ->add('phone', TextType::class,[
                'required' =>  false,



            ])
            ->add('country', ChoiceType::class, [
                'choices' => array_flip(Intl::getRegionBundle()->getCountryNames())
            ])
            ->add('email', TextType::class,[
                'required' =>  false,



            ])
                ->add('password_raw', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'required' =>  false,
                        'label'=> 'Password',

                    ],
                    'second_options' =>[
                        'required' =>  false,
                        'label'=> 'Repeat Password'

                    ]
                ])
        ->add('submit', SubmitType::class) ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' =>  User::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }


}
