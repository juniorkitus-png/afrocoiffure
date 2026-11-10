<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261110173239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(80) NOT NULL, slug VARCHAR(90) NOT NULL, UNIQUE INDEX UNIQ_CATEGORY_NAME (name), UNIQUE INDEX UNIQ_CATEGORY_SLUG (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact_request (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, phone VARCHAR(30) DEFAULT NULL, message LONGTEXT NOT NULL, desired_date DATE DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, hairdresser_id INT NOT NULL, sender_id INT DEFAULT NULL, service_id INT DEFAULT NULL, INDEX IDX_CONTACT_STATUS (status), INDEX IDX_A1B8AE1E696F8EFF (hairdresser_id), INDEX IDX_A1B8AE1EF624B39D (sender_id), INDEX IDX_A1B8AE1EED5CA9E6 (service_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hairdresser (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, slug VARCHAR(170) NOT NULL, description LONGTEXT DEFAULT NULL, address VARCHAR(255) NOT NULL, postal_code VARCHAR(4) NOT NULL, city VARCHAR(100) NOT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, phone VARCHAR(30) DEFAULT NULL, workplace_type VARCHAR(20) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_407D6245A76ED395 (user_id), INDEX IDX_HAIRDRESSER_CITY (city), INDEX IDX_HAIRDRESSER_STATUS (status), UNIQUE INDEX UNIQ_HAIRDRESSER_SLUG (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE opening_hours (id INT AUTO_INCREMENT NOT NULL, day_of_week SMALLINT NOT NULL, open_time TIME DEFAULT NULL, close_time TIME DEFAULT NULL, is_closed TINYINT NOT NULL, hairdresser_id INT NOT NULL, UNIQUE INDEX UNIQ_HAIRDRESSER_DAY (hairdresser_id, day_of_week), INDEX IDX_2640C10B696F8EFF (hairdresser_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE portfolio_photo (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, caption VARCHAR(150) DEFAULT NULL, is_reported TINYINT NOT NULL, created_at DATETIME NOT NULL, hairdresser_id INT NOT NULL, speciality_id INT DEFAULT NULL, INDEX IDX_PHOTO_REPORTED (is_reported), INDEX IDX_49EFAE5D696F8EFF (hairdresser_id), INDEX IDX_49EFAE5D3B5A08D7 (speciality_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, rating SMALLINT NOT NULL, comment LONGTEXT NOT NULL, status VARCHAR(20) NOT NULL, is_reported TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, author_id INT NOT NULL, hairdresser_id INT NOT NULL, INDEX IDX_REVIEW_STATUS (status), UNIQUE INDEX UNIQ_REVIEW_AUTHOR_HAIRDRESSER (author_id, hairdresser_id), INDEX IDX_794381C6F675F31B (author_id), INDEX IDX_794381C6696F8EFF (hairdresser_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, price NUMERIC(6, 2) NOT NULL, duration_minutes SMALLINT NOT NULL, description LONGTEXT DEFAULT NULL, hairdresser_id INT NOT NULL, speciality_id INT NOT NULL, INDEX IDX_E19D9AD2696F8EFF (hairdresser_id), INDEX IDX_E19D9AD23B5A08D7 (speciality_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE speciality (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(80) NOT NULL, slug VARCHAR(90) NOT NULL, category_id INT NOT NULL, UNIQUE INDEX UNIQ_SPECIALITY_NAME (name), UNIQUE INDEX UNIQ_SPECIALITY_SLUG (slug), INDEX IDX_F3D7A08E12469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, phone VARCHAR(30) DEFAULT NULL, consent_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_favorite (user_id INT NOT NULL, hairdresser_id INT NOT NULL, INDEX IDX_88486AD9A76ED395 (user_id), INDEX IDX_88486AD9696F8EFF (hairdresser_id), PRIMARY KEY (user_id, hairdresser_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE contact_request ADD CONSTRAINT FK_A1B8AE1E696F8EFF FOREIGN KEY (hairdresser_id) REFERENCES hairdresser (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact_request ADD CONSTRAINT FK_A1B8AE1EF624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE contact_request ADD CONSTRAINT FK_A1B8AE1EED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE hairdresser ADD CONSTRAINT FK_407D6245A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE opening_hours ADD CONSTRAINT FK_2640C10B696F8EFF FOREIGN KEY (hairdresser_id) REFERENCES hairdresser (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE portfolio_photo ADD CONSTRAINT FK_49EFAE5D696F8EFF FOREIGN KEY (hairdresser_id) REFERENCES hairdresser (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE portfolio_photo ADD CONSTRAINT FK_49EFAE5D3B5A08D7 FOREIGN KEY (speciality_id) REFERENCES speciality (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6F675F31B FOREIGN KEY (author_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6696F8EFF FOREIGN KEY (hairdresser_id) REFERENCES hairdresser (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2696F8EFF FOREIGN KEY (hairdresser_id) REFERENCES hairdresser (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD23B5A08D7 FOREIGN KEY (speciality_id) REFERENCES speciality (id)');
        $this->addSql('ALTER TABLE speciality ADD CONSTRAINT FK_F3D7A08E12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE user_favorite ADD CONSTRAINT FK_88486AD9A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_favorite ADD CONSTRAINT FK_88486AD9696F8EFF FOREIGN KEY (hairdresser_id) REFERENCES hairdresser (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact_request DROP FOREIGN KEY FK_A1B8AE1E696F8EFF');
        $this->addSql('ALTER TABLE contact_request DROP FOREIGN KEY FK_A1B8AE1EF624B39D');
        $this->addSql('ALTER TABLE contact_request DROP FOREIGN KEY FK_A1B8AE1EED5CA9E6');
        $this->addSql('ALTER TABLE hairdresser DROP FOREIGN KEY FK_407D6245A76ED395');
        $this->addSql('ALTER TABLE opening_hours DROP FOREIGN KEY FK_2640C10B696F8EFF');
        $this->addSql('ALTER TABLE portfolio_photo DROP FOREIGN KEY FK_49EFAE5D696F8EFF');
        $this->addSql('ALTER TABLE portfolio_photo DROP FOREIGN KEY FK_49EFAE5D3B5A08D7');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6F675F31B');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6696F8EFF');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2696F8EFF');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD23B5A08D7');
        $this->addSql('ALTER TABLE speciality DROP FOREIGN KEY FK_F3D7A08E12469DE2');
        $this->addSql('ALTER TABLE user_favorite DROP FOREIGN KEY FK_88486AD9A76ED395');
        $this->addSql('ALTER TABLE user_favorite DROP FOREIGN KEY FK_88486AD9696F8EFF');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE contact_request');
        $this->addSql('DROP TABLE hairdresser');
        $this->addSql('DROP TABLE opening_hours');
        $this->addSql('DROP TABLE portfolio_photo');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE speciality');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_favorite');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
