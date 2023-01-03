<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'app_api_')]
class ApiController extends AbstractController
{
    #[Route('/articles/random', name: 'articles_random')]
    public function index(ArticleRepository $articleRepository): JsonResponse
    {
        return $this->json($articleRepository->selectRandomOne());
    }

    #[Route('/articles/search', name: 'articles_search')]
    public function searchArticle(Request $request, ArticleRepository $articleRepository): JsonResponse
    {
        return $this->json($articleRepository->search($request->get('q')));
    }
}
