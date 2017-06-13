<?php

namespace EcommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use EcommerceBundle\Form\UsersAddressesType;
use EcommerceBundle\Entity\UsersAddresses;
use EcommerceBundle\Services\Paypal;

class CartController extends Controller
{
    public function menuAction(Request $request)
    {
        $session = $request->getSession();
        
        if(!$session->has('cart')){
            $total = 0;
            $cart = null;
        }    
        else{
            $total = count($session->get('cart'));
            $cart = $session->get('cart');
        }
        
        if(!$session->has('cart')) $session->set('cart', array());
        
        $array = array_keys($session->get('cart'));
        
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('EcommerceBundle:Products')->findArray($array);
        
        return $this->render('EcommerceBundle:Default:Cart/moduleUsed/cart.html.twig', array('total' => $total,
                                                                                             'products' => $products,
                                                                                             'cart' => $session->get('cart')));
    }
    
    public function cartAction(Request $request)
    {
        $session = $request->getSession();
        if(!$session->has('cart')) $session->set('cart', array());
        
        $array = array_keys($session->get('cart'));
        
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('EcommerceBundle:Products')->findArray($array);
        
        return $this->render('EcommerceBundle:Default:Cart/layout/cart.html.twig', array('products' => $products,
                                                                                         'cart' => $session->get('cart')
                                                                                        ));
    }
    
    public function shippingAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        // the above is a shortcut for this
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $entity = new UsersAddresses();
        $form = $this->createForm('EcommerceBundle\Form\UsersAddressesType', $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setUser($user);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Adresse ajoutée avec succès');

            return $this->redirect($this->generateUrl('shipping'));
        }
        
        
        return $this->render('EcommerceBundle:Default:Cart/layout/shipping.html.twig', array('user' => $user,
                                                                                             'form' => $form->createView()));
    }
    
    public function removeAddressAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EcommerceBundle:UsersAddresses')->find($id);
        if ($this->get('security.token_storage')->getToken()->getUser() != $entity->getUser() || !$entity) 
        {
            return $this->redirect($this->generateUrl('shipping'));
        }

        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('shipping.addressRemoveSuccess'));
        return $this->redirect($this->generateUrl('shipping'));
    }
   
    
    public function addAction($id, Request $request)
    {
        $session = $request->getSession();
        if(!$session->has('cart')) $session->set('cart', array());
        $cart = $session->get('cart');
        if(array_key_exists($id, $cart)){
            if($request->query->get('qty') != null){
                $cart[$id] = (int)$request->query->get('qty');
            }
            $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('cart.add.qtySuccess'));
        } 
        else{
            if($request->query->get('qty') != null){
                $cart[$id] = (int)$request->query->get('qty');
            }
            else{
                $cart[$id] = 1;
            }
            $this->get('session')->getFlashBag()->add('success', 'Article ajouté avec succès');
        } 
        $session->set('cart', $cart); 
        return $this->redirect($this->generateUrl('cart'));
    }
    
    public function removeAction($id, Request $request)
    {
        $session = $request->getSession();
        $cart = $session->get('cart');
        
        if(array_key_exists($id, $cart))
        {
            unset($cart[$id]);
            $session->set('cart', $cart);
        }
        
        $this->get('session')->getFlashBag()->add('success', 'Article supprimé avec succès');
        
        return $this->redirect($this->generateUrl('cart'));
        
    }
    
    public function setShippingSession($request)
    {
        $session = $request->getSession();
        
        if(!$session->has('address')) $session->set('address', array());
        $address = $session->get('address');
        
        if($request->request->get('shipping') != null && $request->request->get('billing') != null){
            $address['shipping'] = $request->request->get('shipping');
            $address['billing'] = $request->request->get('billing');
        }else{
            return $this->redirect($this->generateUrl('shipping'));
        }
        
        $session->set('address', $address);
        return $this->redirect($this->generateUrl('validation'));
    }
    
    public function validationAction(Request $request)
    {
        if($request->getMethod() == 'POST'){
            $this->setShippingSession($request);
        }
        
        
        $em = $this->getDoctrine()->getManager();
        $setOrderDesc = $this->forward('EcommerceBundle:Orders:setOrderDesc');
        $order = $em->getRepository('EcommerceBundle:Orders')->find($setOrderDesc->getContent());
        $details = $order->getOrderDesc();

//        
//        $products = array(
//            array('name' => 'produit1',
//                  'price' => 10.0,
//                  'priceTVA' => 12.0,
//                  'count' => 1),
//            array('name' => 'produit2',
//                  'price' => 25.5,
//                  'priceTVA' => 30.50,
//                  'count' => 2)
//        );
//        $total = 61.0;
//        $totalttc = 73.0;
        $tax = $details['priceTTC'] - $details['priceHT'];
//        //$port = 10.0;
        
        $url = $this->generateUrl('validationOrder', array('id' => $order->getId()));
        $url = 'http://'.$_SERVER['HTTP_HOST'].$url;
        
        $paypal = new Paypal($this->getParameter('array_param_paypal'));
        $params = array(
                    'RETURNURL' => $url,
                    'CANCELURL' => $url,
            
                    'PAYMENTREQUEST_0_AMT' => $details['priceHT'] + $tax,
                    'PAYMENTREQUEST_0_CURRENCYCODE' => 'CAD',
                    'PAYMENTREQUEST_0_TAXAMT' => $tax,
//                    'PAYMENTREQUEST_0_SHIPPINGAMT' => $port,
                    'PAYMENTREQUEST_0_ITEMAMT' => $details['priceHT']
                 );
        
        $i=0;
        foreach ($details['products'] as $product){
            $params["L_PAYMENTREQUEST_0_NAME$i"] = $product['reference'];
            $params["L_PAYMENTREQUEST_0_DESC$i"] = '';
            $params["L_PAYMENTREQUEST_0_AMT$i"] = $product['priceHT'];
            $params["L_PAYMENTREQUEST_0_QTY$i"] = $product['quantity'];
            $i++;
        }
        $response = $paypal->request('SetExpressCheckout', $params);
        
        if($response){
            $paypal = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token='.$response['TOKEN'];
        }else{
            var_dump($paypal->errors);
            die('Erreur ');
        }

        return $this->render('EcommerceBundle:Default:Cart/layout/validation.html.twig', array( 'order' => $order, 'paypal' => $paypal));
    }
}
