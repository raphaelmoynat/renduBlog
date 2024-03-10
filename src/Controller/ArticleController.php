<?php

namespace App\Controller;


use App\Entity\Article;

use App\Entity\Comment;
use App\Entity\Image;
use App\Form\ArticleType;

use App\Form\CommentType;
use App\Form\ImageType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [

            "articles"=>$articleRepository->findAll(),
        ]);
    }

    #[Route('/create/article', name: 'app_create_article')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        if(!$this->getUser()){return $this->redirectToRoute("app_article");}

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $article->setAuthor($this->getUser());


            $article->setCreatedAt(new \DateTime());
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute("app_article");
        }




        return $this->render('article/create.html.twig', [
            "article"=>$article,
            "form"=>$form->createView(),
            "btnValue"=>"Ajouter"
        ]);
    }

    #[Route('/article/show/{id}', name: 'app_show')]
    public function show(Article $article): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment);


        return $this->render('article/show.html.twig', [
            "article"=>$article,
            "form"=>$form->createView(),

        ]);
    }

    #[Route('/article/delete/{id}', name: 'app_delete_article')]
    public function delete(EntityManagerInterface $manager, Article $article):Response
    {
        if($this->getUser() === $article->getAuthor()) {


            $manager->remove($article);
            $manager->flush();

            return $this->redirectToRoute("app_article");
        }else{
            return $this->redirectToRoute("app_article");

        }



    }

    #[Route('/article/edit/{id}', name: 'app_edit_article')]
    public function edit(Request $request, EntityManagerInterface $manager, Article $article):Response
    {
        $form = $this->createForm(ArticleType::class, $article);

        if($this->getUser() === $article->getAuthor()) {


            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $article->setCreatedAt(new \DateTime());
                $manager->persist($article);
                $manager->flush();

                return $this->redirectToRoute("app_show", ["id" => $article->getId()]);
            }
        }else{
            return $this->redirectToRoute('app_article');
        }



        return $this->render('article/create.html.twig', [
            "form"=>$form->createView(),
            "btnValue"=>"Editer"
        ]);

    }

    #[Route('/article/images/{id}', name:"article_image")]
    public function addImage(Article $article):Response
    {
        $image = new Image();
        $formImage = $this->createForm(ImageType::class, $image);

        return $this->render("article/image.html.twig", [
            "article"=>$article,
            'formImage'=>$formImage->createView()

        ]);
    }




}
