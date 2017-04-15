<?php
namespace EcommerceBundle\Twig\Extension;

class TaxeExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(new \Twig_SimpleFilter('taxe', array($this, 'applyTaxe')));
    }
    
    function applyTaxe($priceHT, $taxe)
    {
        return round($priceHT * $taxe, 2);
    }
    
    public function getName()
    {
        return 'taxe_extension';
    }
            
}

