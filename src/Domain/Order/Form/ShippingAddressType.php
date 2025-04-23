<?php

namespace App\Domain\Order\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ShippingAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('addressLine')
            ->add('city')
            ->add('postalCode')
            ->add('submit', SubmitType::class, ['label' => 'Далее']);
    }
}