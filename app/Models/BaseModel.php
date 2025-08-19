<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class BaseModel extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'text'];

    public function setAttribute($key, $value)
    {
        if (is_null($value) && array_key_exists($key, $this->getCasts())) {
            $value = $this->nullCastAttribute($key);
        }
        return parent::setAttribute($key, $value);
    }

    protected function nullCastAttribute($key)
    {
        switch ($this->getCastType($key)) {
            case 'int':
            case 'integer':
                return (int)0;
            case 'real':
            case 'float':
            case 'double':
            case 'decimal':
                return (float)0.0;
            case 'string':
                return '';
            case 'bool':
            case 'boolean':
                return false;
            case 'object':
                return new \stdClass();
            case 'array':
            case 'json':
                return [];
            case 'collection':
                return new Collection();
            case 'date':
                return $this->asDate('0000-00-00');
            case 'datetime':
                return $this->asDateTime('0000-00-00');
            case 'timestamp':
                return $this->asTimestamp('0000-00-00');
            default:
                return null;
        }
    }

    public function getClassName(): string
    {
        return static::class;
    }

    /**
     * @return string
     */
    public static function getTableStatic(): string
    {
        return (new static())->getTable();
    }

    /**
     * @return string
     */
    public static function getKeyNameStatic(): string
    {
        return (new static())->getKeyName();
    }

    /**
     * @return string
     */
    public static function getForeignKeyStatic(): string
    {
        return (new static())->getForeignKey();
    }

    public static function getHasSoftDeleteStatic(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive(static::class));
    }

    public static function getSoftDeleteColumn()
    {
        if (self::getHasSoftDeleteStatic()) {
            return (new static())->getDeletedAtColumn();
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}");
    }
}
