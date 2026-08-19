<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlignPluralTableNames extends Migration
{
    public function up()
    {
        // Preserve existing category data.
        if (
            $this->db->tableExists('category') &&
            ! $this->db->tableExists('categories')
        ) {
            $this->forge->renameTable('category', 'categories');
        }

        // Preserve existing image data.
        if (
            $this->db->tableExists('image') &&
            ! $this->db->tableExists('images')
        ) {
            $this->forge->renameTable('image', 'images');
        }
    }

    public function down()
    {
        if (
            $this->db->tableExists('categories') &&
            ! $this->db->tableExists('category')
        ) {
            $this->forge->renameTable('categories', 'category');
        }

        if (
            $this->db->tableExists('images') &&
            ! $this->db->tableExists('image')
        ) {
            $this->forge->renameTable('images', 'image');
        }
    }
}
