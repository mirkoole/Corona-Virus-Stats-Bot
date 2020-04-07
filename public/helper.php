<?php

function get_nice_number($number)
{
    return number_format($number, " ", ".");
}

function parse_date($date)
{

    $date = DateTime::createFromFormat('Y-m-d\TH:i:s+', $date);
    $date_utc = $date->format('H:i - d.m.Y') . ' (UTC)';

    $date_unix = mktime($date->format("H"), $date->format("i"), 0, $date->format("m"), $date->format("d"), $date->format("Y"));
    $now = time();

    $date_unix += 60 * 120; // add 2h for timezone difference
    $diff = $now - $date_unix;
    $minago = (int)($diff / 60);

    $result = 'Last Update: <i>' . $minago . ' min ago</i>
<i>' . $date_utc . '</i>';

    return $result;

}


function searchForId($id, $array)
{
    foreach ($array as $key => $val) {
        if ($val['CountryCode'] === $id) {
            return $key;
        }
    }
    return null;
}
