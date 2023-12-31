<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class UserAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollectionInterface $collection): void 
    {
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('email', TextType::class)
            ->add('block', CheckboxType::class, [
                'attr' => [
                    'checked' => $this->getSubject()->isBlock()
                ],
                'required' => false
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'ROLE_USER' => 'User',
                    'ROLE_ADMIN' => 'Admin',
                ],
                'expanded' => true, // Display roles as checkboxes
                'multiple' => true, // Allow multiple role selection
                'required' => false
            ]);

        if ($this->isCurrentRoute('create')) {
            $form->add('password', PasswordType::class);
        }
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('email', TextType::class)
            ->add('block', FieldDescriptionInterface::TYPE_BOOLEAN);
            // ->add('roles', null, [
            //     'template' => 'YourBundle:Admin:show_roles_as_checkboxes.html.twig', // Specify the template path
            //     'label' => 'Roles',
            //     'roles' => ['ROLE_USER', 'ROLE_ADMIN'], // Provide the available roles
            //     'userRoles' => $this->getSubject()->getRoles(), // Provide the user's roles
            // ]);
    }

    /**
     *  This method configures which fields are shown when all models are listed (the addIdentifier() method means that this field will link to the show/edit page of this particular model);
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('email')
            ->add('block');
    }
}