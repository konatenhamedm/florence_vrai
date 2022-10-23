<?php

namespace App\Service;

use App\Entity\Groupe;
use App\Entity\Parametre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class Services
{

    private $em;
    private $route;
    private $container;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack, RouterInterface $router)
    {
        $this->em = $em;
        if ($requestStack->getCurrentRequest()) {
            $this->route = $requestStack->getCurrentRequest()->attributes->get('_route');
            $this->container = $router->getRouteCollection()->all();
        }
        
    }

    public function getRoute()
    {

        return $this->route;
    }

    public function listeModule()
    {
        $repo = $this->em->getRepository(Groupe::class)->afficheModule();
        return $repo;
    }

    public function findParametre()
    {
        $repo = $this->em->getRepository(Parametre::class)->findParametre();
        return $repo;
    }

    public function liste()
    {
        $repo = $this->em->getRepository(Groupe::class)->afficheGroupes();

        return $repo;
    }

    public function listeParent()
    {
        $repo = $this->em->getRepository(Groupe::class)->affiche();

        return $repo;
    }

    public function listeLien()
    {
        $array = [
            'module'=>'module',
            'agenda'=>'agenda',
            'groupe'=>'groupe',
            'app_utilisateur_user_groupe_index'=>'user groupe',
            'parent'=>'parent',
            'parametre'=>'parametre',
            'user'=>'user',
            'produit'=>'produit',
            'chambre'=>'chambre',
            'calendar'=>'calendar',
            'partenaire'=>'partenaire',
            'marque'=>'marque',
        ];
    
        return $array ;
    }

}