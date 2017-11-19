<?php

namespace User\Model;

use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class UserTable
{
    protected $tableGateway;

    protected $userHydrator;

    public function __construct(TableGateway $tableGateway, UserHydrator $userHydrator)
    {
         $this->tableGateway = $tableGateway;
         $this->userHydrator = $userHydrator;
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function getUser($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();

        if (!$row) {
            throw new \RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function findUserById($id)
    {
        $sql       = $this->tableGateway->getSql();
        $select    = $sql->select()
            ->join(['r' => 'role'], 'user.role_id = r.id')
            ->where(['user.id' => $id]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();

        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new \RuntimeException(sprintf(
                'Failed retrieving user with identifier "%s"; unknown database error.',
                $id
            ));
        }

        $resultSet = new HydratingResultSet($this->userHydrator, new User());
        $resultSet->initialize($result);
        $user = $resultSet->current();

        if (! $user) {
            throw new \InvalidArgumentException(sprintf(
                'User with identifier "%s" not found.',
                $id
            ));
        }

        return $user;
    }

    public function save(User $user)
    {
        $data = [
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'date_created' => date('Y-m-d H:i:s'),
        ];

        $id = $user->getId();

        if ($id == 0) {
            $this->tableGateway->insert($data);
            return;
        }

        if (!$this->getUser($id)) {
            throw new \RuntimeException(sprintf(
                'Cannot update user with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function delete($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}
