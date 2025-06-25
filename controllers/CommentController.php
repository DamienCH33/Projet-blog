<?php

class CommentController  extends AbstractController
{
    /**
     * Ajoute un commentaire.
     * @return void
     */
    public function addComment(): void
    {
        // Récupération des données du formulaire.
        $pseudo = Utils::request("pseudo");
        $content = Utils::request("content");
        $idArticle = Utils::request("idArticle");

        // On vérifie que les données sont valides.
        if (empty($pseudo) || empty($content) || empty($idArticle)) {
            throw new Exception("Tous les champs sont obligatoires. 3");
        }

        // On vérifie que l'article existe.
        $articleManager = new ArticleManager();
        $article = $articleManager->getArticleById($idArticle);
        if (!$article) {
            throw new Exception("L'article demandé n'existe pas.");
        }

        // On crée l'objet Comment.
        $comment = new Comment([
            'pseudo' => $pseudo,
            'content' => $content,
            'idArticle' => $idArticle
        ]);

        // On ajoute le commentaire.
        $commentManager = new CommentManager();
        $result = $commentManager->addComment($comment);

        // On vérifie que l'ajout a bien fonctionné.
        if (!$result) {
            throw new Exception("Une erreur est survenue lors de l'ajout du commentaire.");
        }

        // On redirige vers la page de l'article.
        Utils::redirect("showArticle", ['id' => $idArticle]);
    }

    /**
     * Gestion des commentaires d'un article.
     * @return void
     */
    public function showComments(): void
    {
        $this->checkIfUserIsConnected();

        $id = Utils::request("id", -1);

        $articleManager = new ArticleManager();
        $articleData = $articleManager->getArticleWithComments($id);

        if ($articleData === null) {
            throw new Exception("Article non trouvé.");
        }

        $view = new View("Edition d'un article");
        $view->render("admin/comments", [
            'article' => $articleData['article'],
            'comments' => $articleData['comments'],
        ]);
    }

    /**
     * Suppression d'un commentaire.
     * @return void
     */
    public function deleteComment(): void
    {
        $this->checkIfUserIsConnected();

        $id = Utils::request('id', -1);

        $commentManager = new CommentManager();
        $commentManager->deleteCommentById((int) $id);

        Utils::redirect('admin');
    }
    /**
     * Supprime plusieurs commentaires sélectionnés.
     * @return void
     */
    public function deleteSelectedComments(): void
    {
        $this->checkIfUserIsConnected();

        $commentIds = $_POST['comments_to_delete'] ?? [];

        if (!empty($commentIds) && is_array($commentIds)) {
            $commentManager = new CommentManager();

            foreach ($commentIds as $id) {
                $commentManager->deleteCommentById((int) $id);
            }

            $idArticle = Utils::request("id", -1);

            Utils::redirect('comments', ['id' => $idArticle]);
        } else {
            throw new Exception("Aucun commentaire sélectionné.");
        }
    }
}
