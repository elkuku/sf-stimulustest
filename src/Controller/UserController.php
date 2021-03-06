<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $template = $request->query->get('ajax')
            ? '_list.html.twig'
            : 'index.html.twig';

        return $this->render(
            'user/'.$template,
            [
                'users' => $userRepository->findAll(),
            ]
        );
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        $template = $request->query->get('ajax')
            ? '_form.html.twig'
            : 'new.html.twig';

        return $this->render(
            'user/'.$template,
            [
                'user' => $user,
                'form' => $form->createView(),
            ],
            new Response(null, $form->isSubmitted() ? 422 : 200)
        );
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render(
            'user/show.html.twig',
            [
                'user' => $user,
            ]
        );
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        $template = $request->query->get('ajax')
            ? '_form.html.twig'
            : 'edit.html.twig';

        return $this->render(
            'user/'.$template,
            [
                'user' => $user,
                'form' => $form->createView(),
            ],
            new Response(null, $form->isSubmitted() ? 422 : 200)
        );
    }

    #[Route('/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid(
            'delete'.$user->getId(),
            $request->request->get('_token')
        )
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
