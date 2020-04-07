<?php

include 'helper.php';
include 'settings.php';
include 'restapi.php';

/*
 * Chart data
 */

$result = CallAPI("GET", "https://api.covid19api.com/total/dayone/country/germany/status/confirmed");

$result = json_decode($result, true);

$data = array();

$i = 0;
$max = 100;
foreach ($result as $e) {

    $date = DateTime::createFromFormat('Y-m-d\TH:i:s+', $e['Date']);
    $date = $date->format('j.n.');

    $key = $date;
    $value = (int)$e['Cases'];

    $data[$key] = $value;

    $max = max($max, $value);

    $i++;
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
$barWidth = 3;

// Font settings
#$font = '/System/Library/Fonts/Supplemental/Verdana.ttf';
$font = __DIR__ . '/fonts/Verdana.ttf';
$fontSize = 10;

// Margin between label and axis
$labelMargin = 8;

// Max value on y-axis
$yMaxValue = $max;

// Distance between grid lines on y-axis
$yLabelSpan = 10000;

// Init image
$chart = imagecreate($imageWidth, $imageHeight);

// Setup colors
$backgroundColor = imagecolorallocate($chart, 255, 255, 255);
$axisColor = imagecolorallocate($chart, 85, 85, 85);
$labelColor = $axisColor;
$gridColor = imagecolorallocate($chart, 212, 212, 212);
$barColor = imagecolorallocate($chart, 47, 133, 217);

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

    imagettftext($chart, $fontSize, 0, $labelX, $labelY, $labelColor, $font, get_nice_number_chart($i));
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
foreach ($data as $key => $value) {
    // Draw the bar
    $x1 = $itemX - $barWidth / 2;
    $y1 = $gridBottom - $value / $yMaxValue * $gridHeight;
    $x2 = $itemX + $barWidth / 2;
    $y2 = $gridBottom - 1;

    imagefilledrectangle($chart, $x1, $y1, $x2, $y2, $barColor);

    // Draw the label
    $labelBox = imagettfbbox($fontSize, 0, $font, $key);
    $labelWidth = $labelBox[4] - $labelBox[0];

    $labelX = $itemX - $labelWidth / 2;
    $labelY = $gridBottom + $labelMargin + $fontSize;

    if ($i % $m == 0) {
        imagettftext($chart, $fontSize, 0, $labelX, $labelY, $labelColor, $font, $key);
    }

    $itemX += $barSpacing;

    $i++;
}

/*
 * Output image to browser
 */

header('Content-Type: image/png');
imagepng($chart);
