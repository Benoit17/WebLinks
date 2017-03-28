<?php

namespace WebLinks\DAO;

use WebLinks\Domain\Link;

class LinkDAO extends DAO
{
    /**
     * @var \WebLinks\DAO\UserDAO
     */
    private $userDAO;

    public function setUserDAO(UserDAO $userDAO) {
        $this->userDAO = $userDAO;
    }
    
    /**
     * Returns a list of all links, sorted by id.
     *
     * @return array A list of all links.
     */
    public function findAll() {
        $sql = "select * from t_link order by link_id desc";
        $result = $this->getDb()->fetchAll($sql);
        
        // Convert query result to an array of domain objects
        $links = array();
        foreach ($result as $row) {
            $id = $row['link_id'];
            $links[$id] = $this->buildDomainObject($row);
        }
        return $links;
    }

    /**
     * Returns a link matching the supplied id.
     *
     * @param integer $id The link id.
     *
     * @return \WebLinks\Domain\Link|throws an exception if no matching book is found
     */
    public function find($id) {
        $sql = "select * from t-link where link_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No link matching id " . $id);
    }

    /**
     * Creates an Link object based on a DB row.
     *
     * @param array $row The DB row containing Link data.
     * @return \WebLinks\Domain\Link
     */
    protected function buildDomainObject($row) {
        $link = new Link();
        $link->setId($row['link_id']);
        $link->setUrl($row['link_title']);
        $link->setTitle($row['link_url']);

        if (array_key_exists('user_id', $row)) {
            // Find and set the associated author
            $userId = $row['user_id'];
            $author = $this->userDAO->find($userId);
            $link->setAuthor($author);
        }
        
        return $link;
    }
}
