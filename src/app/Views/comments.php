<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Комментарии</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="<?= base_url('css/comments.css') ?>">
</head>
<body>
<div class="container py-4">

    <h1 class="mb-4">Комментарии</h1>

    <div class="row sort-block">
        <div class="col-md-4 mb-2">
            <label for="sort">Сортировать по:</label>
            <select id="sort" class="form-control">
                <option value="id" selected>ID</option>
                <option value="date">Дате</option>
            </select>
        </div>
        <div class="col-md-4 mb-2">
            <label for="dir">Направление:</label>
            <select id="dir" class="form-control">
                <option value="desc" selected>По убыванию</option>
                <option value="asc">По возрастанию</option>
            </select>
        </div>
    </div>

    <div id="comments-list">

    </div>

    <nav class="pagination-block">
        <ul class="pagination" id="pagination"></ul>
    </nav>

    <hr>

    <h3 class="mb-3">Добавить комментарий</h3>
    <form id="comment-form" novalidate>
        <div class="form-group">
            <label for="name">Email</label>
            <input type="email" class="form-control" id="name" name="name"
                   placeholder="you@example.com" required>
            <div class="error" id="name-error"></div>
        </div>

        <div class="form-group">
            <label for="date">Дата</label>
            <input type="date" class="form-control" id="date" name="date"
                   max="<?= date('Y-m-d') ?>" required>
            <div class="error" id="date-error"></div>
        </div>

        <div class="form-group">
            <label for="text">Комментарий</label>
            <textarea class="form-control" id="text" name="text" rows="3" required></textarea>
            <div class="error" id="text-error"></div>
        </div>

        <button type="submit" class="btn btn-primary">Отправить</button>
    </form>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?= base_url('js/comments.js') ?>"></script>
</body>
</html>