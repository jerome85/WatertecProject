<?php

namespace EcommerceBundle\Controller;

use EcommerceBundle\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Medium controller.
 *
 */
class MediaAdminController extends Controller
{
    /**
     * Lists all medium entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $medias = $em->getRepository('EcommerceBundle:Media')->findAll();

        return $this->render('EcommerceBundle:Administration:media/layout/index.html.twig', array(
            'medias' => $medias,
        ));
    }

    /**
     * Creates a new medium entity.
     *
     */
    public function newAction(Request $request)
    {
        $medium = new Media();
        $form = $this->createForm('EcommerceBundle\Form\MediaType', $medium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($medium);
            $em->flush();

            return $this->redirectToRoute('adminMedia_show', array('id' => $medium->getId()));
        }

        return $this->render('EcommerceBundle:Administration:media/layout/new.html.twig', array(
            'medium' => $medium,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a medium entity.
     *
     */
    public function showAction(Media $medium)
    {
        $deleteForm = $this->createDeleteForm($medium);

        return $this->render('EcommerceBundle:Administration:media/layout/show.html.twig', array(
            'media' => $medium,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing medium entity.
     *
     */
    public function editAction(Request $request, Media $medium)
    {
        $deleteForm = $this->createDeleteForm($medium);
        $editForm = $this->createForm('EcommerceBundle\Form\MediaType', $medium);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('adminMedia_edit', array('id' => $medium->getId()));
        }

        return $this->render('EcommerceBundle:Administration:media/layout/edit.html.twig', array(
            'medium' => $medium,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a medium entity.
     *
     */
    public function deleteAction(Request $request, Media $medium)
    {
        $form = $this->createDeleteForm($medium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($medium);
            $em->flush();
        }

        return $this->redirectToRoute('adminMedia_index');
    }

    /**
     * Creates a form to delete a medium entity.
     *
     * @param Media $medium The medium entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Media $medium)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('adminMedia_delete', array('id' => $medium->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
