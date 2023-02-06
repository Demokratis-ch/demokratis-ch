<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Modification;
use App\Entity\Thread;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\ThreadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/c')]
class CommentController extends AbstractController
{
    #[Route('/{uuid}/show', name: 'app_comment_show', methods: ['GET'])]
    public function show(Modification $modification, ThreadRepository $threadRepository): Response
    {
        $threads = $threadRepository->findBy(['modification' => $modification]);

        return $this->render('comment/show.html.twig', [
            'modification' => $modification,
            'threads' => $threads,
        ]);
    }

    #[Route('/add/{identifier}/{parentId}', name: 'app_comment_add', methods: ['GET', 'POST'])]
    public function add(
        CommentRepository $commentRepository,
        ThreadRepository $threadRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        string $identifier,
        ?int $parentId = null
    ): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $thread = $threadRepository->findOneBy(['identifier' => $identifier]);

        if (!$thread) {
            $thread = new Thread();
            $thread->setIdentifier($identifier);
            $entityManager->persist($thread);
        }

        $parentComment = $commentRepository->findOneBy(['id' => $parentId]);

        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $comment->setCreatedAt(new \DateTimeImmutable());
        $comment->setThread($thread);
        $comment->setParent($parentComment);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->save($comment, true);
            $entityManager->flush();

            $route = unserialize($form['r']->getData());

            return $this->redirectToRoute($route['route'], array_merge($route['params'], ['lt' => $route['lt']]));
        }

        return $this->render('comment/add.html.twig', [
            'form' => $form,
            'thread' => $thread,
            'parent' => $parentComment,
        ]);
    }
}
