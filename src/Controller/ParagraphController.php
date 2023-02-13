<?php

namespace App\Controller;

use App\Entity\ModificationStatement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ParagraphController extends AbstractController
{
    #[Route('/force-original/{uuid}', name: 'app_paragraph_original', methods: ['GET'])]
    public function forceOriginal(ModificationStatement $modificationStatement, EntityManagerInterface $manager): Response
    {
        $statement = $modificationStatement->getStatement();

        if (!$this->isGranted('own', $statement)) {
            throw new AccessDeniedException();
        }

        $paragraph = $modificationStatement->getModification()->getParagraph();
        $manager->persist($paragraph);
        $manager->flush();

        return $this->redirectToRoute('app_statement_show', ['uuid' => $statement->getUuid(), 'lt' => $paragraph->getLegalText()->getUuid()->toBase58(), '_fragment' => substr($paragraph->getUuid(), 0, 8)], Response::HTTP_SEE_OTHER);
    }
}
