<?php

namespace App\Controller;

use App\Entity\Fichier;
use App\Form\FichierType;
use App\Repository\FichierRepository;
use App\Repository\ScategorieRepository;
use App\Repository\UserRepository;
use App\Service\FileSharingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FichierController extends AbstractController
{
    #[Route('/ajout-fichier', name: 'app_ajout_fichier')]
    public function ajoutFichier(Request $request, ScategorieRepository $scategorieRepository,
        EntityManagerInterface $em, SluggerInterface $slugger): Response {
        $fichier = new Fichier();
        $scategories = $scategorieRepository->findBy([], ['categorie' => 'asc', 'numero' => 'asc']);
        $form = $this->createForm(FichierType::class, $fichier, ['scategories' => $scategories]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedScategories = $form->get('scategories')->getData();
            foreach ($selectedScategories as $scategorie) {
                $fichier->addScategory($scategorie);
            }
            $file = $form->get('fichier')->getData();

            if ($file) {
                $nomFichierServeur = pathinfo($file->getClientOriginalName(),
                    PATHINFO_FILENAME);
                $nomFichierServeur = $slugger->slug($nomFichierServeur);
                $nomFichierServeur = $nomFichierServeur . '-' . uniqid() . '.' . $file->guessExtension();

                try {
                    // Assigner automatiquement l'utilisateur connecté
                    $fichier->setUser($this->getUser());

                    $fichier->setNomServeur($nomFichierServeur);
                    $fichier->setNomOriginal($file->getClientOriginalName());
                    $fichier->setDateEnvoi(new \Datetime());
                    $fichier->setExtension($file->guessExtension());
                    $fichier->setTaille($file->getSize());

                    $em->persist($fichier);
                    $em->flush();

                    $file->move($this->getParameter('file_directory'), $nomFichierServeur);
                    $this->addFlash('notice', 'Fichier envoyé');
                    return $this->redirectToRoute('app_ajout_fichier');
                } catch (FileException $e) {
                    $this->addFlash('notice', 'Erreur d\'envoi');
                }
            }
        }

        return $this->render('fichier/ajout-fichier.html.twig', [
            'form' => $form,
            'scategories' => $scategories,
        ]);
    }

    #[Route('/liste-fichiers', name: 'app_liste_fichiers')]
    public function listeFichiers(FichierRepository $fichierRepository): Response
    {
        $fichiers = $fichierRepository->findAll();
        return $this->render('fichier/liste-fichiers.html.twig', [
            'fichiers' => $fichiers,
        ]);
    }

    #[Route('/liste-fichiers-par-utilisateur', name: 'app_liste_fichiers_par_utilisateur')]
    public function listeFichiersParUtilisateur(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], ['name' => 'asc', 'prenom' => 'asc']);
        return $this->render('fichier/liste-fichiers-par-utilisateur.html.twig', ['users' => $users]);
    }

    #[Route('/private-telechargement-fichier-user/{id}', name: 'app_telechargement_fichier_user',
        requirements: ["id" => "\d+"])]
    public function telechargementFichierUser(Fichier $fichier)
    {
        if ($fichier == null) {
            return $this->redirectToRoute('app_profil');
        } else {
            if ($fichier->getUser() !== $this->getUser()) {
                $this->addFlash('notice', 'Vous n\'êtes pas le propriétaire de ce fichier');
                return $this->redirectToRoute('app_profil');
            }
            return $this->file($this->getParameter('file_directory') . '/' . $fichier->getNomServeur(),
                $fichier->getNomOriginal());
        }
    }

    #[Route('/file/share', name: 'file_share', methods: ['POST'])]
    public function shareFile(Request $request, FileSharingService $fileSharingService): Response
    {
        $data = json_decode($request->getContent(), true);
        $fileId = $data['fileId'];
        $friendId = $data['friendId'];

        $result = $fileSharingService->shareWithFriend($fileId, $friendId);

        return $result ? $this->json(['message' => 'Fichier partagé avec succès'])
        : $this->json(['message' => 'Échec du partage'], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/file/download/{id}', name: 'file_download', methods: ['GET'])]
    public function downloadFile(int $id, FileRepository $fileRepository): Response
    {
        $file = $fileRepository->find($id);

        if (!$file || !$file->isSharedWith($this->getUser())) {
            return $this->json(['message' => 'Fichier non accessible'], Response::HTTP_FORBIDDEN);
        }

        $filePath = $this->getParameter('uploads_directory') . '/' . $file->getFilename();

        return $this->file($filePath);
    }

    #[Route('/file/unshare', name: 'file_unshare', methods: ['POST'])]
    public function unshareFile(Request $request, FileSharingService $fileSharingService): Response
    {
        $data = json_decode($request->getContent(), true);
        $fileId = $data['fileId'];
        $friendId = $data['friendId'];

        $result = $fileSharingService->unshareWithFriend($fileId, $friendId);

        return $result ? $this->json(['message' => 'Partage annulé'])
        : $this->json(['message' => 'Échec de l\'annulation'], Response::HTTP_BAD_REQUEST);
    }
}
