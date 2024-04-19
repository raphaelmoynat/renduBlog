<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Image;
use App\Entity\User;
use App\Form\ImageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/user/avatar/{id}', name:"user_avatar")]
    public function addAvatar(User $user):Response
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);

        return $this->render("user/index.html.twig", [
            "user"=>$user,
            'form'=>$form->createView()
        ]);
    }
}
