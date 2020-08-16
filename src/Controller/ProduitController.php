<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    public function new(Request $request,TranslatorInterface $translator): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if($form->isSubmitted() && $form->isValid()){

            $photoFile = $form->get('photo')->getData();

            // Si un fichier a été uploadé
            if ($photoFile) {
                // On le renomme
                $newFilename = uniqid().'.'.$photoFile->guessExtension();

                // On essaie de le déplacer sur le serveur
                try {
                    $photoFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Impossible d\'uploader la photo');
                }

                // On met àjour l'objet avec le bon nom de fichier
                $produit->setPhoto($newFilename);
            }


            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', $translator->trans('Produit.ajoute'));
        }
        return $this->render('produit/new.html.twig', [	
            'produit' => $produit,	
            'form' => $form->createView(),	
        ]);
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
    public function edit(Request $request, Produit $produit,TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
         
        if($form->isSubmitted() && $form->isValid()){

            $photoFile = $form->get('photo')->getData();

            // Si un fichier a été uploadé
            if ($photoFile) {
                // On le renomme
                $newFilename = uniqid().'.'.$photoFile->guessExtension();

                // On essaie de le déplacer sur le serveur
                try {
                    $photoFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Impossible d\'uploader la photo');
                }

                // On met àjour l'objet avec le bon nom de fichier
                $produit->setPhoto($newFilename);
            }


            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $translator->trans('Produit.modifie'));
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

    

}