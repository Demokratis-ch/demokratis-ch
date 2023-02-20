<?php

namespace App\Controller;

use App\Entity\Membership;
use App\Form\MembershipType;
use App\Repository\MembershipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MembershipController extends AbstractController
{
    #[Route('/join', name: 'app_membership_join', methods: ['GET', 'POST'])]
    public function join(Request $request, MembershipRepository $membershipRepository): Response
    {
        $membership = new Membership();
        $form = $this->createForm(MembershipType::class, $membership);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $membership->setCreatedBy($this->getUser());
            }

            $membershipRepository->save($membership, true);

            return $this->redirectToRoute('app_membership_joined');
        }

        return $this->render('membership/join.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/joined', name: 'app_membership_joined', methods: ['GET'])]
    public function joined(): Response
    {
        return $this->render('membership/joined.html.twig', [
        ]);
    }
}
