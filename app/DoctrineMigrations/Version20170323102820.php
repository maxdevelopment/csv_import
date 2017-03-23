<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170323102820 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            'ALTER TABLE tblProductData ADD stock INT NOT NULL, '.
            'ADD price DECIMAL(10,2) NOT NULL, '.
            'CHANGE stmTimestamp stmTimestamp DATETIME NOT NULL'
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql(
            'ALTER TABLE tblProductData DROP stock, DROP price, '.
            'CHANGE stmTimestamp stmTimestamp '.
            'TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL'
        );
    }
}
