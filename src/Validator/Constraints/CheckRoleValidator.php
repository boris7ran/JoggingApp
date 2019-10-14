<?php

namespace App\Validator\Constraints;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CheckRoleValidator extends ConstraintValidator
{
    /**
     * @var
     */
    public $security;

    /**
     * @var AuthorizationCheckerInterface
     */
    public $checker;

    /**
     * CheckRoleValidator constructor.
     *
     * @param AuthorizationCheckerInterface $checker
     */
    public function __construct(AuthorizationCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof CheckRole) {
            throw new UnexpectedTypeException($constraint, CheckRole::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$this->checker->isGranted('ROLE_ADMIN')) {
            if (!($this->checker->isGranted('ROLE_MANAGER')
                && ($value !== 'ROLE_ADMIN' && $value !== 'ROLE_USER'))){
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();
            }
        }
    }
}
