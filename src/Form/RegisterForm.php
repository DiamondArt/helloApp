<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterForm extends AbstractType
{

 /**
  * @param string $label
  * @param string placeholder
  * @return array
  */
 private function configForm($label, $placeholder)
 {
    return [
        'label'=>$label,
        'attr' => ['placeholder'=> $placeholder ]];
 }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',TextType::class, $this->configForm("username" ,"give your username to authenticate" ))
            ->add('email',TextType::class, $this->configForm("email", "give your email to confirm subscription"))
            ->add('password',PasswordType::class, $this->configForm("password", "give your password to authenticate"));
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['create'],

        ]);
    }
}
