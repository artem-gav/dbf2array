<?php

namespace xBase2Array;

use XBase\Table;

class Converter
{
    public static function xBase2ArrayStandard($table) {
        $arr = [];
        $record_count = 0; $column_count = 0;

        while ($record = $table->nextRecord()) {
            foreach($table->columns as $column) {
                $column_name = $column->name;
                $arr[$column_count][$record_count] = $record->$column_name;

                $column_count++;
            }

            $record_count++; $column_count=0;
        }

        return $arr;
    }

    public static function xBase2ArrayInverted($table) {
        $arr = [];
        $record_count = 0;

        while ($record = $table->nextRecord()) {
            foreach($table->columns as $column) {
                $column_name = $column->name;
                $arr[$record_count][] = $record->$column_name;
            }

            $record_count++;
        }

        return $arr;
    }
}