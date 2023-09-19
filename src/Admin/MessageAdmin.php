<?php

namespace App\Admin;

use DateTimeImmutable;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

final class MessageAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollectionInterface $collection): void 
    {
        $collection->remove('create');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('username', TextType::class)
            ->add('email', TextType::class)
            ->add('homepage', UrlType::class, [
                'required' => false
            ])
            ->add('text', TextType::class)
            ->add('coordination', CheckboxType::class, [
                'required' => false
            ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('username', TextType::class)
            ->add('email', TextType::class)
            ->add('homepage', UrlType::class)
            ->add('text', TextType::class)
            ->add('coordination', CheckboxType::class);
            // ->add('created_at', TextType::class);
    }

    /**
     *  This method configures which fields are shown when all models are listed (the addIdentifier() method means that this field will link to the show/edit page of this particular model);
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add('email')
            ->add('created_at')
            ->add('coordination');
    }
}