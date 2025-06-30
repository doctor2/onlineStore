<?php

namespace App\Bundle\CoreBundle\Controller\Admin;

use App\Bundle\ProductBundle\Form\ProductImportType;
use App\Bundle\ProductBundle\Service\ImportProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/admin/products/import', name:'product_import')]
    public function import(Request $request, ImportProductService $importProductService): Response
    {
        $form = $this->createForm(ProductImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();

            if ($file) {
                $importProductService->import($file->getPathname());

                $this->addFlash('success', 'Products imported successfully!');
                return $this->redirectToRoute('admin_product_index');
            }
        }

        return $this->render('admin/product/import.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
