<?php
namespace App\Form\Type;

use App\Entity\Credentials;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CredentialsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('login');
        $builder->add('password');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => [
                Credentials::class,
                'determineValidationGroups',
            ],
            'data_class' => 'App\Entity\Credentials',
            'csrf_protection' => false
        ]);
    }
}
