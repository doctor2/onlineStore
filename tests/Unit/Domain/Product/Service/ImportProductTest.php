<?php
declare(strict_types=1);

namespace App\Tests\Unit\Domain\Product\Service;

use App\Domain\Product\Entity\Category;
use App\Domain\Product\Repository\CategoryRepository;
use App\Domain\Product\Service\ImportProduct;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ImportProductTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private CategoryRepository $categoryRepository;
    private ImportProduct $importProduct;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->categoryRepository = $this->createMock(CategoryRepository::class);
        $this->importProduct = new ImportProduct($this->entityManager, $this->categoryRepository);
    }

    public function testImportSuccess(): void
    {
        $filePath = $this->createSpreadsheet(['Название товара', 'Описание', 'Цена', 'Остаток на складе', 'Категория'], [
            ['Product 1', 'Description 1', '100', '10', 'Category 1'],
            ['Product 2', 'Description 2', '200', '5', 'Category 2'],
        ]);

        $this->categoryRepository->method('findOneByName')->willReturn(
            $this->createMock(Category::class)
        );

        $this->entityManager->expects($this->exactly(2))->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->importProduct->import($filePath);
    }

    public function testImportMissingRequiredColumns(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $filePath = $this->createSpreadsheet(['Название товара', 'Описание'], [
            ['Product 1', 'Description 1'],
            ['Product 2', 'Description 2'],
        ]);

        $this->importProduct->import($filePath);
    }

    public function testImportWithNonexistentCategory(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Неправильно указана категория 1 раз/раза!');

        $filePath = $this->createSpreadsheet(['Название товара', 'Описание', 'Цена', 'Остаток на складе', 'Категория'], [
            ['Product 1', 'Description 1', '100', '10', 'Nonexistent Category'],
        ]);

        $this->categoryRepository->method('findOneByName')->willReturn(null);

        $this->entityManager->expects($this->never())->method('persist');

        $this->importProduct->import($filePath);
    }

    private function createSpreadsheet(array $headers, array $data): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        foreach ($headers as $columnIndex => $header) {
            $sheet->setCellValue([$columnIndex + 1, 1], $header);
        }

        // Set data
        foreach ($data as $rowIndex => $row) {
            foreach ($row as $columnIndex => $cellValue) {
                $sheet->setCellValue([$columnIndex + 1, $rowIndex + 2], $cellValue);
            }
        }

        // Save the spreadsheet to a temporary file
        $filePath = tempnam(sys_get_temp_dir(), 'import') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $filePath;
    }

    protected function tearDown(): void
    {
        // Clean up the temporary file if necessary.
    }
}