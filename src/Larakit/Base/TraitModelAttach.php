<?php
namespace Larakit\Base;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Larakit\Attach;
use Larakit\Exception;
use Larakit\Helper\HelperFile;
use Larakit\Helper\HelperText;
use Larakit\User\Me;
use Symfony\Component\HttpFoundation\File\UploadedFile;

Trait TraitModelAttach {


    static function bootTraitModelAttach() {
        /** @var Model $class */
        $class = get_called_class();
        $class::observe(new ObserverModelAttach());
    }

    function attachToObject($model) {
        /** @var Model $model */
        $model->attaches()->save($this);
    }

    /****************************************************************
     * хозяин аттача
     ****************************************************************/
    function attachOwner() {
        /** @var Model $this */
        return $this->belongsTo('Larakit\Model\User', 'user_id');
    }


    function attachConfig() {
        return static::config('attach');
    }

    function attachFile($source) {
        /** @var Model $this */
        \DB::beginTransaction();
        try {
            if ($source instanceof UploadedFile) {
                //                $source    = new UploadedFile(1, 1);
                $ext       = $source->getClientOriginalExtension();
                $file_name = mb_substr($source->getClientOriginalName(), 0, 0 - mb_strlen($ext) - 1);
            } else {
                $ext       = \File::extension($source);
                $file_name = \File::name($source);
            }
            $ext = mb_strtolower($ext);

            $tmp      = file_get_contents($source);
            $tmp_name = storage_path('/attaches/' . date('Y_m_d_H-i-s') . '/' . \Str::slug($this->getMorphClass()) . '.' . $ext);
            if (!file_exists(dirname($tmp_name))) {
                mkdir(dirname($tmp_name), 0777, true);
            }
            file_put_contents($tmp_name, $tmp);
            unset($tmp);
            if (!file_exists($tmp_name)) {
                throw new Exception(laralang('larakit::attach.errors.exists',
                    ['file' => larasafepath($tmp_name)]));
            }
            $attach = Attach\Attach::fromModel($this);
            $config = $this->attachConfig();
            //проверка на максимальный размер
            $maxsize = Arr::get($config, 'maxsize');
            $size    = \File::size($tmp_name);
            if ($maxsize < $size) {
                throw new Exception(laralang('larakit::attach.errors.maxsize',
                    ['maxsize' => HelperText::fileSize($maxsize, 0)]));
            }
            //проверка на разрешенные EXT
            $enabled_exts = Arr::get($config, 'ext');
            if (!in_array($ext, $enabled_exts)) {
                throw new Exception(laralang('larakit::attach.errors.ext',
                    ['enabled_exts' => implode(', ', $enabled_exts)]));
            }
            //проверка на разрешенные MIME
            $expected_mimes = HelperFile::mimes_by_ext($ext);
            $mime           = HelperFile::mime($tmp_name);
            if (!in_array($mime, $expected_mimes)) {
                throw new Exception(laralang('larakit::attach.errors.ext',
                    ['enabled_exts' => implode(', ', $enabled_exts)]));
            }
            $img = getimagesize($tmp_name);
            if (false !== $img) {
                $this->attach_w = Arr::get($img, 0);
                $this->attach_h = Arr::get($img, 1);
            }
            $this->attach_user_id = Me::id();
            $this->attach_ext     = $ext;
            $this->attach_size    = $size;
            $this->attach_mime    = $mime;
            $this->attach_file    = $file_name . '.' . $ext;
            $this->attach_name    = $file_name;
            $this->save();
            $attach->setId($this->id)->processing($tmp_name);
            \DB::commit();
            $val = true;
        } catch (\Exception $e) {
            \DB::rollBack();
            $val = $e->getMessage();
        }
        if (file_exists($tmp_name)) {
            if (false !== mb_strpos($source, '//')) {
                unlink($tmp_name);
            }
        }
        return $val;
    }

    public function attachable() {
        /** @var Model $this */
        return $this->morphTo();
    }


    protected $_attach_columns = [
        'attachable_id',
        'attachable_type',
        'attach_user_id',
        'attach_ext',
        'attach_size',
        'attach_mime',
        'attach_file',
        'attach_name',
        'attach_w',
        'attach_h',
    ];
    static    $attach          = [];

    protected $_attach_not_exists_columns = [];

    static $_attach_is_checked = false;


    function constructAttach() {
        if (false === static::$_attach_is_checked) {
            $this->traitModelAttach__CheckColumns();
            static::$_attach_is_checked = true;
        }
    }

    function traitModelAttach__checkColumn($column) {
        /** @var Model $this */
        $has = \Schema::hasColumn($this->getTable(), $column);
        if (!$has) {
            $this->traitModelAttach__addNotExistsField($column);
        }
        $this->traitModelAttach__createNotExistsFields();
    }

    function traitModelAttach__addNotExistsField($field) {
        $this->_attach_not_exists_columns[$field] = $field;
    }

    function traitModelAttach__createNotExistsFields() {
        /** @var Model $this */
        if (count($this->_attach_not_exists_columns)) {
            \Schema::table($this->getTable(),
                function (Blueprint $table) {
                    foreach ($this->_attach_not_exists_columns as $column) {
                        $method = \Str::camel('add_attach_field_' . $column);
                        if (method_exists($this, $method)) {
                            call_user_func([
                                    $this,
                                    $method
                                ],
                                $table);
                        }
                    }
                });
        }
    }

    function traitModelAttach__checkColumns() {
        $columns = $this->_attach_columns;

        foreach ($columns as $column) {
            $this->traitModelAttach__checkColumn($column);
        }
    }

    function addAttachFieldAttachableId(Blueprint $table) {

        $table->integer('attachable_id')->nullable();
    }

    function addAttachFieldAttachableType(Blueprint $table) {
        $table->string('attachable_type')->nullable();
    }

    function addAttachFieldAttachExt(Blueprint $table) {
        $table->string('attach_ext')->nullable();
    }

    function addAttachFieldAttachSize(Blueprint $table) {
        $table->string('attach_size')->nullable();
    }

    function addAttachFieldAttachMime(Blueprint $table) {
        $table->string('attach_mime')->nullable();
    }

    function addAttachFieldAttachFile(Blueprint $table) {
        $table->string('attach_file')->nullable();
    }

    function addAttachFieldAttachName(Blueprint $table) {
        $table->string('attach_name')->nullable();
    }

    function addAttachFieldAttachW(Blueprint $table) {
        $table->string('attach_w')->nullable();
    }

    function addAttachFieldAttachh(Blueprint $table) {
        $table->string('attach_h')->nullable();
    }

}