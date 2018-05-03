<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180502151827 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, salt INT NOT NULL, roles VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE article DROP CONSTRAINT fk_23a0e66f8697d13');
        $this->addSql('DROP INDEX idx_23a0e66f8697d13');
        $this->addSql('ALTER TABLE article ADD author VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE article DROP comment_id');
        $this->addSql('ALTER TABLE comment ADD article_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment DROP comment_id');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C7294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9474526C7294869C ON comment (article_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C7294869C');
        $this->addSql('DROP INDEX IDX_9474526C7294869C');
        $this->addSql('ALTER TABLE comment ADD comment_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment DROP article_id');
        $this->addSql('ALTER TABLE article ADD comment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article DROP author');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT fk_23a0e66f8697d13 FOREIGN KEY (comment_id) REFERENCES comment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_23a0e66f8697d13 ON article (comment_id)');
    }
}
