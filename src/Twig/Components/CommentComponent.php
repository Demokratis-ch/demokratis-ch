<?php

namespace App\Twig\Components;

use App\Entity\Statement;
use App\Entity\Thread;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\ThreadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('comment')]
class CommentComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    protected RequestStack $requestStack;

    #[LiveProp(writable: false)]
    public Thread $thread;

    #[LiveProp(writable: false)]
    public Statement|null $statement = null;

    public bool $commenting = false;

    #[LiveProp(writable: true)]
    public int|null $parent = null;

    #[LiveProp(writable: true)]
    public bool $more = false;

    #[LiveAction]
    public function comment(#[LiveArg] string $show, #[LiveArg] int $parent = null): void
    {
        if ($show === 'form') {
            $this->commenting = false;
        }

        if ($show === 'comments') {
            $this->commenting = true;
        }

        $this->parent = $parent;
    }

    #[LiveAction]
    public function delete(#[LiveArg] int $id): void
    {
        $comment = $this->commentRepository->findOneBy(['id' => $id]);

        if ($comment) {
            $comment->setDeletedAt(new \DateTimeImmutable());
            $comment->setDeletedBy($this->getUser());
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
        }
    }

    #[LiveAction]
    public function showMore(#[LiveArg] bool $more): void
    {
        $this->more = $more;
    }

    public function __construct(
        RequestStack $requestStack,
        private readonly ThreadRepository $threadRepository,
        private readonly CommentRepository $commentRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        $this->requestStack = $requestStack;
    }

    public function getComments(?Thread $thread): array
    {
        return $this->commentRepository->findBy(['thread' => $thread, 'parent' => null, 'deletedAt' => null]);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(CommentType::class, null, [
            'action' => $this->generateUrl('app_comment_add', ['id' => $this->thread->getId(), 'parentId' => $this->parent]),
            'r' => serialize([
                'route' => $this->requestStack->getCurrentRequest()->get('_route'),
                'params' => $this->requestStack->getCurrentRequest()->get('_route_params'),
                'lt' => $this->requestStack->getCurrentRequest()->get('lt'),
            ]),
        ]);
    }
}
