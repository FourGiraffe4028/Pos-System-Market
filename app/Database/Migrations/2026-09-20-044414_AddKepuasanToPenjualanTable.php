<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKepuasanToPenjualanTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('penjualan', [
            'kepuasan' => [
                'type'       => 'ENUM',
                'constraint' => ['puas', 'tidak_puas'],
                'null'       => true,
                'after'      => 'status',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('penjualan', 'kepuasan');
    }
}

