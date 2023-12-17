<?php

namespace App\Controller;

use App\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Joueur;
use App\Entity\Image;
use DateTime;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Form\JoueurType;
use Symfony\Component\HttpFoundation\Request;





class JoueurController extends AbstractController
{   private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    #[Route('/joueur/join/{gameId}', name: 'app_joueur')]
    public function index($gameId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $game = $entityManager->getRepository(Game::class)->find($gameId);
        $joueur= new Joueur();
        $joueur->setNom('Elsa');
        $joueur->setBornAt(new DateTime('8-11-1989'));
        $joueur->setEmail('Elsa@gmail.com');
        $joueur->setScore(10);   
        $joueur->setGame($game);
        
        $imagePath = '/myGameJoueur/public/assets/images/elsa.jpg';
        $baseUrl = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();
        $imageUrl = $baseUrl . '/' . $imagePath;   
        $image = new Image();
        $image->setUrl($imageUrl);
        $image->setAlt('player');
        $joueur->setImage($image);

        $entityManager->persist($joueur);

        $entityManager->flush();
        
        return $this->render('joueur/index.html.twig', [
            'id' => $joueur->getId(),
            'nom'=>$joueur->getNom(),
            'born_at'=> $joueur->getBornAt()->format('Y-m-d'),
            'email'=>$joueur->getEmail(),
            'score'=>$joueur->getScore(),
            'image' => $joueur->getImage()
        ]);
    }
    #[Route('/joueur/{id}', name: 'show_player')]
    public function show($id, Request $request) 
    {    $joueur = $this->getDoctrine() ->getRepository(Joueur::class) ->find($id);
        $publicPath = $request->getScheme().'://'.$request->getHttpHost().$request->getBasePath().'/uploads/players/';
            if (!$joueur) { throw $this->createNotFoundException( 'No player found for id '.$id );
         } 
     return $this->render('joueur/show.html.twig', ['publicPath'=>$publicPath, 'joueur' =>$joueur ]);
     }
     #[Route('/joueurs', name: 'find_all')]
     public function findAll(Request $request) 
     {   $entityManager = $this->getDoctrine()->getManager();
 $repo= $entityManager->getRepository(Joueur::class);
        $joueurs = $repo->findAll();
        $publicPath = $request->getScheme().'://'.$request->getHttpHost().$request->getBasePath().'/uploads/players/';

      return $this->render('joueur/all.html.twig', [ 'publicPath'=>$publicPath,'joueurs' =>$joueurs ]);
      }
 

     #[Route('/ajouter', name: 'Ajouter')]
public function ajouter(Request $request)
{ $publicPath ="uploads/players/";
    $joueur = new Joueur();
   

    $form = $this -> createForm("App\Form\JoueurType",$joueur);
    $form->handleRequest($request);   
    if($form->isSubmitted())
    {
     /*
      * @var UploadedFile $image
      */

      $image = $form->get('Image')->getData();

    $em=$this->getDoctrine()->getManager();
    if($image){
        $imageName = $joueur->getNom().'.'. $image->guessExtension();
        $image->move($publicPath,$imageName);
        $joueur->setImage($imageName);
    }
    $em->persist($joueur);
    $em->flush();
return $this->redirectToRoute('find_all');

}

    return $this->render('joueur/ajouter.html.twig',
    ['f'=> $form->createView()]);
}
}
