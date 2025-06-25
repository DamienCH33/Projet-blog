<?php

/**
 * Contrôleur de la partie admin.
 */

class AdminController extends AbstractController
{
    /**
     * Affiche la page d'administration.
     * @return void
     */
public function showAdmin(): void
{
    $this->checkIfUserIsConnected();

    $sort = $_GET['sort'] ?? 'views';
    $order = strtoupper($_GET['order'] ?? 'DESC');

    $articleManager = new ArticleManager();
    $articles = $articleManager->getAllArticles();

    $allowedSorts = ['views', 'date_creation', 'title', 'comments_count'];
    $allowedOrders = ['ASC', 'DESC'];

    $sort = in_array($sort, $allowedSorts) ? $sort : 'views';
    $order = in_array($order, $allowedOrders) ? $order : 'DESC';

    usort($articles, function($a, $b) use ($sort, $order) {
        switch ($sort) {
            case 'views':
                $valA = $a->getViews();
                $valB = $b->getViews();
                break;
            case 'date_creation':
                $valA = $a->getDateCreation() ? $a->getDateCreation()->getTimestamp() : 0;
                $valB = $b->getDateCreation() ? $b->getDateCreation()->getTimestamp() : 0;
                break;
            case 'title':
                $valA = strtolower($a->getTitle());
                $valB = strtolower($b->getTitle());
                break;
            case 'comments_count':
                $valA = $a->getNbComments();
                $valB = $b->getNbComments();
                break;
            default:
                $valA = 0;
                $valB = 0;
        }

        if ($valA == $valB) {
            return 0;
        }

        if ($order === 'ASC') {
            return ($valA < $valB) ? -1 : 1;
        } else {
            return ($valA > $valB) ? -1 : 1;
        }
    });

    $view = new View("Administration");
    $view->render("admin/admin", [
        'articles' => $articles,
        'sort' => $sort,
        'order' => $order,
    ]);
}

    /**
     * Affichage du formulaire de connexion.
     * @return void
     */
    public function displayConnectionForm(): void
    {
        $view = new View("Connexion");
        $view->render("connectionForm");
    }

    /**
     * Connexion de l'utilisateur.
     * @return void
     */
    public function connectUser(): void
    {
        // On récupère les données du formulaire.
        $login = Utils::request("login");
        $password = Utils::request("password");

        // On vérifie que les données sont valides.
        if (empty($login) || empty($password)) {
            throw new Exception("Tous les champs sont obligatoires. 1");
        }

        // On vérifie que l'utilisateur existe.
        $userManager = new UserManager();
        $user = $userManager->getUserByLogin($login);
        if (!$user) {
            throw new Exception("L'utilisateur demandé n'existe pas.");
        }

        // On vérifie que le mot de passe est correct.
        if (!password_verify($password, $user->getPassword())) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            throw new Exception("Le mot de passe est incorrect : $hash");
        }

        // On connecte l'utilisateur.
        $_SESSION['user'] = $user;
        $_SESSION['idUser'] = $user->getId();

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    /**
     * Déconnexion de l'utilisateur.
     * @return void
     */
    public function disconnectUser(): void
    {
        // On déconnecte l'utilisateur.
        unset($_SESSION['user']);

        // On redirige vers la page d'accueil.
        Utils::redirect("home");
    }

    /**
     * Affichage du formulaire d'ajout d'un article.
     * @return void
     */
    public function showUpdateArticleForm(): void
    {
        $this->checkIfUserIsConnected();

        // On récupère l'id de l'article s'il existe.
        $id = Utils::request("id", -1);

        // On récupère l'article associé.
        $articleManager = new ArticleManager();
        $article = $articleManager->getArticleById($id);

        // Si l'article n'existe pas, on en crée un vide. 
        if (!$article) {
            $article = new Article();
        }

        // On affiche la page de modification de l'article.
        $view = new View("Edition d'un article");
        $view->render("admin/updateArticleForm", [
            'article' => $article
        ]);
    }

    /**
     * Ajout et modification d'un article. 
     * On sait si un article est ajouté car l'id vaut -1.
     * @return void
     */
    public function updateArticle(): void
    {
        $this->checkIfUserIsConnected();

        // On récupère les données du formulaire.
        $id = Utils::request("id", -1);
        $title = Utils::request("title");
        $content = Utils::request("content");

        // On vérifie que les données sont valides.
        if (empty($title) || empty($content)) {
            throw new Exception("Tous les champs sont obligatoires. 2");
        }

        // On crée l'objet Article.
        $article = new Article([
            'id' => $id, // Si l'id vaut -1, l'article sera ajouté. Sinon, il sera modifié.
            'title' => $title,
            'content' => $content,
            'id_user' => $_SESSION['idUser']
        ]);

        // On ajoute l'article.
        $articleManager = new ArticleManager();
        $articleManager->addOrUpdateArticle($article);

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    /**
     * Suppression d'un article.
     * @return void
     */
    public function deleteArticle(): void
    {
        $this->checkIfUserIsConnected();

        $id = Utils::request("id", -1);

        // On supprime l'article.
        $articleManager = new ArticleManager();
        $articleManager->deleteArticle($id);

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }
}
