<?php
// write alert loop
function writeNotification($item,$type) {
    $current = "";
    foreach ($item as $feedback) {
	    $fdback = '<div class="' . $type . '">'.$feedback.'<span class="note-close">x</span></div>';
        $current = $feedback;
        return $fdback;
    }
}
?>