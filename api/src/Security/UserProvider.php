<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserProvider implements UserProviderInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $username
     *
     * @return User|UsernameNotFoundException
     */
    public function loadUserByUsername($username)
    {
        $user = $this->userRepository->loadUserByUsername($username);

        if ($user) {
            return $user;
        }

        throw new UsernameNotFoundException(
            sprintf('Username or Email "%s" does not exist.', $username)
        );
    }

    /**
     * @param UserInterface $user
     *
     * @return User|UsernameNotFoundException
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}
