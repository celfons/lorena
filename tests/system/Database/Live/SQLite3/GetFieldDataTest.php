<?php

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Database\Live\SQLite3;

use CodeIgniter\Database\Live\AbstractGetFieldDataTest;
use Config\Database;

/**
 * @group DatabaseLive
 *
 * @internal
 */
final class GetFieldDataTest extends AbstractGetFieldDataTest
{
    protected function createForge(): void
    {
        if ($this->db->DBDriver !== 'SQLite3') {
            $this->markTestSkipped('This test is only for SQLite3.');
        }

        $config = [
            'DBDriver' => 'SQLite3',
            'database' => 'database.db',
            'DBDebug'  => true,
        ];
        $this->db    = db_connect($config);
        $this->forge = Database::forge($config);
    }

    public function testGetFieldDataDefault(): void
    {
        $this->createTableForDefault();

        $fields = $this->db->getFieldData($this->table);

        $expected = [
            (object) [
                'name'        => 'id',
                'type'        => 'INTEGER',
                'max_length'  => null,
                'nullable'    => true,
                'default'     => null, // The default value is not defined.
                'primary_key' => 1,
            ],
            (object) [
                'name'        => 'text_not_null',
                'type'        => 'VARCHAR',
                'max_length'  => null,
                'nullable'    => false,
                'default'     => null, // The default value is not defined.
                'primary_key' => 0,
            ],
            (object) [
                'name'        => 'text_null',
                'type'        => 'VARCHAR',
                'max_length'  => null,
                'nullable'    => true,
                'default'     => null, // The default value is not defined.
                'primary_key' => 0,
            ],
            (object) [
                'name'        => 'int_default_0',
                'type'        => 'INT',
                'max_length'  => null,
                'nullable'    => false,
                'default'     => '0', // int 0
                'primary_key' => 0,
            ],
            (object) [
                'name'        => 'text_default_null',
                'type'        => 'VARCHAR',
                'max_length'  => null,
                'nullable'    => true,
                'default'     => 'NULL', // NULL value
                'primary_key' => 0,
            ],
            (object) [
                'name'        => 'text_default_text_null',
                'type'        => 'VARCHAR',
                'max_length'  => null,
                'nullable'    => false,
                'default'     => "'null'", // string "null"
                'primary_key' => 0,
            ],
            (object) [
                'name'        => 'text_default_abc',
                'type'        => 'VARCHAR',
                'max_length'  => null,
                'nullable'    => false,
                'default'     => "'abc'", // string "abc"
                'primary_key' => 0,
            ],
        ];
        $this->assertSameFieldData($expected, $fields);
    }

    protected function createTableCompositePrimaryKey()
    {
        $this->forge->dropTable($this->table, true);

        $this->forge->addField([
            'pk1' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
            ],
            'pk2' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
            ],
            'text' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
            ],
        ]);
        $this->forge->addPrimaryKey(['pk1', 'pk2']);
        $this->forge->createTable($this->table);
    }

    public function testGetFieldDataCompositePrimaryKey(): void
    {
        $this->createTableCompositePrimaryKey();

        $fields = $this->db->getFieldData($this->table);

        $expected = [
            (object) [
                'name'        => 'pk1',
                'type'        => 'VARCHAR',
                'max_length'  => null,
                'nullable'    => false,
                'default'     => null,
                'primary_key' => 1,
            ],
            (object) [
                'name'        => 'pk2',
                'type'        => 'VARCHAR',
                'max_length'  => null,
                'nullable'    => false,
                'default'     => null,
                'primary_key' => 1,
            ],
            (object) [
                'name'        => 'text',
                'type'        => 'VARCHAR',
                'max_length'  => null,
                'nullable'    => false,
                'default'     => null,
                'primary_key' => 0,
            ],
        ];
        $this->assertSameFieldData($expected, $fields);
    }
}
