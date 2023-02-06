<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\ExternalStatement;
use App\Form\ExternalStatementType;
use App\Repository\ExternalStatementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/publish-statement')]
class ExternalStatementController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/publish-statement/{uuid}/add', name: 'app_external_statement_new', methods: ['GET', 'POST'])]
    public function new(Consultation $consultation, Request $request, ExternalStatementRepository $statementRepository, SluggerInterface $slugger): Response
    {
        $statement = new ExternalStatement();

        if ($this->getUser()->getActiveOrganisation()) {
            $statement->setOrganisation($this->getUser()->getActiveOrganisation());
        }

        $statement->setCreatedBy($this->getUser());
        $statement->setConsultation($consultation);
        $form = $this->createForm(ExternalStatementType::class, $statement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $file = $form->get('file')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('file_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('File could not be uploaded');
                }

                $statement->setFile($newFilename);
            }

            $statementRepository->save($statement, true);

            return $this->redirectToRoute('app_external_statement_show', ['uuid' => $statement->getUuid()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('external_statement/new.html.twig', [
            'consultation' => $consultation,
            'form' => $form,
        ]);
    }

    #[Route('/publish-statement/{uuid}', name: 'app_external_statement_show', methods: ['GET'])]
    public function show(ExternalStatement $statement): Response
    {
        return $this->render('external_statement/show.html.twig', [
            'statement' => $statement,
        ]);
    }
}
