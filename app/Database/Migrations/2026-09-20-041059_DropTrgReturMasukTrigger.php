<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropTrgReturMasukTrigger extends Migration
{
    public function up()
    {
        $this->db->query("DROP TRIGGER IF EXISTS `trg_retur_masuk`");
    }

    public function down()
    {
        $this->db->query("
            CREATE TRIGGER `trg_retur_masuk` AFTER INSERT ON `detail_retur`
            FOR EACH ROW BEGIN
                IF NEW.`kondisi_barang` = 'kembali_stok' THEN
                    UPDATE `barang` SET `stok` = `stok` + NEW.`jumlah_retur` WHERE `id_barang` = NEW.`id_barang`;
                END IF;
            END
        ");
    }
}

