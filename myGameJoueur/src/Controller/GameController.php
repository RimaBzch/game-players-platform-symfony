<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Game;
use App\Entity\Image;
use App\Entity\Joueur;
use Symfony\Component\HttpFoundation\RequestStack;

class GameController extends AbstractController
{   private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    #[Route('/game', name: 'app_game')]
    public function index(): Response
    {   
        $entityManager = $this->getDoctrine()->getManager();
        $game= new Game();
        $game->setTitre('Hogwarts Legacy');
        $game->setType('Fiction');
        $game->setNbrJoueur('3');
        $game->setEditeur('Hogwarts');
        $imagePath = '/myGameJoueur/public/assets/images/hogwarts.jpg';
        $baseUrl = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();
        $imageUrl = $baseUrl . '/' . $imagePath;   
         $image = new Image();
        $image->setUrl($imageUrl);
        $image->setAlt('Game');
        $game->setImage($image);
        // $joueur = new Joueur();
        // $joueur->setNom('Olivia');
        // $joueur->setEmail('Olivia@gmail.com');
        // $joueur->setBornAt(new \DateTime("8-11-2000"));
        // $joueur->setScore(0);
        // $imagePath = 'GameJoueur/public/assets/images/player4.jpg';
        // $baseUrl = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();
        // $imageUrl = $baseUrl . '/' . $imagePath;   
        // $image = new Image();
        // $image->setUrl($imageUrl);

        // $image->setAlt('joueur');
        // $joueur->setImage($image);
        // //  $joueurController = $this->get(JoueurController::class);
        // //$joueur = $joueurController->index();
        // $joueur->setGame($game);      

        // $entityManager->persist($joueur);
        $entityManager->persist($game);

        $entityManager->flush();
    
        return $this->render('game/index.html.twig', [
            'id' => $game->getId(),
            'name'=>$game->getTitre(),
            'type'=>$game->getType(),
            'nbrJoueur'=>$game->getNbrJoueur(),
            'editeur'=>$game->getEditeur(),
            'image' => $game->getImage()

        ]);
    }

    
    #[Route('/game/{id}', name: 'show_game')]
    public function show($id)
    { $game = $this->getDoctrine() ->getRepository(Game::class) ->find($id);
        
        
        $em=$this->getDoctrine()->getManager();
        $listJoueur =$em->getRepository(Joueur::class)
            ->findBy(['Game'=>$game]);
        if (!$game) { throw $this->createNotFoundException( 'No Game found for id '.$id );
        }
        return $this->render('game/show.html.twig', [
            'game' =>$game,
            'joueur' =>$listJoueur
        ]);
    }

}
