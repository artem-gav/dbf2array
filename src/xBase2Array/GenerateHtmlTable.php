<?php

namespace xBase2Array;

class GenerateHtmlTable
{
    public $index;
    public $link_index;
    public $hideField = [];

    public function generate($titles = null, $rows, $is_key_link = false) {
        $str = ""; // string for writing html table
        $_count=0; // counter for first column (indexes)

        $str .= "<div class='container'>";
        $str .= "<table class='table table-bordered'>";

        // If sent $titles array
        if(isset($titles)) {
            // Header
            $str .=  "<tr class='info'>";
            foreach($titles as $title) {

                // Hide all field that have in $hideFields array
                if(in_array($title, $this->hideField)) { continue; }

                $str .=  "<th>$title</th>";
            }
            $str .=  "</tr>";
        }

        // Body
        foreach($rows as $columns) {
            $str .=  "<tr>";

            foreach($columns as $key => $row) {

                // Hide all field that have in $hideFields array
                if(in_array($key, $this->hideField)) { continue; }

                // Install link to index
                if($is_key_link AND $_count == 0) {
                    $str .=  "<td><a href='details?key=".$columns[$this->index]."'>".$columns[$this->link_index]."</a></td>";
                    $_count++;
                    continue;
                }

                $str .=  "<td>$row</td>";
            }

            $_count=0;
            $str .=  "</tr>";
        }

        $str .=  "</table>";
        $str .= "</div>";

        return $str;
    }

}