<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\AnnoncesType;
use App\Repository\AnnoncesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MesAnnoncesController extends AbstractController
{
    #[Route('/utilisateurs/mes-annonces', name: 'mes_annonces')]
    public function mesAnnonces(AnnoncesRepository $repo,PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();
        $mesAnnonces = $paginator->paginate(
            $repo->findAllPaginationMesannonces($user),/* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3/*limit per page*/
        );
        return $this->render('mes_annonces/mes-annonces.html.twig', [
            'mesannonces' => $mesAnnonces
        ]);
    }

    #[Route('/utilisateurs/mes-annonces/ajouter-annonces', name: 'ajouter_annonces')]
    #[Route('/utilisateurs/mes-annonces/edit/{id}', name: 'modifier_mesannonces')]
    public function AjouterAnnonces(Annonces $annonce, Request $request, EntityManagerInterface $manager): Response
    {
        if ($annonce === null) {
            $annonce = new Annonces;
        }
        $form = $this->createForm(AnnoncesType::class, $annonce);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setCreatedAt(new \DateTimeImmutable());
            $annonce->setNbrVus(35);
            $annonce->setStatus(true);
            $annonce->setUtilisateur($this->getUser());
            $manager->persist($annonce);
            $manager->flush();
            $this->addFlash('success', 'Annonce bien enregistrer');
            return $this->redirectToRoute('ajouter_annonces');
        }
        return $this->render('mes_annonces/ajouterAnnonces.html.twig', [
            'form' => $form->createView()
        ]);
    }



    // Supprimer une annonces 

    #[Route('/utilisateurs/mes-annonces/delete/{id}', name: 'delete_mesannonces')]
    public function DeleteMEsAnnonces(Annonces $annonce, EntityManagerInterface $manager): Response
    {
        $manager->remove($annonce);
        $manager->flush();
        $this->addFlash('success', 'Vous avez bien supprimer une annonces');

        return $this->redirectToRoute('mes_annonces');
    }
}
