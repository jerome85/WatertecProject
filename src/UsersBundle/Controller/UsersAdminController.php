<?php

namespace UsersBundle\Controller;

use UsersBundle\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * User controller.
 *
 * @Route("users")
 */
class UsersAdminController extends Controller
{
    /**
     * Lists all user entities.
     *
     * @Route("/", name="users_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('UsersBundle:Users')->findAll();

        return $this->render('UsersBundle:Administration:Users/layout/index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * Finds and displays a user entity.
     *
     * @Route("/{id}", name="users_show")
     * @Method("GET")
     */
    public function showAction(Users $user)
    {

        return $this->render('users/show.html.twig', array(
            'user' => $user,
        ));
    }
    
    /**
     * Finds and displays a user entity.
     *
     * @Route("/{id}/promote", name="usersAdmin_promote")
     * @Method("GET")
     */
    public function promoteAction($id)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);
        $user->addRole("ROLE_ADMIN");
        $userManager->updateUser($user);
        
        return $this->redirect( $this->generateUrl('adminUsers_index') );
    }
    
    /**
     * Finds and displays a user entity.
     *
     * @Route("/{id}/demote", name="usersAdmin_demote")
     * @Method("GET")
     */
    public function demoteAction($id)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);
        $user->removeRole("ROLE_ADMIN");
        $userManager->updateUser($user);
        
        return $this->redirect( $this->generateUrl('adminUsers_index') );
    }
}
