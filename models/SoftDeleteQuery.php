<?php

namespace app\models;

use yii\db\ActiveQuery;

/** Default scope is applied at SQL preparation, so where()/findOne() cannot replace it. */
class SoftDeleteQuery extends ActiveQuery
{
    private bool $includeDeleted = false;

    public function withDeleted()
    {
        $this->includeDeleted = true;
        return $this;
    }

    public function prepare($builder)
    {
        $query = parent::prepare($builder);
        if (!$this->includeDeleted) {
            [, $alias] = $this->getTableNameAndAlias();
            $query->andWhere([$alias . '.status_registro' => SoftDeleteRecord::REGISTRO_ACTIVO]);
            foreach ($this->modelClass::visibilityParents() as $foreignKey => $parentClass) {
                $query->andWhere([$alias . '.' . $foreignKey => $parentClass::find()->select('id')]);
            }
        }
        return $query;
    }
}
