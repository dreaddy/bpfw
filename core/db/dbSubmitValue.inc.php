<?php
/*
 *
 * Copyright (c) 2017-2023. Torsten Lüders
 *
 * Part of the BPFW project. For documentation and support visit https://bpfw.org .
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 *
 */



/**
 * GroupchatGroup short summary.
 *
 * GroupchatGroup description.
 *
 * @version 1.0
 * @author torst
 */

class DbSubmitValue
{

    /**
     * Summary of $key
     * @var string
     */
    var string $key;
    /**
     * The submitted Value
     * @var mixed
     */
    var mixed $data;
    /**
     * Summary of $model
     * @var DbModel
     */
    private DbModel $model;

    /**
     * Summary of __construct
     * @param string $key
     * @param mixed $data
     * @param DbModel $model
     * @throws Exception
     */
    function __construct(string $key, mixed $data, DbModel $model)
    {

        if (is_object($data) && get_class($data) == "DbSubmitValue") {
            if ($key == $data->key && $data->model === $model) {
                $data = $data->data;
            }
        }

        if (is_object($data)/* || is_array($data)*/) {
            throw new Exception("$key data should be array or string but is " . get_class($data) . " " . print_r($data, true));
        }

        $this->key = $key;
        $this->data = $data;
        $this->model = $model;

    }

    public function __debugInfo()
    {
        return [
            'key' => $this->key,
            'data' => $this->data,
        ];
    }

    /**
     * Summary of getDbField
     * @return BpfwModelFormfield
     * @throws Exception
     */
    function getDbField(): BpfwModelFormfield
    {
        return $this->model->getDbModel()[$this->key];
    }

    /**
     * Summary of getDbField
     * @return BpfwModelFormfield
     * @throws Exception
     */
    function field(): BpfwModelFormfield
    {
        return $this->model->getDbModel()[$this->key];
    }

}
