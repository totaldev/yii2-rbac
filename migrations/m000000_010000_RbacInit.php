<?php

namespace totaldev\yii\rbac\migrations;

use totaldev\yii\usefull\db\MysqlDumpRead;
use yii\db\Migration;

class m000000_010000_RbacInit extends Migration
{
    use MysqlDumpRead;

    public function down()
    {
        $this->dropTable('RbacAuthAssignment');
        $this->dropTable('RbacAuthItemChild');
        $this->dropTable('RbacAuthItem');
        $this->dropTable('RbacAuthRule');
    }

    public function up()
    {
        $this->applyDumpFile(__DIR__ . '/_scheme.sql');
    }
}
