<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Ruta;
use App\Repository\RutaRepository;
use Symfony\Component\Finder\Finder;


final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(RutaRepository $rutaRepository): Response
    {
        $rutas = $rutaRepository->findAll();

        foreach ($rutas as $ruta) {
            $imageDir = $this->getParameter('kernel.project_dir') . '/public/images/uploads/' . $ruta->getId();
            $finder = new Finder();
            $images = [];

            if (is_dir($imageDir)) {
                $finder->files()->in($imageDir)->name('/\.(jpg|jpeg|png)$/i');

                foreach ($finder as $file) {
                    $images[] = 'images/uploads/' . $ruta->getId() . '/' . $file->getFilename();
                }
            }

            // Guardar imágenes en la propiedad temporal del objeto ruta
            $ruta->images = $images;
            $ruta->firstImage = $images[0] ?? 'images/default.jpg'; // Imagen por defecto si no hay imágenes
        }

        return $this->render('home/index.html.twig', [
            'rutas' => $rutas,
        ]);
    }
}
