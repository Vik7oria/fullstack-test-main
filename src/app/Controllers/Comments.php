<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CommentModel;
use CodeIgniter\HTTP\ResponseInterface;

class Comments extends BaseController {
    public function __construct(
        protected ?CommentModel $model = null,
    ) {
        $this->model = $model ?? new CommentModel();
    }

    public function index(): string {
        return view('comments');
    }

    public function list(): ResponseInterface {
        $page = max(1, (int)($this->request->getGet('page') ?? 1));
        $perPage = 3;
        $sortBy = $this->request->getGet('sort') ?? 'id';    // 'id' или 'date'
        $sortDir = $this->request->getGet('dir') ?? 'desc';  // 'asc' или 'desc'

        $allowedSort = ['id', 'date'];
        $allowedDir = ['asc', 'desc'];
        if (!in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'id';
        }

        if (!in_array($sortDir, $allowedDir, true)) {
            $sortDir = 'desc';
        }

        $total = $this->model->countAllResults();

        $comments = $this->model
            ->orderBy($sortBy, $sortDir)
            ->findAll($perPage, ($page - 1) * $perPage);

        return $this->response->setJSON([
            'items' => $comments,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => (int)ceil($total / $perPage),
            'sort' => $sortBy,
            'dir' => $sortDir,
        ]);
    }

    public function create(): ResponseInterface {
        $data = [
            'name' => trim((string)$this->request->getPost('name')),
            'text' => trim((string)$this->request->getPost('text')),
            'date' => $this->request->getPost('date'),
        ];

        $date = (string) $this->request->getPost('date');
        if ($date > date('Y-m-d')) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'errors'  => ['date' => 'Дата не может быть в будущем.'],
            ]);
        }

        if (!$this->model->validate($data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'errors' => $this->model->errors(),
            ]);
        }

        $id = $this->model->insert($data);

        return $this->response->setJSON([
            'success' => true,
            'id' => $id,
            'item' => $this->model->find($id),
        ]);
    }

    public function delete(int $id): ResponseInterface {
        if ($id <= 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'id комментария не может быть меньше 1',
            ]);
        }

        $comment = $this->model->find($id);
        if (!$comment) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Комментарий не найден',
            ]);
        }

        $this->model->delete($id);

        return $this->response->setJSON(['success' => true]);
    }
}
