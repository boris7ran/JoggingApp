<?php

namespace App\Validator\Constraints;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CheckRoleValidator extends ConstraintValidator
{
    public $security;
    public $checker;

    public function __construct(AuthorizationCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof CheckRole) {
            throw new UnexpectedTypeException($constraint, CheckRole::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$this->checker->isGranted('ROLE_ADMIN')) {
            if (!($this->checker->isGranted('ROLE_MANAGER') && $value !== 'ROLE_ADMIN')){
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();
            }
        }
    }
}