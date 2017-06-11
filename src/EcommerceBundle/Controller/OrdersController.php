<?php

namespace EcommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use EcommerceBundle\Entity\UsersAddresses;
use EcommerceBundle\Entity\Orders;

class OrdersController extends Controller
{
    public function billing($request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $address = $session->get('address');
        $cart = $session->get('cart');
        $order = array();
        $totalHT = 0;
        $totalTTC = 0;
        
        $billing = $em->getRepository('EcommerceBundle:UsersAddresses')->find($address['billing']);
        $shipping = $em->getRepository('EcommerceBundle:UsersAddresses')->find($address['shipping']);
        $products = $em->getRepository('EcommerceBundle:Products')->findArray(array_keys($cart));
        
        foreach ($products as $product){
            $priceHT = ($product->getPrice() * $cart[$product->getId()]);
            $priceTTC = $priceHT + round($product->getPrice() * $cart[$product->getId()] * 0.05, 2) + round($product->getPrice() * $cart[$product->getId()] * 0.09975, 2);
            
            $totalHT += $priceHT;
            $totalTTC += $priceTTC;
            
            $order['products'][$product->getId()] = array('reference' => $product->getName(),
                                                          'images' => $product->getImages(),
                                                          'quantity' => $cart[$product->getId()],
                                                          'priceHT' => round($priceHT, 2),
                                                          'priceTTC' => round($priceTTC, 2));
            $order['shipping'] = array('firstname' => $shipping->getFirstname(),
                                       'lastname' => $shipping->getLastname(),
                                       'phone' => $shipping->getPhone(),
                                       'address' => $shipping->getAddress(),
                                       'postalCode' => $shipping->getPostalCode(),
                                       'city' => $shipping->getCity(),
                                       'country' => $shipping->getCountry(),
                                       'complement' => $shipping->getComplement());
            $order['billing'] = array('firstname' => $billing->getFirstname(),
                                       'lastname' => $billing->getLastname(),
                                       'phone' => $billing->getPhone(),
                                       'address' => $billing->getAddress(),
                                       'postalCode' => $billing->getPostalCode(),
                                       'city' => $billing->getCity(),
                                       'country' => $billing->getCountry(),
                                       'complement' => $billing->getComplement());
        }
        
        $order['priceHT'] = round($totalHT, 2);
        $order['priceTTC'] = round($totalTTC, 2);
        $order['token'] = bin2hex(random_bytes(20));
        
        return $order;
        
    }
    
    public function setOrderDescAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        
        if(!$session->has('order')){
            $order = new Orders();
        }else{
            $order = $em->getRepository('EcommerceBundle:Orders')->find($session->get('order'));
        }
        
        $order->setDate(new \DateTime());
        $order->setUser($this->get('security.token_storage')->getToken()->getUser());
        $order->setValidate(0);
        $order->setReference(0);
        $order->setOrderDesc($this->billing($request));
        
        if(!$session->has('order')){
            $em->persist($order);
            $session->set('order', $order);
        }
        
        $em->flush();
        return new Response($order->getId());
    }
}