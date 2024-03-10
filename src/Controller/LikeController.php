<?php

namespace App\Controller;

use App\Entity\Like;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LikeController extends AbstractController
{
    #[Route('/like/article/{id}', name: 'app_like')]
    #[Route('/like/comment/{id}', name: 'comment_like')]
    public function like(Request $request, ArticleRepository $articleRepository, CommentRepository $commentRepository, $id, EntityManagerInterface $manager, LikeRepository $likeRepository): Response
    {
        $mode = null;
        $countSearch = array();
        $user = $this->getUser();
        if(!$user){return $this->json("no user connected", 400);}

        $route = $request->attributes->get("_route");
        if($route == "article_like"){
            $article = $articleRepository->find($id);
            $mode = "article";
        }
        if($route == "comment_like"){
            $comment =  $commentRepository->find($id);
            $mode= "comment";
        }


        $search = [
            "author"=>$user
        ];
        if($mode == "article"){
            $search["article"]=$article;
        }
        if($mode == "comment"){
            $search["comment"]=$comment;
        }

        $like = $likeRepository->findOneBy($search);

        if(!$like){
            $like = new Like();
            $like->setAuthor($user);
            if($mode=="nem"){
                $like->setArticle($article);
            }
            if($mode=="comment"){
                $like->setComment($comment);
            }


            $manager->persist($like);
            $isLiked = true;

        }else{
            $manager->remove($like);
            $isLiked = false;
        }

        $manager->flush();


        if($mode=="article")
        {
            $countSearch= [
                "article"=>$article
            ];
        }

        if($mode=="comment")
        {
            $countSearch= [
                "comment"=>$comment
            ];
        }

        $count = $likeRepository->count($countSearch);
        $data = [
            "isLiked"=>$isLiked,
            "count"=>$count
        ];
        return $this->json($data, 200);
    }
}
