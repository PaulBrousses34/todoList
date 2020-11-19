<?php

namespace App\Controller;

use App\Model\TodoModel;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TodoController extends AbstractController
{
    /**
     * Liste des tâches
     *
     * @Route("/todos", name="todo_list", methods={"GET"})
     */
    public function todoList()
    {
        $todos = TodoModel::findAll();

        return $this->render('todo/list.html.twig', [
            'todos' => $todos,
        ]);
    }

    /**
     * Affichage d'une tâche
     *
     * @Route("/todo/{id}", name="todo_show", requirements={"id" = "\d+"}, methods={"GET"})
     */
    public function todoShow($id)
    {
        $todo = TodoModel::find($id);

        if ($todo === false) {
            throw $this->createNotFoundException('Cette tâche n\'existe pas.');
        }

        return $this->render('todo/single.html.twig', [
            'todo' => $todo
        ]);
    }

    /**
     * Changement de statut
     *
     * @Route(
     *      "/todo/{id}/{status}",
     *      name="todo_set_status",
     *      requirements={
     *          "id": "\d+",
     *          "status": "(un)?done" 
     *      },
     *      methods={"POST"}
     *      )
     * 
     * Pour le requirement sur le status, différentes RegEx sont possibles : (un)?done, done|undone
     */
    public function todoSetStatus($id, $status)
    {
        // setStatus retourne soit true soit false, donc on peut tester sa valeur dans un if
        if (!TodoModel::setStatus($id, $status)) {
            // Comme pour la route pour voir les détails dûne tâche, on peut afficher une 404
            // throw $this->createNotFoundException('Cette tâche n\'existe pas.');
            // …mais ça serait quand même mieux d'avoir un message flash pour annoncer une erreur
            $this->addFlash('warning', 'Un problème est survenu, il semblerait que la tâche n\'existe pas !');
        } else {
            // On ajoute un message flash à la session
            $this->addFlash('success', 'La tâche a bien été modifiée.');
        }

        // On redirige l'utilisateur sur la liste des tâches
        return $this->redirectToRoute('todo_list'); 
    }

    /**
     * Ajout d'une tâche
     *
     * @Route("/todo/add", name="todo_add", methods={"POST"})
     *
     * La route est définie en POST parce qu'on veut ajouter une ressource sur le serveur
     */
    public function todoAdd(Request $request)
    {
        // On utilise l'objet $request pour récupérer la valeur de lînput "task"
        $task = $request->request->get('task', '');

        // On supprime les espaces au début et en fin de chaine de caractés sîl y en a
        $task = trim($task);

        // On va faire des vérifications avant d'ajouter la tâche
        // On veut s'assurer que la tâche est bien reçu
        // (c'est fait avec le getter qui met une valeur par défaut si on ne reçoit pas de champ task)
        // On peut ensuite tester si la chaine est vide
        if (empty($task)) {
            // On annonce qu'on n'a pas reçu une tâche valide
            $this->addFlash('warning', 'Aucune tâche valide n\'a été entrée.');

        } else {
            // On peut ajouter la tâche à notre liste
            TodoModel::add($task);
    
            // On ajoute un message flash à la session
            $this->addFlash('success', 'La tâche '. $task. ' a bien été ajoutée.');
        }

        // On redirige l'utilisateur sur la liste des tâches
        return $this->redirectToRoute('todo_list');
    }

    /**
     * @Route("/todo/{id}/delete", name="todo_delete", requirements={"id" = "\d+"}, methods={"DELETE"})
     */
    public function delete($id)
    {
        if (TodoModel::delete($id)) {
            // On ajoute un message flash à la session
            $this->addFlash('success', 'La tâche a bien été supprimée.');
        } else {
            $this->addFlash('warning', 'Un problème est survenu, il semblerait que la tâche n\'existe pas !');
        }


        // On redirige l'utilisateur sur la liste des tâches
        return $this->redirectToRoute('todo_list');
    }

    /**
     * @Route("/todo/dev/reset", name="todo_dev_reset")
     */
    public function reset()
    {
        // On exécute la méthode reset() seulement si on est en environnement de dev
        if ($_ENV['APP_ENV'] == 'dev') {
            // On veut réinitialiser la liste des tâches
            TodoModel::reset();
        }

        // On redirige vers la liste des tâches
        return $this->redirectToRoute('todo_list');
    }
}
