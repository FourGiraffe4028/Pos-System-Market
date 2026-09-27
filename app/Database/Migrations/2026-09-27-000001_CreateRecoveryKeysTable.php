<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRecoveryKeysTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'key_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'label' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => false,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'expires_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
            'used_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'is_used' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('recovery_keys', true);
    }

    public function down()
    {
        $this->forge->dropTable('recovery_keys', true);
    }
}
