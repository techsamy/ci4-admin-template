<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'website_title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'website_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'website_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'website_meta_keywords' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'website_meta_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'website_logo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'website_favicon' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', true); // Primary key
        $this->forge->createTable('settings', true); // Create table if it doesn't exist
    }

    public function down()
    {
        $this->forge->dropTable('settings', true); // Drop table if it exists
    }
}
