<?php

namespace xBase2Array;

class Converter
{
    public $table;
    public $associative_array = true;
    public $array_inverted = false;
    public $formatDate = "d.m.Y";
    public $link_index = "nr_system";
//    public $field_sorting = "data";
//    public $sorting = SORT_ASC;

    private $columns;
    private $rows;
    private $rowsKey;

    function __construct($table) {
        $this->table = $table;
    }

    // Get rows from xBase
    public function generate() {
        $arr = [];
        $record_count = 0; $column_count = 0;

        $columns = $this->getTitleColumns(); // array with keys for data

        while ($record = $this->table->nextRecord()) {
            foreach($this->table->columns as $column) {
                $column_name = $column->name;
                $record_name = $record->$column_name;

                // Converting format date
                if($column->type == 'D') {
                    $record_name = self::date($this->formatDate, $record->$column_name);
                }

                // Check if array must be associative
                $_column_count = $this->associative_array ? $columns[$column_count] : $column_count;

                // If need inverted array
                if(!$this->array_inverted)
                    $arr[$record_count][$_column_count] = $record_name;
                else
                    $arr[$_column_count][$record_count] = $record_name;

                $column_count++;
            }

            $record_count++;

            // If need inverted array
            $column_count=0;
        }

        $this->rows = $arr;
        return $arr;
    }

    // Return all data with key = $key
    public function getRowsWithKey($key) {
        $rows = $this->generate();
        $return_rows = [];

        foreach($rows as $row) {
            if($row[$this->link_index] != $key) continue; // if key not equal then skip adding to $return_rows array
            $return_rows[] = $row;
        }

        $this->rowsKey = $return_rows;
        return $return_rows;
    }

    // Get titles from xBase
    public function getTitleColumns() {
        $arr = [];
        foreach($this->table->columns as $column) {
            $arr[] = $column->name;
        }

        $this->columns = $arr;
        return $arr;
    }

    public function sorting($fields, $rows) {
        // Fixed variable if $fields is not array
        if(!is_array($fields)) { $fields = [$fields]; }

        foreach($fields as $key_field => $field) {
            foreach($rows as $key_row => $row) {
                $mod_fields[$key_field][$key_row] = $row[$field];
            }
        }

        foreach($mod_fields as $mod_field) {
            array_multisort($mod_field, SORT_NUMERIC, SORT_ASC , $rows);
        }

        return $rows;
    }

    // Select columns from array and return (For simple array)
    public static function selectColumns($keys, $rows) {
        $return_rows = [];

        foreach($rows as $row) {
            $return_rows[] =array_intersect_key($row,array_flip($keys));
        }

        return $return_rows;
    }

    // Remove columns from array and return (For simple array)
    public static function removeColumns($keys, $rows) {
        $return_rows = [];

        foreach($rows as $row) {
            $return_rows[] =array_diff_key($row,array_flip($keys));
        }

        return $return_rows;
    }

    public static function date($format, $date) {
        $source_time = strtotime($date);
        return date($format, $source_time);
    }
}