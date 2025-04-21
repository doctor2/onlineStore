<?php

namespace App\Controller\Admin;

use App\Entity\Enum\OrderStatus;
use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('user'),
            IntegerField::new('total_amount'),
            ChoiceField::new('status')->setChoices(OrderStatus::cases()),
            TextField::new('payment_method'),
            AssociationField::new('orderItems'),
            AssociationField::new('payments'),
            DateTimeField::new('created_at')->onlyOnDetail(),
            DateTimeField::new('updated_at')->onlyOnDetail(),
        ];
    }

}
