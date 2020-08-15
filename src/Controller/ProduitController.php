<?php

namespace App\Controller;

use App\Entity\ContenuPanier;
use App\Entity\Produit;
use App\Form\ContenuPanierType;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="produit_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if($form->isSubmitted() && $form->isValid()){

            $photoFile = $form->get('photo')->getData();
            
            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('upload_dir'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Impossible d\'uploader la photo');
                }

                $produit->setPhoto($newFilename);
            }

            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Produit sauvegardé');
            return $this->redirectToRoute('produit_index');
        }
    }

    /**
     * @Route("/produit/{id}", name="produit_show", methods={"GET"})
     */
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/produit/edit/{id}", name="produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if($form->isSubmitted() && $form->isValid()){

            $photoFile = $form->get('photo')->getData();
            
            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('upload_dir'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Impossible d\'uploader la photo');
                }

                $produit->setPhoto($newFilename);
            }

            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Produit sauvegardé');
      
            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/produit/delete/{id}", name="produit_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Produit $produit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_index');
    }

     /**
     * @Route("/produit/{id}", name="produit_add")
     * Fiche produit
     */
    public function produit(Request $request, Produit $produit, TranslatorInterface $t)
    {
       
            $contenu_panier = new ContenuPanier();
            $form = $this->createForm(ContenuPanierType::class, $contenu_panier);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                // Je force le produit à ajouter au panier
                $contenu_panier->addProduit($produit);

                $em->persist($contenu_panier);
                $em->flush();
    
                $this->addFlash('success', $t->trans('products.added_cart'));
            }

            return $this->render('produit/produit.html.twig', [
                'produit'      => $produit,
                'ajout_produit' => $form->createView()
            ]);
        
       
    }

}
