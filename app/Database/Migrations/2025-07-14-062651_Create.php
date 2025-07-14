<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Create extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ]);

        $this->forge->createTable('password_reset_tokens', true); // Create table if it doesn't exist
    }

    public function down()
    {
        $this->forge->dropTable('password_reset_tokens', true);
    }
}
