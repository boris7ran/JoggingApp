<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CheckRole extends Constraint
{
    public $message = 'You are not authorized for this action';

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}