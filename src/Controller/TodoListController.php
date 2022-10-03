<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Form\TodoListType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TodoListController extends AbstractController
{
    #[Route('/create-list', name: 'create-list')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $todoList = new TodoList;

        $form = $this->createForm(TodoListType::class, $todoList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($todoList);
            $em->flush();
            return $this->redirectToRoute("read_all");
        }
        return $this->render("todo_list/create.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route("/", name: "read_all")]
    public function readAll(Request $request, ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(TodoList::class);
        $lists = $repository->findAll();

        return $this->render("todo_list/index.html.twig", [
            "lists" => $lists,
        ]);
    }

    #[Route('/update-list/{id}', name: 'update-list')]
    public function update(TodoList $list, Request $request, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(TodoListType::class, $list);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute("read_All");
        }
        return $this->render("todo_list/create.html.twig", [
            "form" => $form->createView(),
            "list" => $list
        ]);
    }

    #[Route('/delete-list/{id}', name: 'delete-list')]
    public function delete(TodoList $list, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();;
        $em->remove($list);
        $em->flush();

        return $this->redirectToRoute("read_all");
    }
}
