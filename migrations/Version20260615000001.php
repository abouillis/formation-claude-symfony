<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260615000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Schéma initial FluxCommerce — Catalog, Customer, Order, Billing';
    }

    public function up(Schema $schema): void
    {
        // Sequences
        $this->addSql('CREATE SEQUENCE categories_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE products_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE customers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE customer_tiers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE addresses_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE orders_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE order_lines_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE invoices_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE payments_id_seq INCREMENT BY 1 MINVALUE 1 START 1');

        // Catalog — categories
        $this->addSql('CREATE TABLE categories (
            id INT NOT NULL,
            parent_id INT DEFAULT NULL,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            is_active BOOLEAN NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CAT_SLUG ON categories (slug)');
        $this->addSql('CREATE INDEX IDX_CAT_PARENT ON categories (parent_id)');

        // Catalog — products
        $this->addSql('CREATE TABLE products (
            id INT NOT NULL,
            category_id INT DEFAULT NULL,
            reference VARCHAR(50) NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            price_amount INT NOT NULL,
            price_currency VARCHAR(3) NOT NULL,
            tax_rate INT NOT NULL,
            stock_quantity INT NOT NULL,
            is_active BOOLEAN NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_PROD_REF ON products (reference)');
        $this->addSql('CREATE INDEX IDX_PROD_CAT ON products (category_id)');

        // Customer — customers
        $this->addSql('CREATE TABLE customers (
            id INT NOT NULL,
            company_name VARCHAR(255) NOT NULL,
            siret VARCHAR(14) NOT NULL,
            email VARCHAR(180) NOT NULL,
            status VARCHAR(20) NOT NULL,
            credit_limit_amount INT NOT NULL,
            credit_limit_currency VARCHAR(3) NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CUST_EMAIL ON customers (email)');

        // Customer — customer_tiers
        $this->addSql('CREATE TABLE customer_tiers (
            id INT NOT NULL,
            customer_id INT NOT NULL,
            level VARCHAR(20) NOT NULL,
            valid_until TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_TIER_CUSTOMER ON customer_tiers (customer_id)');

        // Customer — addresses
        $this->addSql('CREATE TABLE addresses (
            id INT NOT NULL,
            customer_id INT NOT NULL,
            street VARCHAR(255) NOT NULL,
            city VARCHAR(100) NOT NULL,
            postal_code VARCHAR(10) NOT NULL,
            country VARCHAR(2) NOT NULL,
            type VARCHAR(20) NOT NULL,
            is_default BOOLEAN NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_ADDR_CUSTOMER ON addresses (customer_id)');

        // Order — orders
        $this->addSql('CREATE TABLE orders (
            id INT NOT NULL,
            customer_id INT NOT NULL,
            shipping_address_id INT DEFAULT NULL,
            status VARCHAR(20) NOT NULL,
            note TEXT DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            confirmed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_ORD_CUSTOMER ON orders (customer_id)');
        $this->addSql('CREATE INDEX IDX_ORD_SHIPPING ON orders (shipping_address_id)');

        // Order — order_lines
        $this->addSql('CREATE TABLE order_lines (
            id INT NOT NULL,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            unit_price_amount INT NOT NULL,
            unit_price_currency VARCHAR(3) NOT NULL,
            tax_rate INT NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_OL_ORDER ON order_lines (order_id)');
        $this->addSql('CREATE INDEX IDX_OL_PRODUCT ON order_lines (product_id)');

        // Billing — invoices
        $this->addSql('CREATE TABLE invoices (
            id INT NOT NULL,
            order_id INT NOT NULL,
            invoice_number VARCHAR(30) NOT NULL,
            status VARCHAR(20) NOT NULL,
            issued_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            due_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_INV_NUMBER ON invoices (invoice_number)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_INV_ORDER ON invoices (order_id)');

        // Billing — payments
        $this->addSql('CREATE TABLE payments (
            id INT NOT NULL,
            invoice_id INT NOT NULL,
            amount INT NOT NULL,
            currency VARCHAR(3) NOT NULL,
            method VARCHAR(30) NOT NULL,
            paid_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            reference VARCHAR(100) DEFAULT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_PAY_INVOICE ON payments (invoice_id)');

        // Default sequences for PKs
        $this->addSql('ALTER TABLE categories ALTER id SET DEFAULT nextval(\'categories_id_seq\')');
        $this->addSql('ALTER TABLE products ALTER id SET DEFAULT nextval(\'products_id_seq\')');
        $this->addSql('ALTER TABLE customers ALTER id SET DEFAULT nextval(\'customers_id_seq\')');
        $this->addSql('ALTER TABLE customer_tiers ALTER id SET DEFAULT nextval(\'customer_tiers_id_seq\')');
        $this->addSql('ALTER TABLE addresses ALTER id SET DEFAULT nextval(\'addresses_id_seq\')');
        $this->addSql('ALTER TABLE orders ALTER id SET DEFAULT nextval(\'orders_id_seq\')');
        $this->addSql('ALTER TABLE order_lines ALTER id SET DEFAULT nextval(\'order_lines_id_seq\')');
        $this->addSql('ALTER TABLE invoices ALTER id SET DEFAULT nextval(\'invoices_id_seq\')');
        $this->addSql('ALTER TABLE payments ALTER id SET DEFAULT nextval(\'payments_id_seq\')');

        // Foreign keys
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_CAT_PARENT FOREIGN KEY (parent_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_PROD_CAT FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer_tiers ADD CONSTRAINT FK_TIER_CUSTOMER FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE addresses ADD CONSTRAINT FK_ADDR_CUSTOMER FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_ORD_CUSTOMER FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_ORD_SHIPPING FOREIGN KEY (shipping_address_id) REFERENCES addresses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_lines ADD CONSTRAINT FK_OL_ORDER FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_lines ADD CONSTRAINT FK_OL_PRODUCT FOREIGN KEY (product_id) REFERENCES products (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoices ADD CONSTRAINT FK_INV_ORDER FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_PAY_INVOICE FOREIGN KEY (invoice_id) REFERENCES invoices (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_PAY_INVOICE');
        $this->addSql('ALTER TABLE invoices DROP CONSTRAINT FK_INV_ORDER');
        $this->addSql('ALTER TABLE order_lines DROP CONSTRAINT FK_OL_ORDER');
        $this->addSql('ALTER TABLE order_lines DROP CONSTRAINT FK_OL_PRODUCT');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_ORD_CUSTOMER');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_ORD_SHIPPING');
        $this->addSql('ALTER TABLE addresses DROP CONSTRAINT FK_ADDR_CUSTOMER');
        $this->addSql('ALTER TABLE customer_tiers DROP CONSTRAINT FK_TIER_CUSTOMER');
        $this->addSql('ALTER TABLE products DROP CONSTRAINT FK_PROD_CAT');
        $this->addSql('ALTER TABLE categories DROP CONSTRAINT FK_CAT_PARENT');

        $this->addSql('DROP TABLE payments');
        $this->addSql('DROP TABLE invoices');
        $this->addSql('DROP TABLE order_lines');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE addresses');
        $this->addSql('DROP TABLE customer_tiers');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE categories');

        $this->addSql('DROP SEQUENCE payments_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE invoices_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE order_lines_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE orders_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE addresses_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE customer_tiers_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE customers_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE products_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE categories_id_seq CASCADE');
    }
}
