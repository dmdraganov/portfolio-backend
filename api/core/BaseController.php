<?php
require_once __DIR__ . '/JsonRepository.php';
require_once __DIR__ . '/Response.php';
require_once __DIR__ . '/Request.php';
require_once __DIR__ . '/Validator.php';

abstract class BaseController
{
    protected JsonRepository $repository;
    protected Validator $validator;
    protected string $resourceName;
    protected string $idPrefix;

    public function __construct(string $jsonFile, string $resourceName, string $idPrefix = '')
    {
        $this->repository = new JsonRepository($jsonFile);
        $this->validator = new Validator();
        $this->resourceName = $resourceName;
        $this->idPrefix = $idPrefix;
    }

    public function getAll(Request $request, Response $response): void
    {
        $response->json($this->repository->findAll());
    }

    public function getById(Request $request, Response $response, array $params): void
    {
        $item = $this->repository->findById($params['id']);
        if ($item) {
            $response->json($item);
        } else {
            $response->error("{$this->resourceName} not found", 404);
        }
    }

    public function create(Request $request, Response $response): void
    {
        $data = $request->getJsonBody();
        if (empty($data)) {
            $response->error('Invalid data: JSON body is empty or malformed.');
            return;
        }

        // Validation would go here

        $newItem = $this->repository->create($data, $this->idPrefix);
        $response->json($newItem, 201);
    }

    public function update(Request $request, Response $response, array $params): void
    {
        $data = $request->getJsonBody();
        if (empty($data)) {
            $response->error('Invalid data: JSON body is empty or malformed.');
            return;
        }

        $updatedItem = $this->repository->update($params['id'], $data);
        if ($updatedItem) {
            $response->json($updatedItem);
        } else {
            $response->error("{$this->resourceName} not found", 404);
        }
    }

    public function partialUpdate(Request $request, Response $response, array $params): void
    {
        $data = $request->getJsonBody();
        if (empty($data)) {
            $response->error('Invalid data: JSON body is empty or malformed.');
            return;
        }

        $updatedItem = $this->repository->partialUpdate($params['id'], $data);
        if ($updatedItem) {
            $response->json($updatedItem);
        } else {
            $response->error("{$this->resourceName} not found", 404);
        }
    }

    public function delete(Request $request, Response $response, array $params): void
    {
        if ($this->repository->delete($params['id'])) {
            $response->noContent();
        } else {
            $response->error("{$this->resourceName} not found", 404);
        }
    }
}
