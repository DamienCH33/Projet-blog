<?php

/**
 * Cette classe sert à gérer les commentaires. 
 */
class CommentManager extends AbstractEntityManager
{
    /**
     * Ajoute un commentaire.
     * @param Comment $comment : l'objet Comment à ajouter.
     * @return bool : true si l'ajout a réussi, false sinon.
     */
    public function addComment(Comment $comment): bool
    {
        $sql = "INSERT INTO comment (pseudo, content, id_article, date_creation) VALUES (:pseudo, :content, :idArticle, NOW())";
        $result = $this->db->query($sql, [
            'pseudo' => $comment->getPseudo(),
            'content' => $comment->getContent(),
            'idArticle' => $comment->getIdArticle()
        ]);
        return $result->rowCount() > 0;
    }

    /**
     * Supprime un commentaire.
     * @param int $id : l'id du commentaire à supprimer.
     * @return void
     */
    public function deleteCommentById(int $id): void
    {
        $sql = "DELETE FROM comment WHERE id = :id";
        $this->db->query($sql, ['id' => $id]);
    }

}
