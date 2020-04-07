<?php

function createReadableNumber($number)
{
    return number_format($number, " ", ".");
}

function parseDate($date)
{

    $date = DateTime::createFromFormat('Y-m-d\TH:i:s+', $date);
    $date_utc = $date->format('H:i - d.m.Y') . ' (UTC)';

    $date_unix = mktime($date->format("H"), $date->format("i"), 0, $date->format("m"), $date->format("d"), $date->format("Y"));

    $date_unix += 60 * 120; // add 2h for timezone difference
    $diff = time() - $date_unix;
    $min_ago = (int)($diff / 60);

    return 'Last Update: <i>' . $min_ago . ' min ago</i>' . PHP_EOL . '<i>' . $date_utc . '</i>';


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
