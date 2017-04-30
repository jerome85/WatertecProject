<?php

namespace EcommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sku', null, ['attr' => ['class' => 'sm-form-control'], 
                                 'label' => 'products.new.sku'])
            ->add('name', null, ['attr' => ['class' => 'sm-form-control', 'maxlength' => '26'], 
                                 'label' => 'products.new.name'])
            ->add('description', null, ['attr' => ['class' => 'ckeditor'], 
                                        'label' => 'products.new.description'])
            ->add('price', null, ['attr' => ['class' => 'sm-form-control'], 
                                  'label' => 'products.new.price'])
            ->add('available', null, ['attr' => ['class' => 'checkbox'], 
                                      'label' => 'products.new.available'])
            ->add('category', null, ['attr' => ['class' => 'sm-form-control'], 
                                     'label' => 'products.new.category']);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EcommerceBundle\Entity\Products',
            'translation_domain' => 'admin'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ecommercebundle_products';
    }


}
