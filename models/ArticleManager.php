<?php

/**
 * Classe qui gère les articles.
 */
class ArticleManager extends AbstractEntityManager
{
    /**
     * Récupère tous les articles.
     * @return array : un tableau d'objets Article.
     */
    public function getAllArticles(): array
    {
        $sql = "
    SELECT a.id, a.id_user, a.title, a.content, a.date_creation, a.date_update, a.views,
    COUNT(c.id) AS comments_count
    FROM article a
    LEFT JOIN comment c ON c.id_article = a.id
    GROUP BY a.id, a.id_user, a.title, a.content, a.date_creation, a.date_update, a.views
    ";

        $stmt = $this->db->query($sql);
        $articles = [];

        while ($row = $stmt->fetch()) {
            $article = new Article($row);
            $article->setNbComments((int)$row['comments_count']);
            $articles[] = $article;
        }

        return $articles;
    }

    public function getArticleWithComments(int $idArticle): ?array
    {
        $sql = "
            SELECT
                a.id AS article_id,
                a.id_user,
                a.title,
                a.content AS article_content,
                a.date_creation AS article_date_creation,
                a.date_update,
                a.views,
                c.id AS comment_id,
                c.pseudo,
                c.content AS comment_content,
                c.date_creation AS comment_date_creation
            FROM article a
            LEFT JOIN comment c ON a.id = c.id_article
            WHERE a.id = $idArticle
            ORDER BY c.date_creation ASC
    ";

        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll();

        if (!$rows) {
            return null;
        }

        $articleData = [
            'id' => $rows[0]['article_id'],
            'idUser' => $rows[0]['id_user'],
            'title' => $rows[0]['title'],
            'content' => $rows[0]['article_content'],
            'dateCreation' => $rows[0]['article_date_creation'],
            'dateUpdate' => $rows[0]['date_update'],
            'views' => $rows[0]['views']
        ];
        $article = new Article($articleData);

        // Parcourir les lignes pour récupérer les commentaires
        $comments = [];
        foreach ($rows as $row) {
            if ($row['comment_id'] !== null) { // il y a un commentaire
                $commentData = [
                    'id' => $row['comment_id'],
                    'pseudo' => $row['pseudo'],
                    'content' => $row['comment_content'],
                    'dateCreation' => $row['comment_date_creation'],
                    'idArticle' => $article->getId()
                ];
                $comments[] = new Comment($commentData);
            }
        }

        return ['article' => $article, 'comments' => $comments];
    }

    /**
     * Récupère un article par son id.
     * @param int $id : l'id de l'article.
     * @return Article|null : un objet Article ou null si l'article n'existe pas.
     */
    public function getArticleById(int $id): ?Article
    {
        $sql = "SELECT * FROM article WHERE id = :id";
        $result = $this->db->query($sql, ['id' => $id]);
        $article = $result->fetch();
        if ($article) {
            return new Article($article);
        }
        return null;
    }
    /**
     * Incremente le nombre de vue pour un article donné.
     * @param int $id
     * @return void
     */
    public function incrementViews(int $id): void
    {
        $sql = "UPDATE article SET views = views + 1 WHERE id = :id";
        $this->db->query($sql, ['id' => $id]);
    }
    /**
     * Ajoute ou modifie un article.
     * On sait si l'article est un nouvel article car son id sera -1.
     * @param Article $article : l'article à ajouter ou modifier.
     * @return void
     */
    public function addOrUpdateArticle(Article $article): void
    {
        if ($article->getId() == -1) {
            $this->addArticle($article);
        } else {
            $this->updateArticle($article);
        }
    }

    /**
     * Ajoute un article.
     * @param Article $article : l'article à ajouter.
     * @return void
     */
    public function addArticle(Article $article): void
    {
        $sql = "INSERT INTO article (id_user, title, content, date_creation) VALUES (:id_user, :title, :content, NOW())";
        $this->db->query($sql, [
            'id_user' => $article->getIdUser(),
            'title' => $article->getTitle(),
            'content' => $article->getContent()
        ]);
    }

    /**
     * Modifie un article.
     * @param Article $article : l'article à modifier.
     * @return void
     */
    public function updateArticle(Article $article): void
    {
        $sql = "UPDATE article SET title = :title, content = :content, date_update = NOW() WHERE id = :id";
        $this->db->query($sql, [
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'id' => $article->getId()
        ]);
    }

    /**
     * Supprime un article.
     * @param int $id : l'id de l'article à supprimer.
     * @return void
     */
    public function deleteArticle(int $id): void
    {
        $sql = "DELETE FROM article WHERE id = :id";
        $this->db->query($sql, ['id' => $id]);
    }
}
