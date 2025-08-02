<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200113125030 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE appointments (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, created_date DATETIME DEFAULT NULL, appointment_date DATETIME DEFAULT NULL, hash VARCHAR(255) DEFAULT NULL, confirmed TINYINT(1) NOT NULL, INDEX IDX_6A41727A9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Brands (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, meta_title LONGTEXT DEFAULT NULL, meta_description LONGTEXT DEFAULT NULL, main_content LONGTEXT DEFAULT NULL, first_content LONGTEXT DEFAULT NULL, status TINYINT(1) NOT NULL, INDEX brand_idx (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, zipcode VARCHAR(255) DEFAULT NULL, house_number VARCHAR(255) DEFAULT NULL, street_name VARCHAR(255) DEFAULT NULL, province VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, email_address VARCHAR(255) DEFAULT NULL, create_date DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Facebook_reviews (id INT AUTO_INCREMENT NOT NULL, facebook_id VARCHAR(255) NOT NULL, facebook_name VARCHAR(255) NOT NULL, facebook_user_image VARCHAR(255) NOT NULL, rating INT NOT NULL, date DATETIME NOT NULL, reviewtext LONGTEXT NOT NULL, domain LONGTEXT NOT NULL, INDEX rating_idx (rating), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_messages (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, model_id INT DEFAULT NULL, domain VARCHAR(255) NOT NULL, type_template VARCHAR(255) NOT NULL, message_id VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, email_body LONGTEXT DEFAULT NULL, incomming TINYINT(1) NOT NULL, send_date DATETIME NOT NULL, color VARCHAR(255) DEFAULT NULL, INDEX IDX_D06401DF9395C3F3 (customer_id), INDEX IDX_D06401DF7975B7E7 (model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_tracking (id INT AUTO_INCREMENT NOT NULL, message_id INT DEFAULT NULL, datetime DATETIME DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, ipadress VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, emailadress VARCHAR(255) DEFAULT NULL, INDEX IDX_A31A7D55537A1329 (message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Models (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, group_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, meta_description LONGTEXT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, main_content LONGTEXT DEFAULT NULL, first_content LONGTEXT DEFAULT NULL, status TINYINT(1) NOT NULL, position INT NOT NULL, INDEX IDX_E37A353F44F5D008 (brand_id), INDEX IDX_E37A353FFE54D947 (group_id), INDEX model_idx (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ModelGroup (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, position INT NOT NULL, INDEX IDX_932E34ED44F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Page (id INT AUTO_INCREMENT NOT NULL, model_id INT DEFAULT NULL, brand_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, template VARCHAR(255) NOT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_keywords VARCHAR(255) DEFAULT NULL, meta_description LONGTEXT DEFAULT NULL, first_content LONGTEXT DEFAULT NULL, main_content LONGTEXT DEFAULT NULL, domain VARCHAR(255) NOT NULL, type_template VARCHAR(255) NOT NULL, INDEX IDX_B438191E7975B7E7 (model_id), INDEX IDX_B438191E44F5D008 (brand_id), INDEX domain_idx (domain), INDEX type_template_idx (type_template), UNIQUE INDEX slug_idx (slug, domain, type_template), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Redirect_url (id INT AUTO_INCREMENT NOT NULL, from_slug VARCHAR(255) NOT NULL, to_slug VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, domain VARCHAR(255) NOT NULL, type_template VARCHAR(255) NOT NULL, INDEX domain_idx (domain), INDEX type_template_idx (type_template), UNIQUE INDEX slug_idx (from_slug, domain, type_template), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Repair (id INT AUTO_INCREMENT NOT NULL, model_id INT DEFAULT NULL, repair_option_id INT DEFAULT NULL, price_nl NUMERIC(10, 2) NOT NULL, price_be NUMERIC(10, 2) NOT NULL, price_from_nl NUMERIC(10, 2) NOT NULL, price_from_be NUMERIC(10, 2) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX IDX_894831177975B7E7 (model_id), INDEX IDX_89483117E6A9FF76 (repair_option_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messages_repairs (repair_id INT NOT NULL, message_id INT NOT NULL, INDEX IDX_4E426FBA43833CFF (repair_id), INDEX IDX_4E426FBA537A1329 (message_id), PRIMARY KEY(repair_id, message_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE RepairOptions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, image VARCHAR(255) DEFAULT NULL, position INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Admin_users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_3235D1C492FC23A8 (username_canonical), UNIQUE INDEX UNIQ_3235D1C4A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_3235D1C4C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appointments ADD CONSTRAINT FK_6A41727A9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE email_messages ADD CONSTRAINT FK_D06401DF9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE email_messages ADD CONSTRAINT FK_D06401DF7975B7E7 FOREIGN KEY (model_id) REFERENCES Models (id)');
        $this->addSql('ALTER TABLE email_tracking ADD CONSTRAINT FK_A31A7D55537A1329 FOREIGN KEY (message_id) REFERENCES email_messages (id)');
        $this->addSql('ALTER TABLE Models ADD CONSTRAINT FK_E37A353F44F5D008 FOREIGN KEY (brand_id) REFERENCES Brands (id)');
        $this->addSql('ALTER TABLE Models ADD CONSTRAINT FK_E37A353FFE54D947 FOREIGN KEY (group_id) REFERENCES ModelGroup (id)');
        $this->addSql('ALTER TABLE ModelGroup ADD CONSTRAINT FK_932E34ED44F5D008 FOREIGN KEY (brand_id) REFERENCES Brands (id)');
        $this->addSql('ALTER TABLE Page ADD CONSTRAINT FK_B438191E7975B7E7 FOREIGN KEY (model_id) REFERENCES Models (id)');
        $this->addSql('ALTER TABLE Page ADD CONSTRAINT FK_B438191E44F5D008 FOREIGN KEY (brand_id) REFERENCES Brands (id)');
        $this->addSql('ALTER TABLE Repair ADD CONSTRAINT FK_894831177975B7E7 FOREIGN KEY (model_id) REFERENCES Models (id)');
        $this->addSql('ALTER TABLE Repair ADD CONSTRAINT FK_89483117E6A9FF76 FOREIGN KEY (repair_option_id) REFERENCES RepairOptions (id)');
        $this->addSql('ALTER TABLE messages_repairs ADD CONSTRAINT FK_4E426FBA43833CFF FOREIGN KEY (repair_id) REFERENCES Repair (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages_repairs ADD CONSTRAINT FK_4E426FBA537A1329 FOREIGN KEY (message_id) REFERENCES email_messages (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Models DROP FOREIGN KEY FK_E37A353F44F5D008');
        $this->addSql('ALTER TABLE ModelGroup DROP FOREIGN KEY FK_932E34ED44F5D008');
        $this->addSql('ALTER TABLE Page DROP FOREIGN KEY FK_B438191E44F5D008');
        $this->addSql('ALTER TABLE appointments DROP FOREIGN KEY FK_6A41727A9395C3F3');
        $this->addSql('ALTER TABLE email_messages DROP FOREIGN KEY FK_D06401DF9395C3F3');
        $this->addSql('ALTER TABLE email_tracking DROP FOREIGN KEY FK_A31A7D55537A1329');
        $this->addSql('ALTER TABLE messages_repairs DROP FOREIGN KEY FK_4E426FBA537A1329');
        $this->addSql('ALTER TABLE email_messages DROP FOREIGN KEY FK_D06401DF7975B7E7');
        $this->addSql('ALTER TABLE Page DROP FOREIGN KEY FK_B438191E7975B7E7');
        $this->addSql('ALTER TABLE Repair DROP FOREIGN KEY FK_894831177975B7E7');
        $this->addSql('ALTER TABLE Models DROP FOREIGN KEY FK_E37A353FFE54D947');
        $this->addSql('ALTER TABLE messages_repairs DROP FOREIGN KEY FK_4E426FBA43833CFF');
        $this->addSql('ALTER TABLE Repair DROP FOREIGN KEY FK_89483117E6A9FF76');
        $this->addSql('DROP TABLE appointments');
        $this->addSql('DROP TABLE Brands');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE Facebook_reviews');
        $this->addSql('DROP TABLE email_messages');
        $this->addSql('DROP TABLE email_tracking');
        $this->addSql('DROP TABLE Models');
        $this->addSql('DROP TABLE ModelGroup');
        $this->addSql('DROP TABLE Page');
        $this->addSql('DROP TABLE Redirect_url');
        $this->addSql('DROP TABLE Repair');
        $this->addSql('DROP TABLE messages_repairs');
        $this->addSql('DROP TABLE RepairOptions');
        $this->addSql('DROP TABLE Admin_users');
    }
}
