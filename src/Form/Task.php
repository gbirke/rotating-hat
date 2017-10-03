<?php

namespace Gbirke\TaskHat\Form;

use Gbirke\TaskHat\Duration;
use Gbirke\TaskHat\Recurrence;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Task extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['required' => false,])
            ->add('people', TextareaType::class, [ 'constraints' => [
                new Assert\NotBlank(),
                new Assert\Regex( ['pattern' => '/\w+\r?\n\w+/', 'message' => 'You must specify at least 2 names' ])
            ] ] )
            ->add( 'duration', ChoiceType::class, [
                'choices' => [
                    '1 day' => Duration::Day,
                    '1 week' => Duration::Week,
                    '1 month' => Duration::Month,
                    '1 year' => Duration::Year
                ],
                'constraints' => new Assert\Choice([
                    Duration::Day,
                    Duration::Week,
                    Duration::Month,
                    Duration::Year
                ])
            ] )
            ->add( 'startOn', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Date()
                ]
            ] )
            ->add( 'recurrence', ChoiceType::class, [
                'choices' => [
                    'Once' => Recurrence::ONCE,
                    'Forever' => Recurrence::FOREVER,
                    'Until ...' => Recurrence::UNTIL
                ],
                'constraints' => new Assert\Choice([
                    Recurrence::ONCE,
                    Recurrence::UNTIL,
                    Recurrence::FOREVER
                ] )
            ] )
            ->add( 'endDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'constraints' => [
                    new Assert\Date()
                ],
                'required' => false,
            ] )
            ->add( 'userTimezone', TimezoneType::class )
            ->add( 'timezone', HiddenType::class )
        ;
    }
}