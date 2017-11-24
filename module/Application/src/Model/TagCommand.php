<?php

namespace Application\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Update;
use Zend\Hydrator\Reflection as ReflectionHydrator;

class TagCommand
{
    protected $dbAdapter;

    public function __construct(AdapterInterface $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    public function insert(Note $note)
    {
        $insert = new Insert('tag');

        $insert->values([
            'category_id' => $note->getCategory()->getId(),
            'title' => $note->getTitle(),
            'content' => $note->getContent(),
            'position' => $note->getPosition() ? $note->getPosition() : 0
        ]);

        $sql = new Sql($this->dbAdapter);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface) {
            throw new \RuntimeException(
                'Database error occurred during note insert operation'
            );
        }

        $id = $result->getGeneratedValue();
        return $id;
    }

    public function delete(Note $note)
    {
        if (!$note->getId()) {
            throw new \RuntimeException('Cannot delete note; missing identifier.');
        }

        $delete = new Delete('note');
        $delete->where(['id' => $note->getId()]);

        $sql = new Sql($this->dbAdapter);
        $statement = $sql->prepareStatementForSqlObject($delete);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface) {
            throw new \RuntimeException('Cannot delete note; undefined erro.');
        }
    }
}