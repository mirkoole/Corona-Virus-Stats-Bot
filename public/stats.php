<?php

use peterkahl\flagMaster\flagMaster;

define("API_URL", "https://api.covid19api.com");

function get_country_history($country = 'germany', $countrycode = 'DE', $history = 30)
{

    $result = CallAPI("GET", API_URL . "/total/dayone/country/$country/status/confirmed");
    $country = ucwords($country);

    $result = json_decode($result, true);
    var_dump($result);

    if ($result == NULL) {
        return 'Sorry, data is currently not available, but will return tomorrow (hopefully). 😐 ';
    }

    $message = 'Last 30 Days of ' . $country . ' ' . flagMaster::emojiFlag($countrycode) . ' ' . PHP_EOL . PHP_EOL . '<b>Date - Infections Total</b>' . PHP_EOL . '<pre>';

    for ($i = sizeof($result) - $history; $i < sizeof($result); $i++) {

        $date = DateTime::createFromFormat('Y-m-d\TH:i:s+', $result[$i]['Date']);
        $date = $date->format('d.m.Y');

        $message .= PHP_EOL . $date . ' - ' . get_nice_number_textaligned($result[$i]['Cases']);

    }

    $message .= '</pre>';

    return $message;
}

function get_country_history_table($country = 'germany', $history = 30)
{
    $result = CallAPI("GET", API_URL . "/total/dayone/country/$country/status/confirmed");

    $result = json_decode($result, true);

    if ($result == NULL) {
        return 'Sorry, data is currently not available, but will return tomorrow (hopefully). 😐 ';
    }

    $message = 'Last 30 Days of Germany 🇩🇪 (Table Beta)' . PHP_EOL . PHP_EOL . '<b>Date - Infections Total</b>' . PHP_EOL . '';

    for ($i = sizeof($result) - $history; $i < sizeof($result); $i++) {

        $date = DateTime::createFromFormat('Y-m-d\TH:i:s+', $result[$i]['Date']);
        $date = $date->format('d.m.');

        $total = $result[$i]['Cases'] / 3000;
        $line = str_repeat("|", $total);

        $message .= PHP_EOL . $date . ' ' . $line;

    }

    return $message;
}

function get_country_status($countryCode = 'DE')
{
    $result = CallAPI("GET", API_URL . "/summary");

    $result = json_decode($result, true);

    if ($result == NULL) {
        return 'Sorry, data is currently not available, but will return tomorrow (hopefully). 😐 ';
    }

    $countryID = search_for_id($countryCode, $result['Countries']);

    if ($countryID == NULL) {
        return 'Sorry, data is currently not available, but will return tomorrow (hopefully). 😐 ';
    }

    $result = $result['Countries'][$countryID];

    $date = parse_date($result['Date']);

    $result = $result['Country'] . ' Corona Infections ' . flagMaster::emojiFlag($result['CountryCode']) . '

<b>Today</b> 🗓 <pre>
New Infections: <b>' . get_nice_number_textaligned($result['NewConfirmed']) . '</b>
New Deaths:     <b>' . get_nice_number_textaligned($result['NewDeaths']) . '</b>
New Recovered:  <b>' . get_nice_number_textaligned($result['NewRecovered']) . '</b>
</pre>

<b>Total</b> 📈 <pre>
Infections: <b>' . get_nice_number_textaligned($result['TotalConfirmed']) . '</b>
Deaths:     <b>' . get_nice_number_textaligned($result['TotalDeaths']) . '</b>
Recovered:  <b>' . get_nice_number_textaligned($result['TotalRecovered']) . '</b>
</pre>
' . $date;

    return $result;

}

function get_world_status()
{
    $result = CallAPI("GET", API_URL . "/summary");

    $result = json_decode($result, true);

    if ($result == NULL) {
        return 'Sorry, data is currently not available, but will return tomorrow (hopefully). 😐 ';
    }

    $date = parse_date($result['Date']);

    $result = 'Worldwide Corona Infections 🌎🌍🌏

<b>Today</b> 🗓 <pre>
New Infections: <b>' . get_nice_number_textaligned($result['Global']['NewConfirmed']) . '</b>
New Deaths:     <b>' . get_nice_number_textaligned($result['Global']['NewDeaths']) . '</b>
New Recovered:  <b>' . get_nice_number_textaligned($result['Global']['NewRecovered']) . '</b></pre>

<b>Total</b> 📈 <pre>
Infections: <b>' . get_nice_number_textaligned($result['Global']['TotalConfirmed']) . '</b>
Deaths:     <b>' . get_nice_number_textaligned($result['Global']['TotalDeaths']) . '</b>
Recovered:  <b>' . get_nice_number_textaligned($result['Global']['TotalRecovered']) . '</b>
</pre>
' . $date;


    return $result;

}
