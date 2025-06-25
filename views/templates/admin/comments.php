<?php include_once '_navbar.php'; ?>

<h2>Commentaires de l'article "<?= htmlspecialchars($article->getTitle()) ?>" :</h2>

<?php if (!empty($comments)) { ?>
    <form method="POST" action="/index.php?action=deleteSelectedComments">
        <input type="hidden" name="id" value="<?= $article->getId() ?>">

        <ul class="commentList">

            <li class="commentItem commentBulkDelete">
                <div class="commentContentWrapper">
                    <button
                        type="submit"
                        class="deleteSelectedBtn"
                        onclick="return confirm('Voulez-vous vraiment supprimer les commentaires sélectionnés ?');">
                        Supprimer la sélection
                    </button>
                </div>
            </li>

            <?php foreach ($comments as $comment) { ?>
                <li class="commentItem">
                    <div class="commentContentWrapper">
                        <input
                            type="checkbox"
                            name="comments_to_delete[]"
                            value="<?= $comment->getId() ?>"
                            class="commentCheckbox">

                        <div class="commentText">
                            <span class="commentPseudo"><strong><?= htmlspecialchars($comment->getPseudo()) ?> :</strong></span><br>
                            <span class="commentContent"><?= nl2br(htmlspecialchars($comment->getContent())) ?></span><br>
                            <span class="commentDate">(le <?= $comment->getDateCreation()->format('d/m/Y H:i') ?>)</span>
                        </div>
                    </div>

                    <a
                        class="commentDelete"
                        href="/index.php?action=deleteComment&id=<?= $comment->getId() ?>"
                        onclick="return confirm('Voulez-vous vraiment supprimer ce commentaire ?');">
                        Supprimer
                    </a>
                </li>
            <?php } ?>
        </ul>
    </form>
<?php } else { ?>
    <p>Aucun commentaire pour cet article.</p>
<?php } ?>