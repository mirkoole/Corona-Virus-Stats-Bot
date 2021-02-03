<?php

function get_nice_number_textaligned($number)
{
    return sprintf("%11s", get_nice_number($number));
}

function get_nice_number($number)
{
    return number_format($number, 0, ',', '.');
}

function parse_date($date)
{

    $date = DateTime::createFromFormat('Y-m-d\TH:i:s+', $date);
    $date_utc = $date->format('H:i - d.m.Y') . ' (UTC)';

    // calc timezone difference
    $date_unix = mktime($date->format("H"), $date->format("i"), 0, $date->format("m"), $date->format("d"), $date->format("Y"));
    $now = new DateTime('now', new DateTimeZone('Europe/Berlin'));
    $date_unix += $now->getOffset();
    $diff = time() - $date_unix;

    $min_ago = (int)($diff / 60);

    return 'Last Update: <i>' . $min_ago . ' min ago</i>' . PHP_EOL . '<i>' . $date_utc . '</i>';
}


function search_for_id($id, $array)
{
    foreach ($array as $key => $val) {

        if ($val['CountryCode'] === $id) {
            return $key;
        }

    }

    return null;
}
