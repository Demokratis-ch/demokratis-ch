<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ChosenModification;
use App\Entity\ModificationStatement;
use App\Repository\ChosenModificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ModificationService
{
    public function __construct(
        private readonly ChosenModificationRepository $chosenModificationRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly UserService $userService,
    ) {
    }

    public function accept(ModificationStatement $modificationStatement): ChosenModification
    {
        $user = $this->userService->getLoggedInUser();
        if ($user === null) {
            throw new AccessDeniedException();
        }

        $statement = $modificationStatement->getStatement();

        if (!$this->authorizationChecker->isGranted('own', $statement)) {
            throw new AccessDeniedException();
        }

        $paragraph = $modificationStatement->getModification()->getParagraph();

        $chosenModification = $this->chosenModificationRepository->findOneBy(['paragraph' => $paragraph, 'statement' => $statement]);

        if (!$chosenModification) {
            $chosenModification = new ChosenModification();
            $chosenModification->setParagraph($paragraph);
            $chosenModification->setStatement($statement);
        }

        $chosenModification->setModificationStatement($modificationStatement);
        $chosenModification->setChosenBy($user);
        $chosenModification->setChosenAt(new \DateTimeImmutable());
        $this->entityManager->persist($chosenModification);

        $this->entityManager->flush();

        return $chosenModification;
    }

    public function resetParagraph(ChosenModification $chosenModification): void
    {
        $statement = $chosenModification->getStatement();

        if (!$this->authorizationChecker->isGranted('own', $statement)) {
            throw new AccessDeniedException();
        }

        $this->entityManager->remove($chosenModification);
        $this->entityManager->flush();
    }
}
