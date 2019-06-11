<?php

namespace Dialect\Gdpr;

use Illuminate\Support\Facades\Crypt;

trait EncryptsAttributes
{
    /**
     * Get a plain attribute (not a relationship).
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->encrypted ?? [])) {
            $value = $this->decryptValue($value);
        }

        return $value;
    }

    /**
     * Decrypts a value only if it is not null and not empty.
     *
     * @param $value
     *
     * @return mixed
     */
    protected function decryptValue($value)
    {
        if ($value !== null && ! empty($value)) {
            return Crypt::decrypt($value);
        }

        return $value;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute(
        $key,
        $value
    ) {
        if ($value !== null && in_array($key, $this->encrypted ?? [])) {
            $value = Crypt::encrypt($value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Return Model in array type, with all datas decrypted.
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        foreach ($this->encrypted ?? [] as $key) {
            if (isset($attributes[$key])) {
                $attributes[$key] = $this->decryptValue($attributes[$key]);
            }
        }

        return $attributes;
    }

    /**
     * Return Model in array type, with all datas decrypted.
     * @return array
     */
    public function decryptToArray()
    {
        $model = parent::toArray();
        foreach ($model as $key => $value) {
            if (in_array($key, $this->encrypted) && ! is_null($value)) {
                $model[$key] = decrypt($model[$key]);
            }
        }

        return $model;
    }

    /**
     * Return Model in collection type, with all datas decrypted.
     * @return array
     */
    public function decryptToCollection()
    {
        return collect($this->attributesToArray());
    }
}
