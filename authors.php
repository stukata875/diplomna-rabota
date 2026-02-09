<?php
$page_title = 'Всички автори';
include 'header.php';
include 'config.php';

/* pagination */
$authors_per_page = 50;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $authors_per_page;

/* count authors - взима от двете таблици */
$total_stmt = $conn->prepare("
    SELECT COUNT(DISTINCT author) as total
    FROM (
        SELECT author FROM books
        WHERE author IS NOT NULL AND author != ''
        UNION
        SELECT author FROM upcoming_books
        WHERE author IS NOT NULL AND author != ''
    ) as all_authors
");
$total_stmt->execute();
$total_authors = $total_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_authors / $authors_per_page);

/* fetch authors - взима от двете таблици и брои общо */
$authors_stmt = $conn->prepare("
    SELECT 
        author,
        ((SELECT COUNT(*) FROM books WHERE books.author = all_authors.author) + 
         (SELECT COUNT(*) FROM upcoming_books WHERE upcoming_books.author = all_authors.author)) as total_books
    FROM (
        SELECT author FROM books
        WHERE author IS NOT NULL AND author != ''
        UNION
        SELECT author FROM upcoming_books
        WHERE author IS NOT NULL AND author != ''
    ) as all_authors
    GROUP BY author
    ORDER BY author ASC
    LIMIT ? OFFSET ?
");
$authors_stmt->bind_param("ii", $authors_per_page, $offset);
$authors_stmt->execute();
$authors_result = $authors_stmt->get_result();
?>

<style>
.page-content-authors {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.page-content-authors h1 {
    font-size: 28px;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e60000;
}

/* top bar */
.authors-top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 15px 0 25px;
    font-size: 14px;
    color: #555;
    gap: 15px;
}

/* pagination */
.pagination-authors {
    display: flex;
    gap: 6px;
}

.pagination-authors a,
.pagination-authors span {
    padding: 6px 10px;
    border: 1px solid #ddd;
    text-decoration: none;
    color: #333;
    font-size: 13px;
}

.pagination-authors a:hover,
.pagination-authors .active {
    background: #e60000;
    color: #fff;
    border-color: #e60000;
}

/* list */
.authors-list {
    columns: 4;
    column-gap: 40px;
}

@media (max-width: 1200px) { .authors-list { columns: 3; } }
@media (max-width: 900px)  { .authors-list { columns: 2; } }
@media (max-width: 600px)  { .authors-list { columns: 1; } }

.authors-list ul {
    list-style: disc;
    padding-left: 18px;
    margin: 0;
}

.authors-list li {
    break-inside: avoid;
    margin-bottom: 8px;
    font-size: 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.author-name {
    flex: 1;
}

.book-count {
    font-size: 13px;
    color: #666;
    background: #f5f5f5;
    padding: 2px 8px;
    border-radius: 10px;
    margin-left: 10px;
    white-space: nowrap;
}

.authors-list a {
    color: #111;
    text-decoration: none;
}

.authors-list a:hover {
    color: #e60000;
    text-decoration: underline;
}

/* Информационен панел */
.info-panel {
    background: #f0f8ff;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    border-left: 4px solid #e60000;
}

.info-panel strong {
    color: #e60000;
}
</style>

<div class="page-content-authors">
    <h1>Всички автори</h1>
    
    
    <div class="authors-top-bar">
        <div>
            Страница <?= $current_page ?> от <?= $total_pages ?> |
            Показване <?= ($offset + 1) ?>–<?= min($offset + $authors_per_page, $total_authors) ?>
            от <?= $total_authors ?> автори
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="pagination-authors">
            <?php if ($current_page > 1): ?>
                <a href="?page=<?= $current_page - 1 ?>">&lsaquo;</a>
            <?php endif; ?>

            <?php 
            // Показваме само 5 страници (2 преди, текущата, 2 след)
            $start = max(1, $current_page - 2);
            $end = min($total_pages, $current_page + 2);
            
            for ($i = $start; $i <= $end; $i++): ?>
                <?php if ($i == $current_page): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?= $current_page + 1 ?>">&rsaquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="authors-list">
        <ul>
            <?php while ($row = $authors_result->fetch_assoc()): ?>
                <li>
                    <div class="author-name">
                        <a href="author_books.php?author=<?= urlencode($row['author']) ?>">
                            <?= htmlspecialchars($row['author']) ?>
                        </a>
                    </div>
                    
                    <?php if ($row['total_books'] > 0): ?>
                        <div class="book-count">
                            <?= $row['total_books'] ?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</div>

<?php
$authors_stmt->close();
$total_stmt->close();
$conn->close();
include 'footer.php';
?>