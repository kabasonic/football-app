<?php

namespace App\Application\Validator;

use App\Application\Validator\Constraints\CurrentYearConstraintValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class CurrentYearConstraint extends Constraint
{
    public $message = 'The year is not valid. It must be the current year or a previous year.';

    public function validatedBy(): string
    {
        return CurrentYearConstraintValidator::class;
    }
}
