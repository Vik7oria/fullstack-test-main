$(function () {
    window.APP = {
        listUrl:   '/comments/list',
        createUrl: '/comments/create',
        deleteUrl: '/comments/delete'
    };

    let state = {
        page: 1,
        sort: 'id',
        dir: 'desc'
    };

    function escapeHtml(str) {
        return $('<div>').text(str).html();
    }

    function formatDate(str) {
        if (!str) {
            return '';
        }

        const d = new Date(str.replace(' ', 'T'));

        if (isNaN(d)) {
            return str;
        }
        const pad = n => String(n).padStart(2, '0');
        return `${pad(d.getDate())}.${pad(d.getMonth() + 1)}.${d.getFullYear()} `;
    }

    function loadComments() {
        $.get(window.APP.listUrl, {
            page: state.page,
            sort: state.sort,
            dir:  state.dir
        }, function (res) {
            if (res.items.length === 0 && res.totalPages > 0 && state.page > res.totalPages) {
                state.page = res.totalPages;
                loadComments();
                return;
            }

            if (res.totalPages === 0 && state.page !== 1) {
                state.page = 1;
                loadComments();
                return;
            }

            renderList(res.items);
            renderPagination(res.totalPages, res.page);
        }, 'json');
    }

    function renderList(items) {
        const $list = $('#comments-list').empty();

        if (!items.length) {
            $list.append('<p class="text-muted">Пока нет комментариев.</p>');
            return;
        }

        items.forEach(function (c) {
            const html = `
                <div class="comment" data-id="${c.id}">
                    <div class="meta">
                        <strong>${escapeHtml(c.name)}</strong> &middot; ${formatDate(c.date)}
                    </div>
                    <div class="text">${escapeHtml(c.text)}</div>
                    <button class="btn btn-sm btn-outline-danger mt-2 delete-btn"
                            data-id="${c.id}">Удалить</button>
                </div>`;
            $list.append(html);
        });
    }

    function renderPagination(totalPages, current) {
        const $p = $('#pagination').empty();
        if (totalPages <= 1) {
            return;
        }

        for (let i = 1; i <= totalPages; i++) {
            const active = i === current ? ' active' : '';
            $p.append(
                `<li class="page-item${active}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                 </li>`
            );
        }
    }

    $('#sort').on('change', function () {
        state.sort = $(this).val();
        state.page = 1;
        loadComments();
    });

    $('#dir').on('change', function () {
        state.dir = $(this).val();
        state.page = 1;
        loadComments();
    });

    $(document).on('click', '#pagination .page-link', function (e) {
        e.preventDefault();
        state.page = parseInt($(this).data('page'), 10);
        loadComments();
    });

    $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (!confirm('Удалить комментарий?')) {
            return;
        }

        $.ajax({
            url: window.APP.deleteUrl + '/' + id,
            method: 'DELETE',
            dataType: 'json'
        }).done(function () {
            loadComments();
        }).fail(function (xhr) {
            alert('Ошибка удаления: ' + (xhr.responseJSON?.message || xhr.status));
        });
    });

    $('#comment-form').on('submit', function (e) {
        e.preventDefault();

        $('.error').text('');
        $('#comment-form .form-control').removeClass('is-invalid');

        const name = $('#name').val().trim();
        const text = $('#text').val().trim();
        const date = $('#date').val();

        const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRe.test(name)) {
            $('#name-error').text('Введите корректный email.');
            $('#name').addClass('is-invalid');
            return;
        }
        if (!text) {
            $('#text-error').text('Введите текст комментария.');
            $('#text').addClass('is-invalid');
            return;
        }
        if (!date) {
            $('#date-error').text('Выберите дату.');
            $('#date').addClass('is-invalid');
            return;
        }

        $.ajax({
            url: window.APP.createUrl,
            method: 'POST',
            dataType: 'json',
            data: { name: name, text: text, date: date }
        }).done(function (res) {
            if (res.success) {
                $('#comment-form')[0].reset();
                state.page = 1;
                loadComments();
            }
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                const errors = xhr.responseJSON.errors;
                Object.keys(errors).forEach(function (field) {
                    $('#' + field + '-error').text(errors[field]);
                    $('#' + field).addClass('is-invalid');
                });
            } else {
                alert('Ошибка: ' + xhr.status);
            }
        });
    });

    loadComments();
});