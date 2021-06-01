<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/my-profile")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_profile", methods={"GET"})
     * @IsGranted("ROLE_CONTRIBUTOR")
     */
    public function show(): Response
    {   
        $user = $this->getUser();
        // Check wether the logged in user is the owner of the comment
        if (!($this->getUser())) {
            // If not the owner, throws a 403 Access Denied exception
            throw new AccessDeniedException('Only the owner can delete the comment!');
        }
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

}
