<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/media')]
class MediaController extends AbstractController
{
    #[Route('add/{slug}', name: 'app_media_add', methods: ['GET', 'POST'])]
    public function add(Consultation $consultation, MediaRepository $mediaRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MediaType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $medium = $form->getData();
            $medium->setConsultation($consultation);
            $medium->setCreatedBy($this->getUser());
            $medium->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($medium);

            $entityManager->flush();

            return $this->redirectToRoute('app_consultation_index_media', ['slug' => $consultation->getSlug()]);
        }

        return $this->render('media/add.html.twig', [
            'consultation' => $consultation,
            'form' => $form,
        ]);
    }
}
