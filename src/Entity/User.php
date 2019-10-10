<?php

namespace App\Entity;

use App\Validator\Constraints\CheckRole;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="simple_array")
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Record", mappedBy="user", orphanRemoval=true)
     */
    private $records;

    /**
     * User constructor.
     * @param string $password
     * @param string $username
     * @param array $roles
     */
    public function __construct(string $password, string $username, array $roles)
    {
        $this->records = new ArrayCollection();

        $this->password = $password;
        $this->username = $username;
        $this->roles = $roles;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @param string $role
     */
    public function addRole(string $role)
    {
        if (!in_array($role, $this->roles)){
            $this->roles[] = $role;
        }
    }

    /**
     * @param string $role
     */
    public function removeRole(string $role)
    {
        $index = array_search($role, $this->roles);
        if ($index) {
            array_splice($this->roles, $index, 1);
        }
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($user->getId() !== $this->id) {
            return false;
        }

        return true;
    }

    /**
     * @return Collection
     */
    public function getRecords(): Collection
    {
        return $this->records;
    }

    /**
     * @param DateTimeInterface $date
     * @param int $time
     * @param int $distance
     *
     * @return Record
     */
    public function addRecord(DateTimeInterface $date, int $time, int $distance): Record
    {
        $record = new Record($date, $time, $distance, $this);

        return $record;
    }

    /**
     * @param Record $record
     */
    public function removeRecord(Record $record)
    {
        if ($this->records->contains($record)) {
            $this->records->removeElement($record);

            if ($record->getUser() === $this) {
                $record->setUser(null);
            }
        }
    }

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'username',
            'groups' => ['registration']
        ]));

        $metadata->addPropertyConstraint('roles', new CheckRole());
    }
}
