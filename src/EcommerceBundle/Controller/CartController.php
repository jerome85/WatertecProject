<?php

namespace EcommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Ecommerce\EcommerceBundle\Form\UtilisateursAdressesType;
use Ecommerce\EcommerceBundle\Entity\UtilisateursAdresses;
use Ecommerce\EcommerceBundle\Services\Paypal;

class CartController extends Controller
{
    public function menuAction()
    {
        $session = $this->getRequest()->getSession();
        
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
        $products = $em->getRepository('EcommerceBundle:Produits')->findArray($array);
        
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
    
    public function shippingAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $entity = new UtilisateursAdresses();
        $form = $this->createForm(new UtilisateursAdressesType(), $entity);
        
        if($this->get('request')->getMethod() == 'POST')
        {
            $form->handleRequest($this->getRequest());
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $entity->setUser($user);
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'Adresse ajoutée avec succès');
                
                return $this->redirect($this->generateUrl('shipping'));
            }else{
                print_r($form->getErrors());
            }
        }
        
        return $this->render('EcommerceBundle:Default:Cart/layout/shipping.html.twig', array('user' => $user,
                                                                                             'form' => $form->createView()));
    }
    
    public function removeAddressAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EcommerceBundle:UtilisateursAdresses')->find($id);
        
        if ($this->container->get('security.context')->getToken()->getUser() != $entity->getUser() || !$entity) 
        {
            return $this->redirect($this->generateUrl('shipping'));
        }

        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', 'Adresse retirée avec succès');
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
            $this->get('session')->getFlashBag()->add('success', 'Quantité modifiée avec succès');
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
    
    public function removeAction($id)
    {
        $session = $this->getRequest()->getSession();
        $cart = $session->get('cart');
        
        if(array_key_exists($id, $cart))
        {
            unset($cart[$id]);
            $session->set('cart', $cart);
        }
        
        $this->get('session')->getFlashBag()->add('success', 'Article supprimé avec succès');
        
        return $this->redirect($this->generateUrl('cart'));
        
    }
    
    public function setShippingSession()
    {
        $session = $this->getRequest()->getSession();
        
        if(!$session->has('address')) $session->set('address', array());
        $address = $session->get('address');
        
        if($this->getRequest()->request->get('shipping') != null && $this->getRequest()->request->get('facturation') != null){
            $address['shipping'] = $this->getRequest()->request->get('shipping');
            $address['facturation'] = $this->getRequest()->request->get('facturation');
        }else{
            return $this->redirect($this->generateUrl('shipping'));
        }
        
        $session->set('address', $address);
        return $this->redirect($this->generateUrl('validation'));
    }
    
    public function validationAction()
    {
        if($this->get('request')->getMethod() == 'POST'){
            $this->setShippingSession();
        }
        
        $port = '10.0';
        
        $em = $this->getDoctrine()->getManager();
        $setCommand = $this->forward('EcommerceBundle:Commands:setCommand');
        $command = $em->getRepository('EcommerceBundle:Commandes')->find($setCommand->getContent());
        $details = $command->getCommand();
        
        //print_r($details);die;
        
        $products = array(
            array('name' => 'produit1',
                  'price' => 10.0,
                  'priceTVA' => 12.0,
                  'count' => 1),
            array('name' => 'produit2',
                  'price' => 25.5,
                  'priceTVA' => 30.50,
                  'count' => 2)
        );
        $total = 61.0;
        $totalttc = 73.0;
        $tax = $details['prixTTC'] - $details['prixHT'];
        //$port = 10.0;
        
        $url = $this->generateUrl('validationCommand', array('id' => $command->getId()));
        $url = 'http://'.$_SERVER['HTTP_HOST'].$url;
        
        $paypal = new Paypal();
        $params = array(
                    'RETURNURL' => $url,
                    'CANCELURL' => $url,
            
                    'PAYMENTREQUEST_0_AMT' => $details['prixHT'] + $tax,
                    'PAYMENTREQUEST_0_CURRENCYCODE' => 'CAD',
                    'PAYMENTREQUEST_0_TAXAMT' => $tax,
                    //'PAYMENTREQUEST_0_SHIPPINGAMT' => $port,
                    'PAYMENTREQUEST_0_ITEMAMT' => $details['prixHT']
                 );
        
        $i=0;
        foreach ($details['products'] as $product){
            $params["L_PAYMENTREQUEST_0_NAME$i"] = $product['reference'];
            $params["L_PAYMENTREQUEST_0_DESC$i"] = '';
            $params["L_PAYMENTREQUEST_0_AMT$i"] = $product['prixHT'];
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

        return $this->render('EcommerceBundle:Default:Cart/layout/validation.html.twig', array( 'command' => $command,
                                                                                                'paypal' => $paypal));
    }
}
