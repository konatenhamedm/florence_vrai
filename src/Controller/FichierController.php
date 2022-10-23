<?php

namespace App\Controller;

use App\Entity\Fichier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("fichier")
 */
class FichierController extends AbstractController
{

    /**
     * @Route("/{id}", name="fichier_index", methods={"GET"})
     */
    public function show(Request $request, Fichier $fichier)
    {

        $fileName = $fichier->getFileName();
        $filePath = $fichier->getPath();
        $download = $request->query->get('download');

        $file = $this->getUploadDir($filePath . '/' . $fileName);

        if (!file_exists($file)) {
            return new Response('Fichier invalide');
        }

        if ($download) {
            return $this->file($file);
        }

        return new BinaryFileResponse($file);
    }

    /**
     * @return mixed
     */
    public function getUploadDir($path)
    {
        return $this->getParameter('upload_dir') . '/' . $path;
    }

    /**
     * @Route("/delete/{id}", name="fichier_delete", methods={"DELETE"}, condition="request.isXmlHttpRequest()")
     */
    public function delete(Fichier $id, EntityManagerInterface $em)
    {
        $em->remove($id);
        $em->flush();

        return $this->json(['statut' => 1]);
    }

}
