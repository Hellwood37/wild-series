<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Service\Slugify;
use App\Form\ProgramType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


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
     * The controller for the program add form
     * Display the form or deal with it
     *
     * @Route("/new", name="new")
     */
    public function new(Request $request, Slugify $slugify): Response
    {
        // Create a new Program Object
        $program = new Program();
        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            // Deal with the submitted data
            // Get the Entity Manager
            $entityManager = $this->getDoctrine()->getManager();
            // Call of the service Slugify
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            // Persist Program Object
            $entityManager->persist($program);
            // Flush the persisted object
            $entityManager->flush();
            // Finally redirect to categories list
            return $this->redirectToRoute('program_index');
        }
        // Render the form
        return $this->render('program/new.html.twig', ["form" => $form->createView()]);
    }

    /**
    * @Route("/{program}", name="show", methods={"GET"})
    * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program": "slug"}})
    * @return Response
    */
    public function show(Program $program): Response
    {
        $seasons = $program->getSeasons();

        if (!$program) {
            throw $this->createNotFoundException(
                'No program : ' . $program . ' found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', ['program' => $program, 'seasons' => $seasons,]);
    }

    /**
     * Getting a program's season by id's
     *
     * @Route("/{program}/seasons/{season}", name="season_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program": "slug"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"season": "number"}})
     *
     * @return Response
     */
    public function showSeason(Program $program, Season $season): Response
    {
        $episodes = $season->getEpisodes();
        $actors = $program->getActors();

        if (!$season) {
            throw $this->createNotFoundException(
                'No season with number : ' . $season . ' found in season\'s table.'
            );
        }
        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
            'actors' => $actors,


        ]);
    }

    /**
     * Getting an episode from a season's program
     * 
     * @Route("/{program}/seasons/{season}/episodes/{episode}", name="episode_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program": "slug"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"season": "number"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episode": "slug"}})
     *
     * @return Response
     */
    public function showEpisode(Program $program, Season $season, Episode $episode)
    {
        if (!$episode) {
            throw $this->createNotFoundException(
                'No episode : ' . $episode . ' found in program\'s table.'
            );
        }
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,

        ]);
    }

    /**
     * @Route("/{program}/edit", name="edit", methods={"GET","POST"})
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program": "slug"}})
     */
    public function edit(Request $request, Program $program): Response
    {
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/edit.html.twig', [
            'program' => $program,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{program}", name="delete", methods={"DELETE"})
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program": "slug"}})
     */
    public function delete(Request $request, Program $program): Response
    {
        if ($this->isCsrfTokenValid('delete' . $program->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($program);
            $entityManager->flush();
        }

        return $this->redirectToRoute('program_index');
    }
}
