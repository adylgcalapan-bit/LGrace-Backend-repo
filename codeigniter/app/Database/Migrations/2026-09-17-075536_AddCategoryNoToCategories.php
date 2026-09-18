<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCategoryNoToCategories extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('category_no', 'categories')) {

            $this->forge->addColumn('categories', [
                'category_no' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'category_id',
                ],
            ]);

            // Give existing categories their current visible number
            // one time only.
            $categories = $this->db->table('categories')
                ->select('category_id')
                ->orderBy('category_id', 'ASC')
                ->get()
                ->getResultArray();

            $number = 1;

            foreach ($categories as $category) {

                $this->db->table('categories')
                    ->where(
                        'category_id',
                        $category['category_id']
                    )
                    ->update([
                        'category_no' => $number,
                    ]);

                $number++;
            }

            // category_no must always have a value
            $this->db->query("
                ALTER TABLE categories
                MODIFY category_no INT UNSIGNED NOT NULL
            ");

            // Prevent duplicate Category Nos.
            $this->db->query("
                ALTER TABLE categories
                ADD UNIQUE KEY uq_categories_category_no (category_no)
            ");
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('category_no', 'categories')) {

            $this->db->query("
                ALTER TABLE categories
                DROP INDEX uq_categories_category_no
            ");

            $this->forge->dropColumn(
                'categories',
                'category_no'
            );
        }
    }
}
