<?php

namespace App\Domain\Order\Form;

use App\Domain\Order\Entity\Enum\PaymentMethod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('paymentMethod', EnumType::class, ['class' => PaymentMethod::class])
            ->add('submit', SubmitType::class, ['label' => 'Оформить заказ']);
    }
}