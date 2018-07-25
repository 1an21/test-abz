<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('surname', TextType::class )
            ->add('name', TextType::class)
            ->add('salary', MoneyType::class)
            ->add('position', TextType::class)
            ->add('employmentDate', DateType::class)
            ->add('image', FileType::class, array('label' => 'Image', 'data_class'=>null))
            ->add('parent', EntityType::class, array('label' => 'Boss','class' => 'AppBundle:Employee', 'empty_data'  => null, 'placeholder' => 'Im boss', 'required' => false, 'choice_label' => 'surname'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Employee',
            'allow_extra_fields' => true,
        ]);
    }
    public function getName()
    {
        return 'employee';
    }

}