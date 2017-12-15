<?php

namespace EcommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('EcommerceBundle:Default:index.html.twig');
    }
    
    public function sendEmailAction($name)
    {
        $message = new \Swift_Message('Hello Email');
        $message->setFrom('commande@watertecinc.com')
                ->setTo('jerome.nourrain85@gmail.com')
                ->setBody(
                    $this->renderView(
                            // app/Resources/views/Emails/registration.html.twig
                            'Emails/registration.html.twig',
                            array('name' => $name)
                        ),
                        'text/html'
                    );
  
    try{
        $this->get('mailer')->send($message);
        $this->get('session')->getFlashBag()->add('success', 'super, email envoyé');
    } catch ( Exception $e ) {
        print_r($e->getMessage());
        $this->get('session')->getFlashBag()->add('error', 'une erreur est survenue');
    }
        return $this->render('EcommerceBundle:Default:index.html.twig');
    }
    
    
    public function contactAction(Request $request)
    {
        $form = $this->createForm('EcommerceBundle\Form\ContactType');
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $message = new \Swift_Message('Contact Email');
            $message->setFrom('commande@watertecinc.com')
                    ->setTo('jerome.nourrain85@gmail.com')
                    ->setBody(
                        $this->renderView(
                                'EcommerceBundle:Default:Emails/registration.html.twig',
                                array('name' => 'jerome')
                            ),
                            'text/html'
                        );
            try{
                $this->get('mailer')->send($message);
                $this->get('session')->getFlashBag()->add('success', 'super, email envoyé');
                $json = array('alert' => 'success', 'message' => 'enjoy');
            } catch ( Exception $e ) {
                $this->get('session')->getFlashBag()->add('error', 'une erreur est survenue');
                $json = array('alert' => 'error', 'message' => $e->getMessage());
            }
            
            $response = new Response(json_encode($json));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
        
        return $this->render('EcommerceBundle:Default:contact.html.twig', array('form' => $form->createView()));
    }
    
}
