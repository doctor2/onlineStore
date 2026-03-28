<?php

namespace App\Bundle\CoreBundle\Command;

use App\Bundle\CoreBundle\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import:cities',
    description: 'Импорт городов России из Википедии'
)]
class ImportCitiesCommand extends Command
{
    private string $url = 'https://ru.wikipedia.org/wiki/Список_городов_России';

    public function __construct(
        private HttpClientInterface $client,
        private EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Скачиваю страницу...');

        $response = $this->client->request('GET', $this->url);
        $html = $response->getContent();

        $crawler = new Crawler($html);

        $rows = $crawler->filter('table.standard tbody tr');

        $count = 0;

        foreach ($rows as $row) {
            $cols = (new Crawler($row))->filter('td');

            if ($cols->count() < 2) {
                continue;
            }

            $name = trim($cols->eq(2)->text());
            $region = trim($cols->eq(3)->text());

            $city = new City();
            $city->setName($name);
            $city->setRegion($region);

            $this->em->persist($city);
            $count++;
        }

        $this->em->flush();

        $output->writeln("Импорт завершён. Добавлено городов: $count");

        return Command::SUCCESS;
    }
}
