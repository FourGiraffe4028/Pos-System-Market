<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusAndKasirToReturTable extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('retur', [
            'user_id_spv' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
                'null'       => true,
            ],
        ]);

        $this->forge->addColumn('retur', [
            'user_id_kasir' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
                'null'       => true,
                'after'      => 'id_penjualan',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
                'after'      => 'alasan',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('retur', ['user_id_kasir', 'status']);
        $this->forge->modifyColumn('retur', [
            'user_id_spv' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
                'null'       => false,
            ],
        ]);
    }
}

