<?php

namespace bedezign\yii2\audit\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use bedezign\yii2\audit\models\AuditError;

class AuditErrorSearch extends AuditError
{
    public function rules()
    {
        // only fields in rules() are searchable
        return [
            [['id', 'entry_id', 'file', 'line'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = AuditError::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]
        ]);

        // load the search form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // adjust the query by adding the filters
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['entry_id' => $this->entry_id]);
        $query->andFilterWhere(['like', 'file', $this->file]);
        $query->andFilterWhere(['line' => $this->line]);

        return $dataProvider;
    }

    static public function fileFilter()
    {
        $files = AuditEntry::getDb()->cache(function($db) {
            return AuditError::find()->distinct(true)
                ->select('file')->where(['is not', 'file', null])
                ->groupBy('file')->orderBy('file ASC')->column();
        }, 30);
        return array_combine($files, $files);
    }

}
