<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RecordVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'view';

    /**
     * @param string $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, array(self::VIEW, self::EDIT))) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        $owner = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($owner, $user);
            case self::EDIT:
                return $this->canEdit($owner, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param User $owner
     * @param User $user
     *
     * @return bool
     */
    private function canView(User $owner, User $user): bool
    {
        if ($this->canEdit($owner, $user)) {
            return true;
        }

        return false;
    }

    /**
     * @param User $owner
     * @param User $user
     *
     * @return bool
     */
    private function canEdit(User $owner, User $user): bool
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        if ((in_array('ROLE_MANAGER', $user->getRoles()))
            && (in_array('ROLE_USER', $owner->getRoles()))) {
            return true;
        }

        if ($user->getId() === $owner->getId()) {
            return true;
        }

        return false;
    }
}