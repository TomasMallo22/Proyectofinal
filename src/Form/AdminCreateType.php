<?php

namespace App\Form;

use App\Entity\Admin;
use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AdminCreateType extends AbstractType
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $entity = $builder->getData();

        $rolesChoices = [];
        $roles = $this->entityManager->getRepository(Role::class)->findAll();
        foreach ($roles as $rol) {
            $rolesChoices[$rol->__toString()] = $rol->getId();
        }

        //traemos la funcion rol object para saber que tiene asignado el usuario
        $rolesIds = [];
        foreach ($entity->getRolesObjects() as $role) {
            $rolesIds[] = $role->getId();
        }

        if(!empty($entity->getId())){
            $builder->add('username', TextType::class, [
                'attr' => ['readonly' => true],
            ]);
        }else{
            $builder->add('username');
        }

        $builder
            ->add('roles_in_form', ChoiceType::class,[
                 'choices' => $rolesChoices,
                 'expanded' => true,
                 'multiple' => true,
                 'mapped' => false,
                 'data' => $entity->getRoles()
             ])
            ->add('password', TextType::class,[
                'mapped' => false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Admin::class,
        ]);
    }
}
