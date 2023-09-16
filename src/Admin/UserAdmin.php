<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class UserAdmin extends AbstractAdmin
{
    /**
     * This method configures which fields are displayed on the edit and create actions. The FormMapper behaves similar to the FormBuilder of the Symfony Form component;
     */
    // protected function configureFormFields(FormMapper $form): void
    // {
    //     $form->add('name', TextType::class);
    // }

    /**
     * This method configures the filters, used to filter and sort the list of models;
     */
    // protected function configureDatagridFilters(DatagridMapper $datagrid): void
    // {
    //     $datagrid->add('name');
    // }

    /**
     *  This method configures which fields are shown when all models are listed (the addIdentifier() method means that this field will link to the show/edit page of this particular model);
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('email');
        // ->addIdentifier('name');
    }

    /**
     * This method configures which fields are displayed on the show action.
     */
    // protected function configureShowFields(ShowMapper $show): void
    // {
    //     $show->add('name');
    // }
}