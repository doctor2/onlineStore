<?php

namespace App\Bundle\CoreBundle\Controller\Frontend;

use App\Bundle\ProductBundle\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function list(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager->getRepository(Product::class)->createQueryBuilder('product');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('product/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
