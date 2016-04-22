<?php
namespace Larakit\Base;

use Larakit\Event;
use Larakit\Manager\ManagerRowType;

Trait TraitControllerEntityItemDelete {
    function traitEntityItemDelete_successHardDelete() {
        return static::translate('actions.item|delete.success.hard', ['model' => $this->model]);
    }

    function traitEntityItemDelete_successSoftDelete() {
        return static::translate('actions.item|delete.success.soft', ['model' => $this->model]);
    }

    static function getEntitySuffix() {
        return 'item_delete';
    }

    function traitEntityItemDelete() {
        $this->traitEntity_assertReason($this->model, 'delete');

        $data = [
            'vendor' => (string)static::getVendor(),
            'entity' => \Str::snake($this->getEntity()),
        ];


        if ($this->model->deleted_at) {
            //            $this->model->restore();
            //            dd();
            $data['state']    = -1;
            $data['message'] = static::translateAction('item|delete.success.hard', ['model'=>$this->model]);
            $data['models'][] = [
                'id' => $this->model->id,
            ];
            $this->model->forceDelete();
        } else {
            $this->model->delete();
            $data['state']   = 0;
            $data['message'] = static::translateAction('item|delete.success.soft', ['model'=>$this->model]);

            $row_types = ManagerRowType::get(ManagerRowType::makeKey(static::getVendor(), static::getEntity()));
            $rows      = [];
            foreach ($row_types as $row_type) {
                $rows[$row_type] = (string)Accessor::factory($this->model)->row($row_type);
            }

            $data['models'][] = [
                'id'    => $this->model->id,
                'model' => $this->model->toArray(),
                'rows'  => $rows,
            ];
        }

        $this->traitAjax_set($data);
        Event::notify('larakit::model_delete-admin', $data);
        return $this->traitAjax_response();
    }

}