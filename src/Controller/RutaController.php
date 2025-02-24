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
        $ruta = new Ruta();
        $form = $this->createForm(RutaType::class, $ruta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ruta);
            $entityManager->flush();

            // Subir imágenes
            $images = $request->files->get('images');
            $uploadDir = $this->getParameter('kernel.project_dir') . '/public/images/uploads/' . $ruta->getId();

            
            if ($images) {
               
                foreach ($images as $image) {
                    if ($image->isValid() && in_array($image->getMimeType(), ['image/jpeg', 'image/png', 'image/gif'])) {
                        try {
                            $newFilename = uniqid() . '.' . $image->guessExtension();
                            $image->move($uploadDir, $newFilename);
                        } catch (FileException $e) {
                            $this->addFlash('error', 'No se pudo subir la imagen: ' . $e->getMessage());
                        }
                    } else {
                        $this->addFlash('error', 'Archivo no válido: ' . $image->getClientOriginalName());
                    }
                }
            } else {
                $this->addFlash('error', 'No se seleccionaron imágenes.');
            }

            return $this->redirectToRoute('app_ruta_index', [], Response::HTTP_SEE_OTHER);
            
        }

        return $this->render('ruta/new.html.twig', [
            'ruta' => $ruta,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ruta_show', methods: ['GET'])]
    public function show(Ruta $ruta): Response
    {
        return $this->render('ruta/show.html.twig', [
            'ruta' => $ruta,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ruta_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ruta $ruta, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RutaType::class, $ruta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ruta_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ruta/edit.html.twig', [
            'ruta' => $ruta,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ruta_delete', methods: ['POST'])]
    public function delete(Request $request, Ruta $ruta, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ruta->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ruta);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ruta_index', [], Response::HTTP_SEE_OTHER);
    }
}
