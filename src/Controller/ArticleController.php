<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="app_home", methods="GET")
     */
    public function index(ArticleRepository $repo): Response
    {
        $article = $repo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('article/index.html.twig', [
            'articles' => $article
        ]);
    }

    /**
     * @Route("/article/new", name="app_article_create", methods={"GET", "POST"} )
     */
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            
            $manager->persist($article);
            $manager->flush();

            $this->addFlash('success', 'Article créé avec succès');

            return $this->redirectToRoute('app_home');
        }
        
        return $this->render('article/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/{id<[0-9]+>}", name="app_article_show", methods={"GET"})
     */
    public function show(Article $article)
    {
        return $this->render('article/show.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @Route("/article/{id<[0-9]+>}/edit",name="app_article_edit", methods={"GET", "PUT"})
     */
    public function edit(Article $article,Request $request, EntityManagerInterface $manager)
    {

        $form = $this->createForm(ArticleType::class, $article, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            
            $manager->flush();

            $this->addFlash('success', 'Article modifié avec succès');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    /**
     * @Route("/article/{id<[0-9]+>}/delete",name="app_article_delete", methods="DELETE")
     */
    public function delete(Article $article, Request $request, EntityManagerInterface $manager)
    {
        if ($this->isCsrfTokenValid('article_deletion_' . $article->getId(), $request->request->get('csrf_token'))) {

            $manager->remove($article);
            $manager->flush();    
            
            $this->addFlash('info', 'Article supprimé avec succès');
        }
        
        
        return $this->redirectToRoute('app_home');
    }

}