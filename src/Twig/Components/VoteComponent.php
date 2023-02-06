<?php

namespace App\Twig\Components;

use App\Entity\Comment;
use App\Entity\Vote;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('vote')]
class VoteComponent extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp]
    public Comment $comment;

    #[LiveProp]
    public bool $hasVoted = false;

    public function __construct(private CommentRepository $commentRepository, private EntityManagerInterface $entityManager)
    {
    }

    #[LiveAction]
    public function vote(#[LiveArg] string $input, #[LiveArg] Comment $comment): void
    {
        if ('up' === $input) {
            $this->submitVote('up', $comment);
        } elseif ('down' === $input) {
            $this->submitVote('down', $comment);
        }

        $this->hasVoted = true;
    }

    public function submitVote(string $input, Comment $comment): void
    {
        $vote = new Vote();
        $vote->setAuthor($this->getUser());
        $vote->setVote($input);
        $vote->setComment($comment);

        $this->entityManager->persist($vote);
        $this->entityManager->flush();
    }
}
