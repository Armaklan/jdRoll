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
		$userTable->addColumn("description", "text", array('default' => ''));
		$userTable->addColumn("profil", "integer", array("unsigned" => true, 'default' => 0));
		$userTable->setPrimaryKey(array("id"));
		$userTable->addUniqueIndex(array("username"));
		$userTable->addUniqueIndex(array("mail"));

		$campagneTable =  $schema->createTable("campagne");
		$campagneTable->addColumn("id", "integer", array("unsigned" => true, 'autoincrement' => true));
		$campagneTable->addColumn("mj_id", "integer", array("unsigned" => true));
		$campagneTable->addColumn("nb_joueurs", "integer", array("unsigned" => true));
		$campagneTable->addColumn("nb_joueurs_actuel", "integer", array("unsigned" => true, 'default' => '0'));
		$campagneTable->addColumn("name", "string", array("length" => 100));
		$campagneTable->addColumn("banniere", "string", array("length" => 500, 'default' => ''));
		$campagneTable->addColumn("systeme", "string", array("length" => 100));
		$campagneTable->addColumn("univers", "string", array("length" => 100));
		$campagneTable->addColumn("description", "text", array('default' => ''));
		$campagneTable->addColumn("statut", "integer", array("unsigned" => true, 'default' => '0'));
		$campagneTable->addForeignKeyConstraint($userTable, array("mj_id"), array("id"), array("onDelete" => "CASCADE"));
		$campagneTable->setPrimaryKey(array("id"));

		$participantTable =  $schema->createTable("campagne_participant");
		$participantTable->addColumn("campagne_id", "integer", array("unsigned" => true));
		$participantTable->addColumn("user_id", "integer", array("unsigned" => true));
		$participantTable->addForeignKeyConstraint($userTable, array("user_id"), array("id"), array("onDelete" => "CASCADE"));
		$participantTable->addForeignKeyConstraint($campagneTable, array("campagne_id"), array("id"), array("onDelete" => "CASCADE"));
		$participantTable->setPrimaryKey(array("campagne_id", "user_id"));

		$persoTable =  $schema->createTable("personnages");
		$persoTable->addColumn("id", "integer", array("unsigned" => true, 'autoincrement' => true));
		$persoTable->addColumn("campagne_id", "integer", array("unsigned" => true));
		$persoTable->addColumn("user_id", "integer", array("unsigned" => true, 'notnull' => false));
		$persoTable->addColumn("name", "string", array("length" => 100, 'default' => ''));
		$persoTable->addColumn("avatar", "string", array("length" => 500, 'default' => ''));
		$persoTable->addColumn("publicDescription", "text", array('default' => ''));
		$persoTable->addColumn("privateDescription", "text", array('default' => ''));
		$persoTable->addColumn("technical", "text", array('default' => ''));
		$persoTable->setPrimaryKey(array("id"));
		$persoTable->addForeignKeyConstraint($userTable, array("user_id"), array("id"), array("onDelete" => "CASCADE"));
		$persoTable->addForeignKeyConstraint($campagneTable, array("campagne_id"), array("id"), array("onDelete" => "CASCADE"));
		
		$sectionTable = $schema->createTable("sections");
		$sectionTable->addColumn("id", "integer", array("unsigned" => true, 'autoincrement' => true));
		$sectionTable->addColumn("campagne_id", "integer", array("unsigned" => true, 'notnull' => false));
		$sectionTable->addColumn("title", "string", array("length" => 500, 'default' => ''));
		$sectionTable->addColumn("ordre", "integer", array("unsigned" => true));
		$sectionTable->addColumn("default_collapse", "integer", array("unsigned" => true));
		$sectionTable->setPrimaryKey(array("id"));
		$sectionTable->addForeignKeyConstraint($campagneTable, array("campagne_id"), array("id"), array("onDelete" => "CASCADE"));
		
		$topicTable = $schema->createTable("topics");
		$topicTable->addColumn("id", "integer", array("unsigned" => true, 'autoincrement' => true));
		$topicTable->addColumn("section_id", "integer", array("unsigned" => true));
		$topicTable->addColumn("title", "string", array("length" => 500, 'default' => ''));
		$topicTable->addColumn("stickable", "integer", array("unsigned" => true));
		$topicTable->addColumn("ordre", "integer", array("unsigned" => true));
		$topicTable->addColumn("last_post_id", "integer", array("unsigned" => true, 'notnull' => false));
		$topicTable->setPrimaryKey(array("id"));
		$topicTable->addForeignKeyConstraint($sectionTable, array("section_id"), array("id"), array("onDelete" => "CASCADE"));

		
		$postTable = $schema->createTable("posts");
		$postTable->addColumn("id", "integer", array("unsigned" => true, 'autoincrement' => true));
		$postTable->addColumn("topic_id", "integer", array("unsigned" => true));
		$postTable->addColumn("user_id", "integer", array("unsigned" => true, 'notnull' => false));
		$postTable->addColumn("perso_id", "integer", array("unsigned" => true, 'notnull' => false));
		$postTable->addColumn("content", "text", array('default' => ''));
		$postTable->addColumn("create_date", "datetime");
		$postTable->setPrimaryKey(array("id"));
		$postTable->addForeignKeyConstraint($topicTable, array("topic_id"), array("id"), array("onDelete" => "CASCADE"));
		$postTable->addForeignKeyConstraint($userTable, array("user_id"), array("id"), array("onDelete" => "CASCADE"));
		$postTable->addForeignKeyConstraint($persoTable, array("perso_id"), array("id"), array("onDelete" => "CASCADE"));
		
		
		$topicTable->addForeignKeyConstraint($postTable, array("last_post_id"), array("id"), array());
		
		$readPost = $schema->createTable("read_post");
		$readPost->addColumn("topic_id", "integer", array("unsigned" => true));
		$readPost->addColumn("user_id", "integer", array("unsigned" => true));
		$readPost->addColumn("post_id", "integer", array("unsigned" => true));
		$readPost->addForeignKeyConstraint($topicTable, array("topic_id"), array("id"), array());
		$readPost->addForeignKeyConstraint($userTable, array("user_id"), array("id"), array());
		$readPost->addForeignKeyConstraint($postTable, array("post_id"), array("id"), array());
		
		$dicerTable = $schema->createTable("dicer");
		$dicerTable->addColumn("id", "integer", array("unsigned" => true, 'autoincrement' => true));
		$dicerTable->addColumn("user_id", "integer", array("unsigned" => true));
		$dicerTable->addColumn("campagne_id", "integer", array("unsigned" => true));
		$dicerTable->addColumn("result", "string", array("length" => 500, 'default' => ''));
		$dicerTable->addColumn("create_date", "datetime");
		$dicerTable->addForeignKeyConstraint($userTable, array("user_id"), array("id"), array("onDelete" => "CASCADE"));
		$dicerTable->addForeignKeyConstraint($campagneTable, array("campagne_id"), array("id"), array("onDelete" => "CASCADE"));
		$dicerTable->setPrimaryKey(array("id"));
		
		return $schema;
	}

    public function init() {
		$queries = $this->schema->toSql($this->db->getDatabasePlatform()); // get queries to create this schema.
		foreach($queries as $query) {
			 $this->db->query($query);
		}
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