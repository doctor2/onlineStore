<?php

namespace App\Tests\Support\Repository;

use Codeception\Exception\ModuleException;
use Codeception\Module;
use PDO;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Tests\Support\TransferObject\User;

class UserRepository extends Module
{
    private function getOptionsResolver(): OptionsResolver
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver
            ->setDefined([
                'email',
                'roles',
            ])
            ->setAllowedTypes('email', 'string')
            ->setAllowedValues('roles', ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_CUSTOMER']);

        return $optionsResolver;
    }

    public function findAdmin(): User
    {
        return $this->findUserByRole('ROLE_ADMIN');
    }

    public function findCustomer(): User
    {
        return $this->findUserByRole('ROLE_CUSTOMER');
    }

    private function findUserByRole(string $role): User
    {
        $criteria = [
            'roles' => $role,
        ];

        return $this->findUserByCriteria($criteria);
    }

    /**
     * @param mixed[] $criteria
     */
    public function findUserByCriteria(array $criteria): User
    {
        $options = $this->getOptionsResolver()->resolve($criteria);

        $userInformation = $this->grabInformationFromDataBase($options);

        return $this->convertToObject($userInformation, $options);
    }

    /**
     * @throws ModuleException
     */
    private function grabInformationFromDataBase(array $options = []): array
    {
        $queryString = $this->buildQueryString($options);

        $connection = $this->getModule('Db')->_getDbh();

        $query = $connection->query($queryString, PDO::FETCH_ASSOC);

        $information = $query->fetch();
        if (!$information) {
            throw new \RuntimeException('Not found user by criteria');
        }

        return $information;
    }

    private function convertToObject(array $userInformation, $options): User
    {
        $user = new User();
        $user->id = $userInformation['id'];
        $user->username = $userInformation['username'];
        $user->roles = $userInformation['roles'];
        $user->password = $userInformation['password'];
        $user->email = $userInformation['email'];

        return $user;
    }

    /**
     * @param mixed[] $options
     */
    private function buildQueryString(array $options = []): string
    {
        $sql = [
            'select' => ['`user`.*'],
            'from' => ['`users` AS `user`'],
            'limit' => [0, 1],
        ];

        if (!empty($options['roles'])) {
            $sql['where'][] = sprintf('JSON_CONTAINS(`user`.`roles`, \'"%s"\')', $options['roles']);
        }

        if (!empty($options['email'])) {
            $sql['where'][] = sprintf('`user`.`email` = \'%s\'', $options['email']);
        }

        $queryString = sprintf(
            'SELECT %s FROM %s %s WHERE %s GROUP BY `user`.`id` ORDER BY %s LIMIT %s',
            implode(',', $sql['select']),
            implode(',', $sql['from']),
            (empty($sql['join']) ? '' : implode(' ', $sql['join'])),
            implode(' AND ', $sql['where']),
            (empty($sql['order']) ? '1' : implode(',', $sql['order'])),
            (empty($sql['limit']) ? '0, 1' : implode(',', $sql['limit']))
        );

        return $queryString;
    }
}
