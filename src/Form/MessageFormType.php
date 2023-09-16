<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $dateTime = new \DateTimeImmutable();

        // float: right;
        // margin-right: 20px;
        // margin-bottom: 10px;

        $builder
        ->add('username')
        ->add('email', EmailType::class)
        ->add('homepage')
        ->add('text', TextareaType::class)
        ->add('created_at', HiddenType::class, [
            'data' => $dateTime->format('Y-m-d H:i:s')
        ]);
        // ->add('save', SubmitType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 20px')));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}