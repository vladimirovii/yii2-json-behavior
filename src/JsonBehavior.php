<?php
namespace paulzi\jsonBehavior;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 *  @property ActiveRecord $owner
 */
class JsonBehavior extends Behavior
{
    /**
     * @var array
     */
    public $attributes = [];


    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_INIT          => function () { $this->initialization(); },
            ActiveRecord::EVENT_AFTER_FIND    => function () { $this->decode(); },
            ActiveRecord::EVENT_BEFORE_INSERT => function () { $this->encode(); },
            ActiveRecord::EVENT_BEFORE_UPDATE => function () { $this->encode(); },
            ActiveRecord::EVENT_AFTER_INSERT  => function () { $this->decode(); },
            ActiveRecord::EVENT_AFTER_UPDATE  => function () { $this->decode(); },
        ];
    }

    /**
     */
    protected function initialization()
    {
        foreach ($this->attributes as $attribute) {
            $this->owner->setAttribute($attribute, new JsonField());
        }
    }

    /**
     */
    protected function decode()
    {
        foreach ($this->attributes as $attribute) {
            $value = $this->owner->getAttribute($attribute);
            if (!$value instanceof JsonField) {
                $value = new JsonField($value);
            }
            $this->owner->setAttribute($attribute, $value);
        }
    }

    /**
     */
    protected function encode()
    {
        foreach ($this->attributes as $attribute) {
            $field = $this->owner->getAttribute($attribute);
            if (!$field instanceof JsonField) {
                $field = new JsonField($field);
            }
            $this->owner->setAttribute($attribute, (string)$field ?: null);
        }
    }
}
