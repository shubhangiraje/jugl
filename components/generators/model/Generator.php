<?php

namespace app\components\generators\model;

use yii\gii\CodeFile;
use Yii;

class Generator extends yii\gii\generators\model\Generator {

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Model Generator (Pavimus)';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates an ActiveRecord class for the specified database table.';
    }

    public function generate()
    {
        $files=parent::generate();

        // add base model templates
        $db = $this->getDbConnection();
        $relations = $this->generateRelations();


        foreach ($this->getTableNames() as $tableName) {
            $className = $this->generateClassName($tableName);
            $tableSchema = $db->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $className,
                'tableSchema' => $tableSchema,
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
            ];

            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/base/' . $className . '.php',
                $this->render('base/model.php', $params)
            );
        }

        return $files;
    }

}