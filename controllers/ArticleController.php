<?php

class ArticleController
{
    /**
     * Affiche la page d'accueil.
     * @return void
     */
    public function showHome(): void
    {
        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticles();

        $view = new View("Accueil");
        $view->render("home", ['articles' => $articles]);
    }

    /**
     * Affiche le détail d'un article.
     * @return void
     */
    public function showArticle(): void
    {
        $id = (int) Utils::request("id", -1);

        $articleManager = new ArticleManager();
        $result = $articleManager->getArticleWithComments($id);

        if (!$result) {
            throw new Exception("L'article demandé n'existe pas.");
        }

        $articleManager->incrementViews($id);

        $view = new View($result['article']->getTitle());
        $view->render("detailArticle", ['article' => $result['article'], 'comments' => $result['comments']]);
    }

    /**
     * Affiche le formulaire d'ajout d'un article.
     * @return void
     */
    public function addArticle(): void
    {
        $view = new View("Ajouter un article");
        $view->render("addArticle");
    }

    /**
     * Affiche la page "à propos".
     * @return void
     */
    public function showApropos()
    {
        $view = new View("A propos");
        $view->render("apropos");
    }
}
