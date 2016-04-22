<?php
namespace Larakit\Base;

use Larakit\Event;
use Larakit\Manager\ManagerRowType;

Trait TraitControllerEntityItemRestore
{

    function traitEntityItemRestore_success()
    {
        return static::translate('actions.item|restore.success', ['model' => $this->model]);
    }

    static function getEntitySuffix()
    {
        return 'item_restore';
    }

    function traitEntityRestore()
    {
        $this->traitEntity_assertReason($this->model, 'restore');
        $this->model->restore();
        $row_types = ManagerRowType::get(ManagerRowType::makeKey(static::getVendor(), static::getEntity()));
        $rows      = [];
        foreach ($row_types as $row_type) {
            $rows[$row_type] = (string)Accessor::factory($this->model)->row($row_type);
        }
        $data = [
            'models'  => [
                [
                    'model' => $this->model->toArray(),
                    'rows'  => $rows,
                    'id'    => $this->model->id,
                ],
            ],
            'vendor'  => (string)static::getVendor(),
            'entity'  => \Str::snake($this->getEntity()),
            'message' => sprintf($this->traitEntityItemRestore_success(), $this->model),
            'state'   => 1,
        ];
        Event::notify('larakit::model_restore-admin', $data);
        $this->traitAjax_set($data);
        return $this->traitAjax_response();
    }

}