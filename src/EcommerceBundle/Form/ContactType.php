<?php

namespace EcommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ContactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'contact.name'])
            ->add('email', EmailType::class, ['label' => 'contact.email'])
            ->add('phone', TextType::class, ['label' => 'contact.phone'])
            ->add('services', ChoiceType::class, ['choices' => ['contact.technical' => '1',
                                                                'contact.sac' => '2',
                                                                'contact.support' => '3',
                                                                'contact.financial' => '4'],
                                                   'label' => 'contact.services'])
            ->add('subject', TextType::class, ['label' => 'contact.subject'])
            ->add('message', TextareaType::class, ['label' => 'contact.message']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ecommercebundle_contact';
    }


}
