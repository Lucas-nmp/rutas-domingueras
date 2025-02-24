<?php

namespace App\Controller;

use App\Entity\Ruta;
use App\Form\RutaType;
use App\Repository\RutaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ruta')]
final class RutaController extends AbstractController
{
    #[Route(name: 'app_ruta_index', methods: ['GET'])]
    public function index(RutaRepository $rutaRepository): Response
    {
        return $this->render('ruta/index.html.twig', [
            'rutas' => $rutaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ruta_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rutum = new Ruta();
        $form = $this->createForm(RutaType::class, $rutum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rutum);
            $entityManager->flush();

            return $this->redirectToRoute('app_ruta_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ruta/new.html.twig', [
            'rutum' => $rutum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ruta_show', methods: ['GET'])]
    public function show(Ruta $rutum): Response
    {
        return $this->render('ruta/show.html.twig', [
            'rutum' => $rutum,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ruta_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ruta $rutum, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RutaType::class, $rutum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ruta_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ruta/edit.html.twig', [
            'rutum' => $rutum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ruta_delete', methods: ['POST'])]
    public function delete(Request $request, Ruta $rutum, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rutum->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rutum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ruta_index', [], Response::HTTP_SEE_OTHER);
    }
}
