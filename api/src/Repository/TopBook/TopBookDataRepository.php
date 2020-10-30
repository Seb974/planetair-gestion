<?php

declare(strict_types=1);

namespace App\Repository\TopBook;

use App\Entity\TopBook;
use Symfony\Component\HttpKernel\KernelInterface;

final class TopBookDataRepository implements TopBookDataInterface
{
    private const DATA_SOURCE = 'top-100-novel-sci-fi-fr.csv';
    private const FIELDS_COUNT = 5;

    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function getTopBooks(): array
    {
        return $this->getFromCsv();
    }

    /**
     * Be careful that the file is a simple csv file without "enclosure". That means
     * a field can't contain a ";" or this would add an extra column to the row.
     * Consider using a more robust library like csv reader from the PHP league.
     *
     * @see https://csv.thephpleague.com
     */
    public function getFromCsv(): array
    {
        foreach ($this->getFileAsArray() as $line) {
            $data[] = str_getcsv($line, ';');
        }

        $cpt = 0;
        foreach ($data ?? [] as $row) {
            if (1 === ++$cpt) {
                continue;
            }
            if (self::FIELDS_COUNT !== count($row)) {
                throw new \RuntimeException(sprintf('Invalid data at row: %d', count($row)));
            }
            $topBooks[$cpt - 1] = (new TopBook())
                ->setId($cpt - 1)
                ->setTitle($this->sanitize($row[0] ?? ''))
                ->setAuthor($this->sanitize($row[1] ?? ''))
                ->setPart($this->sanitize($row[2] ?? ''))
                ->setPlace($this->sanitize($row[3] ?? ''))
                ->setBorrowCount((int) ($row[4] ?? 0));
        }

        return $topBooks ?? [];
    }

    private function getFileAsArray(): array
    {
        $csvFileName = $this->kernel->getProjectDir().'/data/'.self::DATA_SOURCE;
        if (!is_file($csvFileName)) {
            throw new \RuntimeException(sprintf("Can't find data source: %s", $csvFileName));
        }
        $file = file($csvFileName);
        if (!is_array($file)) {
            throw new \RuntimeException(sprintf("Can't load data source: %s", $csvFileName));
        }

        return $file;
    }

    /**
     * The CSV file is a "ISO-8859-1" encoded file with French accents.
     */
    private function sanitize(?string $str): string
    {
        return trim(utf8_encode((string) $str));
    }
}