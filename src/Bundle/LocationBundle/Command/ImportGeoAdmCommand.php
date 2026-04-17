<?php

namespace App\Bundle\LocationBundle\Command;

use App\Bundle\LocationBundle\Entity\Locality;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

#[AsCommand(
    name: 'app:import:geoadm',
    description: 'Импорт населенных пунктов России'
)]
class ImportGeoAdmCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = HttpClient::create();

        // 1. Загружаем карту сайта GeoADM
        $sitemapUrl = 'https://geoadm.com/karta-saita.html';
        $html = $client->request('GET', $sitemapUrl)->getContent();

        $crawler = new Crawler($html);

        // 2. Находим ссылки на регионы
        $regionLinks = $crawler->filter('a')->each(fn(Crawler $node) => $node->attr('href'));


        foreach ($regionLinks as $regionUrl) {
            if (!str_contains($regionUrl, 'naselennye-punkty-')) {
                continue;
            }

            $output->writeln("Регион: $regionUrl");
            sleep(3);

            $regionHtml = $client->request('GET', 'https://geoadm.com/' . $regionUrl)->getContent();
            $regionCrawler = new Crawler($regionHtml);

            $regionName = $this->getRegionName($regionCrawler);

            // На странице несколько таблиц .table-bordered.
            // Возьмём последнюю таблицу на странице.
            $tables = $regionCrawler->filter('table.table.table-bordered');
            $localitiesTable = $tables->last();

            $rows = $localitiesTable->filter('tr');

            $rows->each(function (Crawler $tr, int $i) use ($regionName) {
                if ($i === 0) {
                    return;
                }

                $cols = $tr->filter('td');
                if ($cols->count() < 5) {
                    return;
                }

                $name = trim($cols->eq(1)->text(''));
                $type = trim($cols->eq(2)->text(''));
                $population = trim($cols->eq(3)->text(''));
                $district = trim($cols->eq(4)->text(''));

                $locality = new Locality();
                $locality
                    ->setName($name)
                    ->setType($type)
                    ->setRegion($regionName)
                    ->setDistrict($district !== '' ? $district : null)
                    ->setPopulation($population !== '' ? (int)$population : null);

                $this->em->persist($locality);
            });

            $this->em->flush();
        }

        $output->writeln('Импорт завершён');
        return Command::SUCCESS;
    }

    public function getRegionName(Crawler $regionCrawler): string
    {
        // 1. Находим заголовок h2 с нужным текстом
        $regionHeader = $regionCrawler->filterXPath("//h2[contains(text(), 'С какими областями и странами граничит')]");

        if ($regionHeader->count() === 0) {
            throw new \RuntimeException("Не найден заголовок региона");
        }

        // 2. Получаем текст, например:
        // "С какими областями и странами граничит Магаданская область"
        $fullTitle = trim($regionHeader->text());

        return trim(
            preg_replace('/^С какими областями и странами граничит\s+/u', '', $fullTitle)
        );
    }
}
