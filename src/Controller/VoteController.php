<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Vote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/vote', name: 'app_vote', methods: ['GET'])]
class VoteController extends AbstractController
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->manager = $entityManager;
    }

    #[Route('/{id}/up', name: '_up')]
    public function up(Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $this->vote('up', $comment);

        return $this->redirectToRoute('app_paragraph_diff', ['uuid' => $comment->getThread()->getModification()->getUuid()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/down', name: '_down')]
    public function down(Comment $comment): Response
    {
        $this->vote('down', $comment);

        return $this->redirectToRoute('app_paragraph_diff', ['uuid' => $comment->getThread()->getModification()->getUuid()], Response::HTTP_SEE_OTHER);
    }

    public function vote(string $input, Comment $comment): void
    {
        $vote = new Vote();
        $vote->setAuthor($this->getUser());
        $vote->setVote($input);
        $vote->setComment($comment);

        $this->manager->persist($vote);
        $this->manager->flush();
    }
}
