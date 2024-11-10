<?php

namespace App\Service;

use App\Repository\FileRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class FileSharingService
{
    private FileRepository $fileRepository;
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(FileRepository $fileRepository, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->fileRepository = $fileRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function shareWithFriend(int $fileId, int $friendId): bool
    {
        $file = $this->fileRepository->find($fileId);
        $friend = $this->userRepository->find($friendId);

        if (!$file || !$friend) {
            return false;
        }

        // Assure-toi que `addSharedWith` est une méthode définie dans l'entité `File`
        $file->addSharedWith($friend);
        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return true;
    }

    public function unshareWithFriend(int $fileId, int $friendId): bool
    {
        $file = $this->fileRepository->find($fileId);
        $friend = $this->userRepository->find($friendId);

        if (!$file || !$friend) {
            return false;
        }

        // Assure-toi que `removeSharedWith` est une méthode définie dans l'entité `File`
        $file->removeSharedWith($friend);
        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return true;
    }
}
