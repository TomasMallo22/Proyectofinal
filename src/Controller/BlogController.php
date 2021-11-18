<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Category;
use App\Entity\Post;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'blog')]
    public function index(): Response
    {
        //conectar a base de datos y solicitar categorias
        $cRepositorio = $this->getDoctrine()->getRepository(Category::class);
        $category = $cRepositorio->findAll();

        //solicitamos post de la base de datos
        $repoPost = $this->getDoctrine()->getRepository(Post::class);
        $posts = $repoPost->findAll();
        
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'category' => $category,
            'posts' => $posts,
        ]);
    }
}
