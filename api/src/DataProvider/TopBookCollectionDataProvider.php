<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ArrayPaginator;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\TopBook;
use App\Repository\TopBook\TopBookDataInterface;

final class TopBookCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private TopBookDataInterface $repository;
    private array $collection;
    private array $context;

    public function __construct(TopBookDataInterface $repository)
    {
        $this->repository = $repository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return TopBook::class === $resourceClass;
    }

    /**
     * @throws \RuntimeException
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $this->context = $context;
        try {
            $this->collection = $this->repository->getTopBooks();
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Unable to retrieve top books from external source: %s', $e->getMessage()));
        }

        return new ArrayPaginator($this->collection, $this->getOffset(), $this->getItemsPerPage());
    }

    public function getLastPage(): int
    {
        return (int) ceil(($this->getTotalItems() / $this->getItemsPerPage()));
    }

    public function getTotalItems(): int
    {
        return count($this->collection);
    }

    private function getOffset(): int
    {
        return ($this->getCurrentPage() - 1) * $this->getItemsPerPage();
    }

    public function getCurrentPage(): int
    {
        $page = (int) ($this->context['filters']['page'] ?? 1);
        $page = $page < 1 || $page > $this->getLastPage() ? 1 : $page;

        return $page;
    }

    public function getItemsPerPage(): int
    {
        return 30;
    }
}
