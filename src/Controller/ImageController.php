<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\User;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImageController extends AbstractController
{
    #[Route('/image/add/article/{id}', name: 'add_article_image')]
    #[Route('/image/add/comment/{id}', name: 'add_comment_image')]
    #[Route('/avatar/add/user/{id}', name: 'add_user_avatar')]
    public function index($id, Request $request, EntityManagerInterface $manager): Response
    {

        //determiner la route utilisée
        $route = $request->attributes->get("_route");

        //en fonction de la route, récuperer la bonne entité

        switch ($route){

            case 'add_article_image':
                $entity = Article::class;
                $setter = "setArticle";
                $redirectRoute = "article_image";
                $routeParam= ["id"=>$id];
                break;
            case 'add_user_avatar':
                $entity = User::class;
                $setter = "setAvatar";
                $redirectRoute = "user_avatar";
                $routeParam= ["id"=>$id];
                break;
            case 'add_comment_image':
                $entity = Comment::class;
                $setter = "setComment";
                $redirectRoute = "comment_image";
                $routeParam= ["id"=>$id];
                break;



        }


        $toBeAddedAnImage = $manager->getRepository($entity)->find($id);



        $image = new Image();
        $formImage = $this->createForm(ImageType::class, $image);
        $formImage->handleRequest($request);
        if($formImage->isSubmitted() && $formImage->isValid())
        {

            $image->$setter($toBeAddedAnImage);
            $manager->persist($image);
            $manager->flush();

        }



        return $this->redirectToRoute($redirectRoute, $routeParam);
    }

    #[Route('/image/delete/article/{id}', name: 'delete_article_image')]
    #[Route('/image/delete/comment/{id}', name: 'delete_comment_image')]
    #[Route('/image/delete/avatar/{id}', name: 'delete_user_avatar')]
    public function delete(EntityManagerInterface $manager, Image $image,Request $request){

        $route = $request->attributes->get("_route");

        switch ($route){

            case 'delete_article_image':
                $article = $image->getArticle();
                $redirectRoute = "article_image";
                $routeParam= ["id"=>$article->getId()];
                break;
            case 'delete_user_avatar':
                $avatar = $image->getAvatar();
                $redirectRoute = "user_avatar";
                $routeParam= ["id"=>$avatar->getId()];
                break;
            case 'delete_comment_image':
                $comment = $image->getComment();
                $redirectRoute = "comment_image";
                $routeParam= ["id"=>$comment->getId()];
                break;



        }
        $manager->remove($image);
        $manager->flush();


        return $this->redirectToRoute($redirectRoute, $routeParam);

    }


}
