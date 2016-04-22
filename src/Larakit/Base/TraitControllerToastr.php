<?php
namespace Larakit\Base;

/**
 * Created by PhpStorm.
 * User: berdnikov_ay
 * Date: 04.08.2015
 * Time: 8:48
 */
trait TraitControllerToastr {
    function traitToastr_NotAutoClosed() {
        return $this->traitAjax_set('toastr_options.timeOut', 0);
    }

    function traitToastr_positionCenterCenter() {
        return $this->traitAjax_set('toastr_options.positionClass', 'toastr-center');
    }

    function traitToastr_positionTopCenter() {
        return $this->traitAjax_set('toastr_options.positionClass', 'toast-top-center');
    }

    function traitToastr_positionBottomCenter() {
        return $this->traitAjax_set('toastr_options.positionClass', 'toast-bottom-center');
    }

    function traitToastr_positionTopFullWidth() {
        return $this->traitAjax_set('toastr_options.positionClass', 'toast-top-full-width');
    }

    function traitToastr_positionBottomFullWidth() {
        return $this->traitAjax_set('toastr_options.positionClass', 'toast-bottom-full-width');
    }


    function traitToastr_positionTopLeft() {
        return $this->traitAjax_set('toastr_options.positionClass', 'toast-top-left');
    }

    function traitToastr_positionTopLeftRight() {
        return $this->traitAjax_set('toastr_options.positionClass', 'toast-top-right');
    }

    function traitToastr_positionBottomRight() {
        return $this->traitAjax_set('toastr_options.positionClass', 'toast-bottom-right');
    }

    function traitToastr_positionBottomLeft() {
        return $this->traitAjax_set('toastr_options.positionClass', 'toast-bottom-left');
    }

}
