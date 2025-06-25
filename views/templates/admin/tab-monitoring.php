<?php
$sort = $_GET['sort'] ?? 'views';
$order = $_GET['order'] ?? 'desc';

function sortLink($column, $text, $sort, $order): string
{
    $upArrow = "<a href='/index.php?action=admin&tab=monitoring&sort=$column&order=asc' title='Trier par ordre croissant' style='text-decoration: none; color: " .
                (($sort === $column && $order === 'asc') ? "black" : "grey") . ";'>▲</a>";

    $downArrow = "<a href='/index.php?action=admin&tab=monitoring&sort=$column&order=desc' title='Trier par ordre décroissant' style='text-decoration: none; color: " .
                (($sort === $column && $order === 'desc') ? "black" : "grey") . ";'>▼</a>";

    return "<span class=\"label\">$text</span><span class=\"sort-order\">$upArrow $downArrow</span>";
}
?>

<h2>Tableau de bord des articles</h2>

<div class="adminArticle">
    <table>
        <thead>
            <tr>
                <th><?= sortLink('title', 'Titre', $sort, $order) ?></th>
                <th><?= sortLink('comments_count', 'Nombre commentaires', $sort, $order) ?></th>
                <th><?= sortLink('views', 'Nombre vues', $sort, $order) ?></th>
                <th><?= sortLink('date_creation', 'Date de publication', $sort, $order) ?></th>
                <th class="action">Voir les commentaires</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($articles as $article) { ?>
                <tr>
                    <td><?= htmlspecialchars($article->getTitle()) ?></td>
                    <td><?= $article->getNbComments() ?? 0 ?></td>
                    <td><?= $article->getViews() ?></td>
                    <td><?= $article->getDateCreation()->format('d/m/Y') ?></td>
                    <td class="action">
                        <a href="/index.php?action=comments&id=<?= $article->getId() ?>">Voir</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>