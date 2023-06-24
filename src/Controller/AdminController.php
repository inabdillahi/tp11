<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Categorie;
use App\Entity\Utilisateur;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManager;
use App\Repository\AnnoncesRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateurRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    // Administration 

    #[Route('/utilisateurs/admin', name: 'admin')]
    public function index(AnnoncesRepository $repo,PaginatorInterface $paginator, Request $request): Response
    {
        $annonces = $paginator->paginate(
            $repo->findAllPagination(),/* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            7/*limit per page*/
        );
        return $this->render('admin/admin.html.twig', [
            'annonces' => $annonces
        ]);
    }


    // Supprimer une annonces 

    #[Route('/utilisateurs/admin/annonce/{id}', name: 'delete_annonces')]
    public function DeleteAnnonces(Annonces $annonce, EntityManagerInterface $manager): Response
    {
        $manager->remove($annonce);
        $manager->flush();
        $this->addFlash('success', 'Vous avez bien supprimer une annonces');

        return $this->redirectToRoute('admin');
    }


    //Ajouter - Afficher - Modifier une categorie

    #[Route('/utilisateurs/admin/categorie', name: 'ajouter_categorie')]
    #[Route('/utilisateurs/admin/categorie/edit/{id}', name: 'modifier_categorie')]
    public function categorie(CategorieRepository $repo, Categorie $categorie, Request $request, SluggerInterface $slug, EntityManagerInterface $manager): Response
    {
        $categories = $repo->findAll();
        if ($categorie === null) {
            $categorie = new Categorie;
        }
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $categorie->setSlug(strtolower($slug->slug($categorie->getNom())));
            $manager->persist($categorie);
            $manager->flush();
            $this->addFlash('success', 'Vous avez bien enregistrer');
            return $this->redirectToRoute('ajouter_categorie');
        }
        return $this->render('admin/categorie.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories
        ]);
    }


    //Supprimer une categirie
    #[Route('/utilisateurs/admin/categorie/delete/{id}', name: 'delete_categorie')]
    public function DeleteCategorie(Categorie $categorie, EntityManagerInterface $manager): Response
    {
        $manager->remove($categorie);
        $manager->flush();
        $this->addFlash('success', 'Vous avez bien supprimer');
        return $this->redirectToRoute('ajouter_categorie');
    }



    //Gestions des utilisateurs
    #[Route('/utilisateurs/admin/utilisateurs', name: 'utilisateurs')]
    public function GestionsUtilisateur(UtilisateurRepository $repo): Response
    {
        $users = $repo->findAll();

        return $this->render('admin/utilisateur.html.twig', [
            'users' => $users
        ]);
    }



    //Supprimer une categirie
    #[Route('/utilisateurs/admin/utilisateurs/delete/{id}', name: 'delete_user')]
    public function DeleteUtilisateurs(Utilisateur $user, EntityManagerInterface $manager): Response
    {
        $manager->remove($user);
        $manager->flush();
        $this->addFlash('success', 'Vous avez bien supprimer un utilisateur');
        return $this->redirectToRoute('utilisateurs');
    }
}
