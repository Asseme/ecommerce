<?php

namespace App\Controller;

use App\Entity\ContenuPanier;
use App\Form\ContenuPanierType;
use App\Repository\ContenuPanierRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/contenu/panier")
 */
class ContenuPanierController extends AbstractController
{
    /**
     * @Route("/", name="contenu_panier_index", methods={"GET"})
     */
    public function index(ContenuPanierRepository $contenuPanierRepository, SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $contenu_panier = $session->get('contenu_panier', []);

        $contenu_panier_with_data = [];
        foreach($contenu_panier as $id => $quantite){
            $contenu_panier_with_data[] = [
                'produit' => $produitRepository->find($id),
                'quantite' => $quantite

            ];
        }
        // dd($contenu_panier_with_data);
        return $this->render('contenu_panier/index.html.twig', [
            'contenu_paniers' => $contenu_panier_with_data,
        ]);
    }

    /**
     * @Route("/new", name="contenu_panier_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $contenuPanier = new ContenuPanier();
        $form = $this->createForm(ContenuPanierType::class, $contenuPanier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contenuPanier);
            $entityManager->flush();

            return $this->redirectToRoute('contenu_panier_index');
        }

        return $this->render('contenu_panier/new.html.twig', [
            'contenu_panier' => $contenuPanier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="contenu_panier_show", methods={"GET"})
     */
    public function show(ContenuPanier $contenuPanier): Response
    {
        return $this->render('contenu_panier/show.html.twig', [
            'contenu_panier' => $contenuPanier,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="contenu_panier_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ContenuPanier $contenuPanier): Response
    {
        $form = $this->createForm(ContenuPanierType::class, $contenuPanier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('contenu_panier_index');
        }

        return $this->render('contenu_panier/edit.html.twig', [
            'contenu_panier' => $contenuPanier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="contenu_panier_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ContenuPanier $contenuPanier): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contenuPanier->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($contenuPanier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('contenu_panier_index');
    }

    /**
     * @Route("/add/{id}", name="panier_add")
     */
    public function add($id , SessionInterface $session): Response
        {
            $contenu_panier = $session->get('contenu_panier', []);
            if(!empty($contenu_panier[$id])){
                $contenu_panier[$id]++;
            }else {
                $contenu_panier[$id] = 1;
            }
          
            $session->set('contenu_panier', $contenu_panier);
            dd($session->get('contenu_panier'));
            return $this->redirectToRoute('contenu_panier_index');
         }

    
}
