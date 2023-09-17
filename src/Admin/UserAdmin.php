<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\Form\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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

    // protected function configureBatchActions(array $actions): array
    // {
    //     // $actions = parent::getBatchActions();

    //     $actions['block'] = [
    //         'label' => 'Change block',
    //         'ask_confirmation' => false, // You can set this to true if you want to confirm before making changes
    //     ];

    //     return $actions;
    // }

    // public function batchActionChangeFieldValue(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    // {
    //     // Get the value to set from the request (e.g., 'new_value' parameter)
    //     $block = $request->request->get('block');

    //     // Iterate through the selected entities and update the field value
    //     foreach ($selectedModelQuery->execute() as $entity) {
    //         $entity->setYourField($block); // Change 'YourField' to the actual field name you want to update
    //     }

    //     // Save the changes
    //     $this->getModelManager()->flush();

    //     $this->addFlash('sonata_flash_success', 'Field value changed successfully.');

    //     // Redirect to the list view
    //     return $this->redirectToList();
    // }

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
            //     'template' => 'templates:UserAdmin:show_roles.html.twig'
            // ]); //TODO
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

    /**
     * This method configures which fields are displayed on the show action.
     */
    // protected function configureShowFields(ShowMapper $show): void
    // {
    //     $show->add('name');
    // }
}