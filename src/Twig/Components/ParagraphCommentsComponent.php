<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Comment;
use App\Entity\Modification;
use App\Entity\ModificationStatement;
use App\Entity\Paragraph;
use App\Entity\ParagraphStatement;
use App\Entity\Statement;
use App\Entity\Thread;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\ModificationStatementRepository;
use App\Repository\ParagraphStatementRepository;
use App\Repository\ThreadRepository;
use DiffMatchPatch\DiffMatchPatch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('paragraph_comments')]
class ParagraphCommentsComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(writable: false)]
    public Paragraph $paragraph;

    #[LiveProp(writable: false)]
    public ?Modification $modification = null;

    #[LiveProp(writable: false)]
    public Statement $statement;

    #[LiveProp(writable: true)]
    public bool $overlayOpen = false;

    #[LiveProp(fieldName: 'formData')]
    public ?Comment $formComment = null;

    #[LiveProp(writable: true)]
    public ?Comment $parent = null;

    public function __construct(
        private readonly ThreadRepository $threadRepository,
        private readonly CommentRepository $commentRepository,
        private readonly ModificationStatementRepository $modificationStatementRepository,
        private readonly ParagraphStatementRepository $paragraphStatementRepository,
    ) {
    }

    #[LiveAction]
    public function setOverlayOpen(#[LiveArg] bool $open): void
    {
        $this->overlayOpen = $open;
    }

    #[LiveAction]
    public function resetParent(): void
    {
        $this->parent = null;
    }

    #[LiveAction]
    public function delete(#[LiveArg] int $id, #[LiveArg] bool $restore = false): void
    {
        $comment = $this->commentRepository->findOneBy(['id' => $id]);

        if (!$this->isGranted('edit', $comment)) {
            throw new BadRequestHttpException('Unable to delete comment');
        }

        if ($comment) {
            $comment->setDeletedAt($restore ? null : new \DateTimeImmutable());
            $comment->setDeletedBy($this->getUser());
            $this->commentRepository->save($comment, true);
        }
    }

    public function getThread(): Thread
    {
        if ($this->modification) {
            return $this->getModificationStatementOrCreate()->getThreadOrCreate();
        }

        return $this->getParagraphStatementOrCreate()->getThreadOrCreate();
    }

    public function getDiff(): array
    {
        if ($this->modification === null) {
            return [[0, $this->paragraph->getText()]];
        }

        return (new DiffMatchPatch())->diff_main($this->paragraph->getText(), $this->modification->getText(), false);
    }

    #[LiveAction]
    public function save(): void
    {
        $this->submitForm();

        /** @var Comment $comment */
        $comment = $this->getFormInstance()->getData();

        if (!($comment instanceof Comment)) {
            throw new \RuntimeException('Not a comment object');
        }

        $comment->setAuthor($this->getUser());
        $comment->setThread($this->getThread());
        $this->getThread()->addComment($comment);
        $comment->setParent($this->parent);

        $this->commentRepository->save($comment, true);

        $this->overlayOpen = true;
        $this->parent = null;
        $this->formComment = null;
    }

    public function getModificationStatementOrCreate(): ModificationStatement
    {
        return $this->modificationStatementRepository->getOrCreate($this->statement, $this->modification);
    }

    public function getParagraphStatementOrCreate(): ParagraphStatement
    {
        return $this->paragraphStatementRepository->getOrCreate($this->statement, $this->paragraph);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(CommentType::class, $this->formComment);
    }
}
