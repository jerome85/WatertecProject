<?php

namespace EcommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class MediaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, ['attr' => ['class' => 'sm-form-control'], 
                                 'label' => 'media.new.name'])
            ->add('path', null, ['attr' => ['class' => 'sm-form-control'], 
                                 'label' => 'media.new.path'])
            ->add('product', null, ['attr' => ['class' => 'sm-form-control'], 
                                 'label' => 'media.new.product']);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EcommerceBundle\Entity\Media',
            'translation_domain' => 'admin'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ecommercebundle_media';
    }


}
