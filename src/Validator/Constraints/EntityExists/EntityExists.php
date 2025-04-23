<?php

namespace App\Validator\Constraints\EntityExists;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class EntityExists extends Constraint
{
    public function __construct(public string $entityClass, public string $message = 'Entity not found',
                                ?array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);
    }
}
