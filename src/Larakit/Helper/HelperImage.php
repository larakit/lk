<?php
namespace Larakit\Helper;

class HelperImage {

    /**
     * Вписываем изображение в указанную ширину
     * Высота какая получится такая и будет
     * Пример: фотки на аватарках в контактике
     *
     * @param \Intervention\Image\Image $img
     * @param                           $w
     * @param bool                      $can_upsize
     *
     * @return \Intervention\Image\Image
     */
    static function resizeByWidth(\Intervention\Image\Image $img, $w, $can_upsize = true) {
        return $img->resize(
            $w, null, function ($constraint) use ($can_upsize) {
                if ($can_upsize) {
                    $constraint->upsize();
                }
                $constraint->aspectRatio();
            }
        );
    }

    /**
     * Насильно вписываем изображение без учета пропорций в указанные рамки
     *
     * @param \Intervention\Image\Image $img
     * @param                           $w
     * @param                           $h
     *
     * @return \Intervention\Image\Image
     */
    static function resizeIgnoringAspectRatio(\Intervention\Image\Image $img, $w, $h) {
        return $img->resize($w, $h);
    }

    /**
     * Исходная картинка сжимается до тех пор пока не начнет целиком входить
     * в указанные рамки
     * С сохранением пропорций
     *
     * @param type $w
     * @param type $h
     *
     * @return \Image
     */
    static function resizeImgInBox(\Intervention\Image\Image $img, $w, $h, $can_upsize = true) {
        return $img->resize(
            $w, $h, function ($constraint) use ($can_upsize) {
                if ($can_upsize) {
                    $constraint->upsize();
                }
                $constraint->aspectRatio();
            }
        );
    }

    /**
     * Уменьшаем размер исходного изображения с сохранением пропорций так,
     * чтобы новое получилось вписанным в указанный размер
     * Там где изображение уже отсутствует - добиваем белым цветом до указанного размера
     *
     * @param type $width
     * @param type $height
     *
     * @return \Image
     */
    static function cropImgInBox(\Intervention\Image\Image $img, $width, $height) {
        //сделаем так, чтобы исходная картинка вписывалась большей стороной в указанный прямоугольник
        $img = self::resizeImgInBox($img, $width, $height);
        return \Image::canvas($width, $height)
                     ->insert($img, 'center-center');
    }

    /**
     * Уменьшаем размер исходного изображения с сохранением пропорций так,
     * чтобы новое получилось описанным вокруг указанного размера
     * Там где изображение будет за границами рамки оно будет просто обрезано с центровкой посредине картинки
     *
     * @param \Intervention\Image\Image $img
     * @param                           $width
     * @param                           $height
     *
     * @return \Intervention\Image\Image
     */
    static function cropBoxInImg(\Intervention\Image\Image $img, $width, $height) {
        //сделаем так, чтобы исходная картинка вписывалась большей стороной в указанный прямоугольник
        $img = self::resizeBoxInImg($img, $width, $height);
        return $img->crop($width, $height);
    }

    /**
     * Указанная рамка должна помещаться внутрь конечного изображения
     * Т.е. если заказываем 100 на 400 а картинка 2000 на 1000
     * То картинка будет уменьшаться до тех пор пока ее высота меньше указанного
     * или ширина меньше указанного
     *
     * @param \Intervention\Image\Image $img
     * @param                           $w
     * @param                           $h
     *
     * @return \Intervention\Image\Image
     */
    static function resizeBoxInImg(\Intervention\Image\Image $img, $w, $h) {
        $ratio_image = $img->width() / $img->height();
        $ratio_box   = $w / $h;
        if ($ratio_box < $ratio_image) {
            $_h = $h;
            $_w = null;
        }
        else {
            $_w = $w;
            $_h = null;
        }
        return $img->resize(
            $_w, $_h, function ($constraint) {
                $constraint->aspectRatio();
            }
        );
    }

    static function original(\Intervention\Image\Image $img, $w, $h) {
        $max = max($w, $h);
        return $img->resizeCanvas($max * 2, $max * 2);
    }

}