<?php

namespace App\Validator\Constraints\EntityExists;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EntityExistsValidator extends ConstraintValidator
{
    public function __construct(private EntityManagerInterface $entityManager)
    {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof EntityExists) {
            throw new \InvalidArgumentException(sprintf('Constraint must be instance %s', EntityExists::class));
        }

        if (empty($constraint->entityClass) || false === $this->entityManager->getMetadataFactory()->isTransient($constraint->entityClass)) {
            throw new \InvalidArgumentException(sprintf(
                '"entityClass" must be real entity class. Not found mapping for "%s"',
                $constraint->entityClass
            ));
        }
        $repository = $this->entityManager->getRepository($constraint->entityClass);
        $entity = $value ? $repository->find($value) : null;

        if ($entity === null) {
            $this->context->addViolation($constraint->message);
        }
    }
}
