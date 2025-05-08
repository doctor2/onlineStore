<?php

namespace App\Controller\Admin;

use App\Domain\Product\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('slug'),
            TextField::new('name'),
            TextEditorField::new('description'),
            AssociationField::new('parent')
                ->setQueryBuilder(function ($qb) {
                    return $qb->orderBy('entity.name', 'ASC');
                }),
            TextField::new('hierarchyName', 'Hierarchy')->setTemplatePath('admin/hierarchy_title.html.twig')->hideOnForm(),
            DateTimeField::new('created_at')->onlyOnDetail(),
            DateTimeField::new('updated_at')->onlyOnDetail(),
        ];
    }
}
