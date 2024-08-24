<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;

class FacebookEventType extends EventType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Start Date',
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ->add('startTime', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Start Time',
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ->add('description', TextAreaType::class, [
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ->add('location', TextType::class, [
                'attr' => [
                'readonly' => true,
                ],
            ])
            ->add('facebookEventId', HiddenType::class)
        ;
    }

}
