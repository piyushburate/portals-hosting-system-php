<?php
function getJSONfile($file_name): array
{
    return json_decode(file_get_contents("./static/json/$file_name.json"), true);
}

date_default_timezone_set("Asia/Calcutta");

$portals = getJSONfile("portals");
$statusColors = getJSONfile("status-colors");

function userStatus($portal_type, $user_status): string
{
    global $portals;
    return $portals[$portal_type]["user_status"][$user_status][0];
}
function userStatusColor($portal_type, $user_status): string
{
    global $portals, $statusColors;
    return $statusColors[$portals[$portal_type]["user_status"][$user_status][1]];
}

function getAvgElapsedTime($d): string
{
    $f = [
        "y" => "year",
        "m" => "month",
        "d" => "day",
        "H" => "hour",
        "i" => "minute",
        "s" => "second",
    ];
    $d1 = new DateTime();
    $d2 = new DateTime($d);
    $interval = $d1->diff($d2);
    $t = 's';
    if ($interval->format('%y') > 0):
        $t = "y";
    elseif ($interval->format('%m') > 0):
        $t = "m";
    elseif ($interval->format('%d') > 0):
        $t = "d";
    elseif ($interval->format('%H') > 0):
        $t = "H";
    elseif ($interval->format('%i') > 0):
        $t = "i";
    endif;

    $str = (int)$interval->format("%$t") . " " . $f[$t];
    if($t == 's' and $interval->format("%$t") < 20):
        $str = "Now";
    elseif ($interval->format("%$t") > 1):
        $str .= "s ago";;
    else:
        $str .= " ago";
    endif;
    return $str;

}
?>