<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/programs", name="program_")
 */
class ProgramController extends AbstractController
{
    /**
     * Show all rows from Program’s entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        return $this->render(
            'program/index.html.twig',
            ['programs' => $programs]
        );
    }

    /**
     * Getting a program by id
     *
     * @Route("/show/{id<^[0-9]+$>}", name="show")
     * @return Response
     */
    public function show(int $id): Response
    {
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['id' => $id]);

        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findAll();

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $id . ' found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', [
            'program' => $program,
            'season' => $season,
        ]);
    }

    /**
     * Getting a program's season by id's
     *
     * @Route("/{programId}/season/{seasonId}", name="season_show")
     * @return Response
     */
    public function showSeason(int $programId, int $seasonId)
    {
        $programId = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['id' => $programId]);

        $seasonId = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $seasonId]);

        $episodes = $seasonId->getEpisodes();

        if (!$programId) {
            throw $this->createNotFoundException(
                'No program with id : ' . $programId . ' found in program\'s table.'
            );
        } elseif (!$seasonId) {
            throw $this->createNotFoundException(
                'No season with id : ' . $seasonId . ' found in season\'s table.'
            );
        }
        return $this->render('program/season_show.html.twig', [
            'program' => $programId,
            'season' => $seasonId,
            'episodes' => $episodes,

        ]);
    }
}
