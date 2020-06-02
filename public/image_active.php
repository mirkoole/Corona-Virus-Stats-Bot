<?php

include 'helper.php';
include 'settings.php';
include 'restapi.php';

/*
 * Chart data
 */
if (!isset($_GET['country'])) {
    $country = 'germany';
} else {
    $country = $_GET['country'];
}

$result = CallAPI("GET", API_URL . "/total/dayone/country/$country");
$country = ucwords($country);

$result = json_decode($result, true);

$data = array();

$max = 100;
foreach ($result as $e) {

    $date = DateTime::createFromFormat('Y-m-d\TH:i:s+', $e['Date']);
    $date = $date->format('j.n.');

    $key = $date;
    $value = (int)$e['Active'];
    $value_d = (int)$e['Deaths'];
    $value_r = (int)$e['Recovered'];

    // improves chart readability
    if (($value + $value_d + $value_r) > 100) {
        $data[$key]['Active'] = $value;
        $data[$key]['Deaths'] = $value_d;
        $data[$key]['Recovered'] = $value_r;

        // calc max val
        $max = max($max, $value, $value_d, $value_r);

    }

}

/*
 * Chart settings and create image
 */

// Image dimensions
$imageWidth = 800;
$imageHeight = 600;

// Grid dimensions and placement within image
$gridTop = 50;
$gridLeft = 50 + 50;
$gridBottom = $imageHeight - 50;
$gridRight = $imageWidth - 50;
$gridHeight = $gridBottom - $gridTop;
$gridWidth = $gridRight - $gridLeft;

// Bar and line width
$lineWidth = 1;
$barWidth = 2;

// Font settings
#$font = '/System/Library/Fonts/Supplemental/Verdana.ttf';
$font = __DIR__ . '/fonts/Verdana.ttf';
$fontSize = 10;

// Margin between label and axis
$labelMargin = 8;

// Distance between grid lines on y-axis
$yLabelSpan = 5000;

// Max value on y-axis
$yMaxValue = $max + $yLabelSpan;

// Max value on y-axis
$yMaxValue = round($max * 1.1, -4);

// Distance between grid lines on y-axis
$yLabelSpan = round($max * 0.1, -3);


// Init image
$chart = imagecreate($imageWidth, $imageHeight);

// Setup colors
$backgroundColor = imagecolorallocate($chart, 255, 255, 255);
$axisColor = imagecolorallocate($chart, 85, 85, 85);
$labelColor = $axisColor;
$gridColor = imagecolorallocate($chart, 212, 212, 212);
$barColor_red = imagecolorallocate($chart, 217, 47, 47); // active
$barColor_black = imagecolorallocate($chart, 47, 47, 47); // death
$barColor_blue = imagecolorallocate($chart, 47, 133, 217); // recovered

imagefill($chart, 0, 0, $backgroundColor);

imagesetthickness($chart, $lineWidth);

/*
 * Print grid lines bottom up
 */

for ($i = 0; $i <= $yMaxValue; $i += $yLabelSpan) {
    $y = $gridBottom - $i * $gridHeight / $yMaxValue;

    // draw the line
    imageline($chart, $gridLeft, $y, $gridRight, $y, $gridColor);

    // draw right aligned label
    $labelBox = imagettfbbox($fontSize, 0, $font, strval($i));
    $labelWidth = $labelBox[4] - $labelBox[0];

    $labelX = $gridLeft - $labelWidth - $labelMargin;
    $labelY = $y + $fontSize / 2;

    imagettftext($chart, $fontSize, 0, $labelX, $labelY, $labelColor, $font, get_nice_number($i));
}

/*
 * Draw x- and y-axis
 */

imageline($chart, $gridLeft, $gridTop, $gridLeft, $gridBottom, $axisColor);
imageline($chart, $gridLeft, $gridBottom, $gridRight, $gridBottom, $axisColor);

/*
 * Draw the bars with labels
 */

$barSpacing = $gridWidth / count($data);
$itemX = $gridLeft + $barSpacing / 2;

$m = round(sizeof($data) / CHART_LABEL_COUNT);
$i = 0;
$l = 0;
foreach ($data as $key => $value) {

    // Draw the bar
    $x1 = $itemX - $barWidth / 2;
    $y1 = $gridBottom - $value['Active'] / $yMaxValue * $gridHeight;
    $y1_death = $gridBottom - $value['Deaths'] / $yMaxValue * $gridHeight;
    $y1_recovered = $gridBottom - $value['Recovered'] / $yMaxValue * $gridHeight;
    $x2 = $itemX + $barWidth / 2;
    $y2 = $gridBottom - 1;

    // sort by: death, active, recovered
    imagefilledrectangle($chart, $x1, $y1_recovered, $x2, $y2, $barColor_blue);
    imagefilledrectangle($chart, $x1, $y1, $x2, $y2, $barColor_red);
    imagefilledrectangle($chart, $x1, $y1_death, $x2, $y2, $barColor_black);

    // Draw the label
    $labelBox = imagettfbbox($fontSize, 0, $font, $key);
    $labelWidth = $labelBox[4] - $labelBox[0];

    $labelX = $itemX - $labelWidth / 2;
    $labelY = $gridBottom + $labelMargin + $fontSize;

    if ($i % $m == 0) {
        if ($l % 2 != 0) $labelY += 15;
        $l++;
        imagettftext($chart, $fontSize, 0, $labelX, $labelY, $labelColor, $font, $key);
    }

    $itemX += $barSpacing;

    $i++;
}

imagettftext($chart, 14, 0, 190, 30, $labelColor, $font, "COVID-19 Active Infections & Deaths $country");
imagettftext($chart, 7, 0, 250, 45, $labelColor, $font, "via Telegram Bot @CoronananaVirusBot powered by covid19api.com");
imagettftext($chart, 11, 0, 200, 70, $barColor_red, $font, "Active = RED");
imagettftext($chart, 11, 0, 350, 70, $barColor_black, $font, "Death = BLACK");
imagettftext($chart, 11, 0, 500, 70, $barColor_blue, $font, "Recovered = BLUE");

/*
 * Output image to browser
 */

header('Content-Type: image/png');
imagepng($chart);
