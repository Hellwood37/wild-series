<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(): Response
    {
        /* $id = $this->getDoctrine()
            ->getRepository(Program::class); */
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findby(
                array(),
                array('id' => 'DESC'),
                $limit = 3,
            );
        return $this->render('home/home.html.twig', ['program' => $program,]);
    }
}
