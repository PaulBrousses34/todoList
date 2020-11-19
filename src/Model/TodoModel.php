<?php

namespace App\Model;

use Symfony\Component\HttpFoundation\Session\Session;

class TodoModel
{
    private static $session = null;

    private static $initTodos = [
        [
            'task' => 'Installer Symfony',
            'status' => 'done',
            'created_at' => '2017-05-12 14:12:05',
        ],
        [
            'task' => 'Comprendre le Routing',
            'status' => 'undone',
            'created_at' => '2017-06-24 09:52:31',
        ],
        [
            'task' => 'Comprendre les Contrôleurs',
            'status' => 'undone',
            'created_at' => '2017-07-02 18:36:45',
        ],
    ];

    /**
     * 1. Initialise la session si non active
     * 2. Attribuer les todos par défaut si les données sont vides
     */
    private static function checkSession()
    {
        // La session est-elle créee au sein de ma classe ?
        if (self::$session == null) {
            self::$session = new Session();
        }
        // Des données existent-elles
        if (self::$session->get('todos', false) === false) {
            // Si vide, on initialise
            self::$session->set('todos', self::$initTodos);
        }
    }

    private static function setTodos($todos)
    {
        self::checkSession();
        self::$session->set('todos', $todos);
    }

    private static function getTodos()
    {
        self::checkSession();
        return self::$session->get('todos');
    }

    public static function find($id)
    {
        // On récupère les tâches
        $tasks = self::getTodos();
        // On vérifie l'existence de cette tâche
        if (isset($tasks[$id])) {
            return $tasks[$id];
        }
        // Sinon on renvoie false
        return false;
    }

    public static function setStatus($id, $status)
    {
        // On récupère les tâches
        $tasks = self::getTodos();
        // On vérifie l'existence de cette tâche
        if (isset($tasks[$id])) {
            // Si oui on modifie le statut
            $tasks[$id]['status'] = $status;
            // On écrase les todos
            self::setTodos($tasks);
            return true;
        }
        // Sinon on renvoie false
        return false;
    }

    /**
     * Retourne true (si delete ok) ou false (si tâche non trouvée)
     */
    public static function delete($id)
    {
        // On récupère les tâches
        $tasks = self::getTodos();
        // On vérifie l'existence de cette tâche
        if (isset($tasks[$id])) {
            // Si oui on supprime à l'index $id
            unset($tasks[$id]);
            // On écrase les todos
            self::setTodos($tasks);
            return true;
        }
        // Sinon on renvoie false
        return false;
    }

    public static function findAll()
    {
        return self::getTodos();
    }

    public static function add($title)
    {
        // Date courante
        $date = new \DateTime();

        // Nouvelle tâche à créer
        $task = [
            'task' => $title,
            'status' => 'undone',
            'created_at' => $date->format('Y-d-m H:i:s'),
        ];

        // On récupère les todos dans un tableau temporaire
        // (on utilise getTodos pour gérer la session)
        $todos = self::getTodos();
        // On ajoute la tâche à la liste des tâches
        $todos[] = $task;

        // On écrase les todos
        self::setTodos($todos);
    }

    public static function reset()
    {
        // Avant d'utiliser la variable $session
        // Il faut s'assurer que cette variable contient bien un objet Session dedans
        self::checkSession();

        // Ensuite on réinitiliase la liste des todos en écrasant la valeurs todos dans la session
        // avec le tableau initial des tâches
        self::$session->set('todos', self::$initTodos);
    }
}
