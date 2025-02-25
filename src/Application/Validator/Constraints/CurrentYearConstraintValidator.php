<?php

namespace App\Application\Validator\Constraints;

use App\Application\Validator\CurrentYearConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CurrentYearConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CurrentYearConstraint) {
            throw new \InvalidArgumentException('Invalid constraint type');
        }

        $currentYear = (int) date('Y');

        if ($value < 1800 || $value > $currentYear) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
