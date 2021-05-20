<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Service\Slugify;
use App\Form\EpisodeType;
use Symfony\Component\Mime\Email;
use App\Repository\EpisodeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/episode")
 */
class EpisodeController extends AbstractController
{
    /**
     * @Route("/", name="episode_index", methods={"GET"})
     */
    public function index(EpisodeRepository $episodeRepository): Response
    {
        return $this->render('episode/index.html.twig', [
            'episodes' => $episodeRepository->findAll(),
            
        ]);
    }

    /**
     * @Route("/new", name="episode_new", methods={"GET","POST"})
     */
    public function new(Request $request, Slugify $slugify, MailerInterface $mailer): Response
    {
        $episode = new Episode();
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            // Call of the service Slugify
            $slug = $slugify->generate($episode->getTitle());
            $episode->setSlug($slug);
            $entityManager->persist($episode);
            $entityManager->flush();
            // Send an Email after have had persist object
            $season = $this->getDoctrine()->getRepository(Season::class)->find($episode->getSeason());
            $program = $this->getDoctrine()->getRepository(Program::class)->find($season->getProgram());

            $email = new Email();
            $email->from($this->getParameter("mailer_from"))
                ->to($this->getParameter("mailer_from"))
                ->subject('Un nouvelle épisode vient d\'être publié !')
                ->html($this->renderView("email/newEpisodeEmail.html.twig", [
                    "episode" => $episode,
                    "program" => $program
                ]));
            $mailer->send($email);
            return $this->redirectToRoute('episode_index');
        }

        return $this->render('episode/new.html.twig', [
            'episode' => $episode,
            
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{episode}", name="episode_show", methods={"GET"})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episode": "slug"}})
     */
    public function show(Episode $episode): Response
    {
        return $this->render('episode/show.html.twig', [
            'episode' => $episode,
        ]);
    }

    /**
     * @Route("/{episode}/edit", name="episode_edit", methods={"GET","POST"})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episode": "slug"}})
     */
    public function edit(Request $request, Episode $episode): Response
    {
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('episode_index');
        }

        return $this->render('episode/edit.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{episode}", name="episode_delete", methods={"DELETE"})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episode": "slug"}})
     */
    public function delete(Request $request, Episode $episode): Response
    {
        if ($this->isCsrfTokenValid('delete' . $episode->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($episode);
            $entityManager->flush();
        }

        return $this->redirectToRoute('episode_index');
    }
}
