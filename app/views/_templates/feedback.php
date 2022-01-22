<?php
// include loop function only once
include_once('feedbackloop.php');

// get the feedback (they are arrays, to make multiple positive/negative messages possible)
$feedback_positive = Session::get('feedback_positive');
$feedback_negative = Session::get('feedback_negative');
$feedback_warning = Session::get('feedback_warning');

echo '<div id="notifications">';

// echo out positive messages
if (isset($feedback_positive)) {
    $fdback = writeNotification($feedback_positive,'success');
    echo $fdback;
}

// echo out negative messages
if (isset($feedback_negative)) {
    $fdback = writeNotification($feedback_negative,'error');
    echo $fdback;
}

// echo out warning messages
if (isset($feedback_warning)) {
    $fdback = writeNotification($feedback_warning,'warning');
    echo $fdback;
}

echo '</div>';

