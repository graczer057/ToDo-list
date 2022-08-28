<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Todo;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EditTaskType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('description', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Opis'
            ])
            ->add('priority', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Priorytet',
                'choices' => [
                    'Niski' => 1,
                    'Normalny' => 2,
                    'Pilny' => 3
                ]
            ])
            ->add('date', DateTimeType::class, [
                'attr' => [
                    'class' => 'js-datepicker'
                ],
                'widget' => 'single_text',
                'html5' => 'false',
                'placeholder' => [
                    'year' => 'Rok', 'month' => 'Miesiąc', 'day' => 'Dzień', 'hour' => 'Godzina', 'minute' => 'Minuta',
                ],
                'label' => 'Data'
            ])
            ->add('isDone', CheckboxType::class, [
                'required' => false,
                'label' => 'isDone'
            ])
            ->add('category', EntityType::class, [
                'class'=>Category::class,
                'query_builder' => function (EntityRepository
                                             $er){
                    return $er->createQueryBuilder('u');
                },
                'multiple' => false,
                'expanded' => false,
                'choice_label' => function(Category $category){
                    return $category->getCategory();
                },
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Kategorie',
            ])
            ->add('btn_submit', SubmitType::class, [
                'label' => "Zapisz",
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }
}
