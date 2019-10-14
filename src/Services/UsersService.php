<?php

namespace App\Services;

use App\DataTransferObjects\ListUsersDto;
use App\DataTransferObjects\UserDto;
use App\Entity\User;
use App\Model\Builders\UserFilterBuilder;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UsersService
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UsersService constructor.
     *
     * @param UserRepository $userRepo
     * @param ValidatorInterface $validator
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserRepository $userRepo, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepo = $userRepo;
        $this->validator = $validator;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param int $userId
     *
     * @return UserDto
     *
     * @throws NonUniqueResultException
     */
    public function getUser(int $userId)
    {
        $user = $this->userRepo->ofId($userId);

        return new UserDto($user);
    }

    /**
     * @param User $authUser
     *
     * @return ListUsersDto
     */
    public function getUsers(User $authUser): ListUsersDto
    {
        $userFilterBuilder = new UserFilterBuilder();
        if (in_array('ROLE_ADMIN', $authUser->getRoles())) {
            $userFilter = $userFilterBuilder->build();
            $users = $this->userRepo->filter($userFilter);
        } else {
            $userFilter = $userFilterBuilder->users()->build();
            $users = $this->userRepo->filter($userFilter);
        }

        return new ListUsersDto($users);
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return User
     *
     * @throws ORMException
     */
    public function storeUser(string $username, string $password)
    {
        $roles[] = "ROLE_USER";

        $user = new User($password, $username, $roles);

        $password = $this->passwordEncoder->encodePassword($user, $password);
        $user->setPassword($password);

        $errors = $this->validator->validate($user, null, ['registration']);

        if (count($errors) > 0) {
            throw new Error('This user is not valid!');
        }

        $this->userRepo->add($user);

        return $user;
    }

    /**
     * @param array $role
     * @param int $id
     *
     * @return UserDto
     *
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function upgradeUser(array $role, int $id): UserDto
    {
        $user = $this->userRepo->ofId($id);

        $user->setRoles($role);

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new \Error($errorsString);
        }

        $this->userRepo->add($user);

        return new UserDto($user);
    }
}
