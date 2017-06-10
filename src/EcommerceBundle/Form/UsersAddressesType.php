<?php

namespace EcommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsersAddressesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', null, ['label' => 'usersAddresses.firstname'])
            ->add('lastname', null, ['label' => 'usersAddresses.lastname'])
            ->add('phone', null, ['label' => 'usersAddresses.phone'])
            ->add('address', null, ['label' => 'usersAddresses.address'])
            ->add('postalCode', null, ['label' => 'usersAddresses.postalCode'])
            ->add('city', null, ['label' => 'usersAddresses.city'])
            ->add('country', null, ['label' => 'usersAddresses.country'])
            ->add('complement', null, ['label' => 'usersAddresses.complement']);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EcommerceBundle\Entity\UsersAddresses'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ecommercebundle_usersaddresses';
    }


}
