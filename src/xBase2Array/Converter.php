<?php

namespace xBase2Array;

class Converter
{
    public $table;
    public $formatDate = "d.m.Y";
    public $array_inverted = false;

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

                // If need inverted array
                if(!$this->array_inverted)
                    $arr[$record_count][$columns[$column_count]] = $record_name;
                else
                    $arr[$columns[$column_count]][$record_count] = $record_name;

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
            if($row['nr_system'] != $key) continue; // if key not equal then skip adding to $return_rows array
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