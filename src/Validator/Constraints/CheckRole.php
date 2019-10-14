<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CheckRole extends Constraint
{
    /**
     * @var string
     */
    public $message = 'You are not authorized for this action';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}
