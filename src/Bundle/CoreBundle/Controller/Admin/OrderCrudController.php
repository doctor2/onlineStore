<?php

namespace App\Bundle\CoreBundle\Controller\Admin;

use App\Bundle\OrderBundle\Entity\Enum\OrderStatus;
use App\Bundle\OrderBundle\Entity\Enum\PaymentMethod;
use App\Bundle\OrderBundle\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

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
            ChoiceField::new('status')->setChoices($this->formChoices(OrderStatus::cases()))->setDisabled(),
            ChoiceField::new('payment_method')->setChoices($this->formChoices(PaymentMethod::cases()))->hideOnForm(),
            AssociationField::new('orderItems'),
            AssociationField::new('payments'),
            DateTimeField::new('created_at')->onlyOnDetail(),
            DateTimeField::new('updated_at')->onlyOnDetail(),
        ];
    }

    public function formChoices(array $cases): array
    {
        $choices = [];

        foreach ($cases as $case) {
            $choices[$case->name] = $case->value;
        }

        return $choices;
    }

    public function configureActions(Actions $actions): Actions
    {
        // Hide the Add action button for the entity
        $actions->disable(Action::NEW);

        return $actions;
    }
}
