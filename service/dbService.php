<?php

class DbService {

	private $db;
	private $schema;

    public function __construct($db)
    {
        $this->db = $db;
        $this->schema = $this->getSchema();
    }

	public function getSchema() {
		$schema = new \Doctrine\DBAL\Schema\Schema();
		$userTable = $schema->createTable("user");
		$userTable->addColumn("id", "integer", array("unsigned" => true, 'autoincrement' => true));
		$userTable->addColumn("username", "string", array("length" => 32));
		$userTable->addColumn("password", "string", array("length" => 32));
		$userTable->addColumn("mail", "string", array("length" => 100));
		$userTable->addColumn("avatar", "string", array("length" => 500, 'default' => ''));
		$userTable->addColumn("description", "string", array("length" => 2000, 'default' => ''));
		$userTable->setPrimaryKey(array("id"));
		$userTable->addUniqueIndex(array("username"));
		$userTable->addUniqueIndex(array("mail"));

		$campagneTable =  $schema->createTable("campagne");
		$campagneTable->addColumn("id", "integer", array("unsigned" => true, 'autoincrement' => true));
		$campagneTable->addColumn("mj_id", "integer", array("unsigned" => true));
		$campagneTable->addColumn("nb_joueurs", "integer", array("unsigned" => true));
		$campagneTable->addColumn("nb_joueurs_actuel", "integer", array("unsigned" => true, 'default' => 0));
		$campagneTable->addColumn("name", "string", array("length" => 100));
		$campagneTable->addColumn("systeme", "string", array("length" => 100));
		$campagneTable->addColumn("univers", "string", array("length" => 100));
		$campagneTable->addColumn("description", "string", array("length" => 2000, 'default' => ''));
		$campagneTable->addForeignKeyConstraint($userTable, array("mj_id"), array("id"), array("onUpdate" => "CASCADE"));
		$campagneTable->setPrimaryKey(array("id"));

		return $schema;
	}

    public function init() {
    	$this->db->beginTransaction();
		$queries = $this->schema->toSql($this->db->getDatabasePlatform()); // get queries to create this schema.
		foreach($queries as $query) {
			 $this->db->query($query);
		}
		$this->db->commit();
    }

    public function drop() {
    	$this->db->beginTransaction();
    	$queries = $this->schema->toDropSql($this->db->getDatabasePlatform()); // get queries to create this schema.
		foreach($queries as $query) {
			 $this->db->query($query);
		}
		$this->db->commit();
    }

    public function reInit() {
    	$this->drop();
    	$this->init();
    }
}

?>