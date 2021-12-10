<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


use App\Entity\Post;
use App\Entity\Category;

use App\Repository\CategoryRepository;
use App\Repository\PostRepository;

class BlogController extends AbstractController
{

    private $repoCat;
    

    public function __construct( CategoryRepository $rc){
        $this->repoCat = $rc;
    }


    #[Route('/blog', name: 'blog')]
    public function index(Request $request, PostRepository $repoPost): Response
    {

        //filtrado de categorias
        $idCategoria = $request->query->get('cat');
        if(empty($idCategoria)){
            $idCategoria = 0;
        }
        $categoria = $this->repoCat->find($idCategoria);

        

        //conectar a base de datos y solicitar categorias
        $repoCat = $this->getDoctrine()->getRepository(Category::class);
        $category = $repoCat->findAll();

        //solicitamos post de la base de datos
        $repoPost = $this->getDoctrine()->getRepository(Post::class);
        $posts = $repoPost->findAll();
        
        $posts = $repoPost->findByFilter($categoria);

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'category' => $category,
            'posts' => $posts,
            'filter' => ['cat'=>$idCategoria]
        ]);
    }

    #[Route('/blog/{id}', name: 'blog-detail')]

    public function detail(Request $request, Post $post): Response
    {
        return $this->render('blog/detail.html.twig',[
            'post' => $post,
            'category' => $this->repoCat->findAll(),
        
        ]);
    }
}
