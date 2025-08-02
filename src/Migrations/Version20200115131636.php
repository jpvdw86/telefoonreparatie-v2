<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200115131636 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE openinghours (id INT AUTO_INCREMENT NOT NULL, opening_hours VARCHAR(22) NOT NULL, closing_hour VARCHAR(22) NOT NULL, day VARCHAR(22) NOT NULL, domain VARCHAR(22) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE email_messages CHANGE customer_id customer_id INT DEFAULT NULL, CHANGE model_id model_id INT DEFAULT NULL, CHANGE message message LONGTEXT NOT NULL, CHANGE email_body email_body LONGTEXT DEFAULT NULL, CHANGE color color VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE brands CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE appointments CHANGE customer_id customer_id INT DEFAULT NULL, CHANGE created_date created_date DATETIME DEFAULT NULL, CHANGE appointment_date appointment_date DATETIME DEFAULT NULL, CHANGE hash hash VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(255) DEFAULT NULL, CHANGE zipcode zipcode VARCHAR(255) DEFAULT NULL, CHANGE house_number house_number VARCHAR(255) DEFAULT NULL, CHANGE street_name street_name VARCHAR(255) DEFAULT NULL, CHANGE province province VARCHAR(255) DEFAULT NULL, CHANGE city city VARCHAR(255) DEFAULT NULL, CHANGE email_address email_address VARCHAR(255) DEFAULT NULL, CHANGE create_date create_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE email_tracking CHANGE message_id message_id INT DEFAULT NULL, CHANGE datetime datetime DATETIME DEFAULT NULL, CHANGE type type VARCHAR(255) DEFAULT NULL, CHANGE url url VARCHAR(255) DEFAULT NULL, CHANGE ipadress ipadress VARCHAR(255) DEFAULT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE emailadress emailadress VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE models CHANGE brand_id brand_id INT DEFAULT NULL, CHANGE group_id group_id INT DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL, CHANGE meta_title meta_title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE modelgroup CHANGE brand_id brand_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE page CHANGE model_id model_id INT DEFAULT NULL, CHANGE brand_id brand_id INT DEFAULT NULL, CHANGE meta_title meta_title VARCHAR(255) DEFAULT NULL, CHANGE meta_keywords meta_keywords VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE repair CHANGE model_id model_id INT DEFAULT NULL, CHANGE repair_option_id repair_option_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE repairoptions CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE admin_users CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL, CHANGE firstname firstname VARCHAR(255) DEFAULT NULL, CHANGE lastname lastname VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE openinghours');
        $this->addSql('ALTER TABLE Admin_users CHANGE salt salt VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\', CHANGE firstname firstname VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE lastname lastname VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE appointments CHANGE customer_id customer_id INT DEFAULT NULL, CHANGE created_date created_date DATETIME DEFAULT \'NULL\', CHANGE appointment_date appointment_date DATETIME DEFAULT \'NULL\', CHANGE hash hash VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE Brands CHANGE image image VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE customer CHANGE first_name first_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE last_name last_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE phone_number phone_number VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE zipcode zipcode VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE house_number house_number VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE street_name street_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE province province VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE city city VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE email_address email_address VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE create_date create_date DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE email_messages CHANGE customer_id customer_id INT DEFAULT NULL, CHANGE model_id model_id INT DEFAULT NULL, CHANGE message message LONGTEXT NOT NULL COLLATE utf8_unicode_ci, CHANGE email_body email_body LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE color color VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE email_tracking CHANGE message_id message_id INT DEFAULT NULL, CHANGE datetime datetime DATETIME DEFAULT \'NULL\', CHANGE type type VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE url url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE ipadress ipadress VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE description description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE emailadress emailadress VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE ModelGroup CHANGE brand_id brand_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Models CHANGE brand_id brand_id INT DEFAULT NULL, CHANGE group_id group_id INT DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE meta_title meta_title VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE Page CHANGE model_id model_id INT DEFAULT NULL, CHANGE brand_id brand_id INT DEFAULT NULL, CHANGE meta_title meta_title VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE meta_keywords meta_keywords VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE Repair CHANGE model_id model_id INT DEFAULT NULL, CHANGE repair_option_id repair_option_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE RepairOptions CHANGE image image VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
    }
}
