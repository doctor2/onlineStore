<?php

namespace App\Domain\Product\Service;

use App\Domain\Product\Entity\Product;
use App\Domain\Product\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ImportProduct
{
    private const COLUMN_TITLE_NAME = 'Название товара';
    private const COLUMN_TITLE_DESCRIPTION = 'Описание';
    private const COLUMN_TITLE_PRICE = 'Цена';
    private const COLUMN_TITLE_STOCK_QUANTITY = 'Остаток на складе';
    private const COLUMN_TITLE_CATEGORY = 'Категория';

    public function __construct(private EntityManagerInterface $entityManager, private CategoryRepository $categoryRepository)
    {
    }

    public function import(string $filepath): void
    {
        $spreadsheet = IOFactory::load($filepath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $columnNumbersByTitle = $this->getColumnNumbersByTitle($rows);
        $this->assertAllRequiredColumnsArePresent($columnNumbersByTitle);
        $categoryError = 0;

        // Assuming the first row is the header
        foreach ($rows as $key => $row) {
            if ($key === 0) {
                continue; // Skip header row
            }

            $category = $this->categoryRepository->findOneByName($row[$columnNumbersByTitle[self::COLUMN_TITLE_CATEGORY]]);

            if (is_null($category)) {
                $categoryError++;

                continue;
            }

            $product = new Product();
            $product->setName($row[$columnNumbersByTitle[self::COLUMN_TITLE_NAME]]);
            $product->setDescription($row[$columnNumbersByTitle[self::COLUMN_TITLE_DESCRIPTION]]);
            $product->setPrice($row[$columnNumbersByTitle[self::COLUMN_TITLE_PRICE]]);
            $product->setStockQuantity($row[$columnNumbersByTitle[self::COLUMN_TITLE_STOCK_QUANTITY]]);
            $product->setCategory($category);

            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();

        if ($categoryError) {
            throw new BadRequestHttpException(sprintf('Неправильно указана категория %s раз/раза!', $categoryError));
        }
    }

    private function assertAllRequiredColumnsArePresent(array $columnNumbersByTitle): void
    {
        $columnTitles = $this->getColumnTitles();

        if (count($columnNumbersByTitle) !== count($columnTitles)) {
            throw new BadRequestHttpException('В файле отсутствует одно из обязательных полей: ' . implode(', ', $columnTitles));
        }
    }

    private function getColumnNumbersByTitle(array $rows): array
    {
        $columnTitles = $this->getColumnTitles();
        $columns = [];

        foreach ($columnTitles as $columnTitle) {
            $columns[$columnTitle] = $this->getColumnNumber($columnTitle, $rows);
        }

        return array_filter($columns, [$this, 'isNotNull']);
    }

    private function getColumnNumber(string $columnTitle, array $rows): ?int
    {
        $columnNumber = null;
        $titles = reset($rows);

        foreach ($titles as $number => $cell) {
            if (trim($cell) === $columnTitle) {
                $columnNumber = $number;
                break;
            }
        }

        return $columnNumber;
    }

    private function isNotNull ($var): bool
    {
        return !is_null($var);
    }

    private function getColumnTitles(): array
    {
        return [self::COLUMN_TITLE_NAME, self::COLUMN_TITLE_DESCRIPTION, self::COLUMN_TITLE_PRICE, self::COLUMN_TITLE_STOCK_QUANTITY, self::COLUMN_TITLE_CATEGORY];
    }
}