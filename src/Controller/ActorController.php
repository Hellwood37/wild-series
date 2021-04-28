<?php
// src/Controller/ActorController.php
namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Form\ProgramType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/actors", name="actors_")
 */
class ActorController extends AbstractController
{
    /**
     * Show all rows from Actor’s entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $actors = $this->getDoctrine()
            ->getRepository(Actor::class)
            ->findAll();

        return $this->render(
            'actor/index.html.twig',
            ['actors' => $actors]
        );
    }

    /**
     * Getting a actor by id
     *
     * @Route("/{id}", name="show")
     * @return Response
     */
/*     public function show(Actor $actor): Response
    {
        $seasons = $program->getSeasons();

        if (!$program) {
            throw $this->createNotFoundException(
                'No program : ' . $program . ' found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', ['program' => $program, 'seasons' => $seasons,]);
    } */
}
