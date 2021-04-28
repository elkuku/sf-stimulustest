<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'default', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            'default/index.html.twig',
            [
                'controller_name' => 'DefaultController',
                'php_version'     => PHP_VERSION,
            ]
        );
    }

    #[Route('/test-autocomplete', name: 'test_autocomplete', methods: ['GET'])]
    public function testAutocomplete()
    {
        return new Response(
            '<li class="list-group-item" role="option" data-autocomplete-value="1">Blackbird</li>
<li class="list-group-item" role="option" data-autocomplete-value="2">Bluebird</li>
<li class="list-group-item" role="option" data-autocomplete-value="3">Mockingbird</li>'
        );

    }
}
