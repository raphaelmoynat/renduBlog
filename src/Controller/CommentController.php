<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormTypeInterface;

class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    #[Route('/comment/create/{id}', name: 'app_comment_create')]
    public function create(Request $request, EntityManagerInterface $manager, Article $article):Response
    {
        if(!$this->getUser()){return $this->redirectToRoute("app_article");}

        $comment= new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $comment->setAuthor($this->getUser());
            $comment->setCreatedAt(new \DateTime());


            $comment->setArticle($article);
            $manager->persist($comment);
            $manager->flush();

        }

        return $this->redirectToRoute("app_show",["id"=>$article->getId()]);


    }

    #[Route('/comment/delete/{id}', name: 'delete_comment')]
    public function delete(Comment $comment, EntityManagerInterface $manager): Response
    {

        if($this->getUser() === $comment->getAuthor()) {
            $article = $comment->getArticle();

            $manager->remove($comment);
            $manager->flush();

            return $this->redirectToRoute('app_show', ["id" => $article->getId()]);
        }else{
            return $this->redirectToRoute('app_article');
        }





    }

    #[Route('/comment/edit/{id}', name: 'edit_comment')]
    public function edit(Request $request, EntityManagerInterface $manager, Comment $comment):Response
    {
        $article = $comment->getArticle();
        $form = $this->createForm(CommentType::class, $comment);


        if($this->getUser() === $comment->getAuthor()) {

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setCreatedAt(new \DateTime());

                $manager->persist($comment);
                $manager->flush();

                return $this->redirectToRoute("app_show", ["id" => $article->getId()]);
            }
        }else{
            return $this->redirectToRoute('app_article');
        }

        return $this->render('comment/edit.html.twig', [

            "form"=>$form->createView(),
        ]);

    }
}
