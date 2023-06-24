<?php

namespace App\Controller;

use App\Repository\AnnoncesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(AnnoncesRepository $repoo, PaginatorInterface $paginator, Request $request): Response
    {
        $annonces = $paginator->paginate(
            $repoo->findAllPagination(),/* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            4/*limit per page*/
        );
        return $this->render('home/home.html.twig', [
            'mesannonces' => $annonces
        ]);
    }



    #[Route('annonce/show/{id}', name: 'show')]
    public function show($id, AnnoncesRepository $repo): Response
    {
        $annonce = $repo->find($id);

        return $this->render('home/showAnnonces.html.twig', [
            'annonce' => $annonce
        ]);
    }
}
