<?php
namespace Larakit\Base;


use Larakit\Thumb;

Trait TraitControllerEntityAjaxAutocomplete {

    function getEntitySuffix() {
        return 'autocomplete';
    }

    function getEntityPrefix() {
        return 'ajax_admin';
    }

    function traitControllerEntityAjaxAutoComplete_response() {
        $word  = trim(\Input::get('query', false));
        $items = [];
        if ($word) {
            $list = $this->model->where('name', 'like', '%' . $word . '%')
                                ->listsExt();

            if (is_array($list)) {
                foreach ($list as $id => $value) {
                    $items[$id]['id']        = $id;
                    $items[$id]['thumb_url'] = Thumb::factory(
                        $this->getVendor(),
                        $this->getEntity(),
                        $id,
                        'item'
                    )
                                                    ->getUrl('small');
                    $items[$id]['value']     = $value;
                }
            }
        }
        else {
            return [
                'items' => []
            ];
        }

        sort($items);

        return [
            'total_count'        => sizeof($items),
            'incomplete_results' => false,
            'items'              => $items
        ];
    }

}
