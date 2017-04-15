<?php

namespace EcommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EcommerceBundle\Form\SearchType;
use Symfony\Component\HttpFoundation\Request;


class ProductsController extends Controller
{   
    public function productsAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        
        if($request->getMethod() == 'POST')
        {
            $form = $this->createForm('EcommerceBundle\Form\SearchType');
            $form->handleRequest($request);
            $products = $em->getRepository('EcommerceBundle:Products')->search($form['search']->getData());
        } else {
            $products = $em->getRepository('EcommerceBundle:Products')->findBy(array('available' => 1)); 
        }
        
        if($session->has('cart')) 
            $cart = $session->get('cart');
        else
            $cart = false;
        
        return $this->render('EcommerceBundle:Default:Products/layout/products.html.twig', array('products' => $products,
                                                                                                 'cart' => $cart));
    }
    
    public function quickviewAction($id, Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('EcommerceBundle:Products')->find($id);
        
        if(!$product) throw $this->createNotFoundException('La page n\'existe pas');
        
        if($session->has('cart')) 
            $cart = $session->get('cart');
        else
            $cart = false;
        
        return $this->render('EcommerceBundle:Default:Products/layout/quickview.html.twig', array('product' => $product,
                                                                                                  'cart' => $cart));
    }
    
    public function searchAction(Request $request)
    {
        $form = $this->createForm('EcommerceBundle\Form\SearchType');
        $form->handleRequest($request);
        return $this->render('EcommerceBundle:Default:Search/moduleUsed/search.html.twig', array('form' => $form->createView()));
        
    }

}
