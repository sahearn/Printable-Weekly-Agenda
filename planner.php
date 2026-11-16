<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Family Week At-a-Glance</title>

    <link rel="icon" href="/favicon.ico">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Handlee">

    <style>
    #filterDiv {
        margin-bottom: 1em;
    }
    #print-wrapper * {
        visibility: visible;
    }
    #print-week-heading {
        font-family: Handlee;
        font-size: 2em;
        font-weight: bold;
        text-align: center;
    }
    #print-week-footer {
        font-family: Handlee;
    }
    .dayOfWeek {
        background-color: #bbb;
        font-family: Handlee;
        font-weight: bold;
        padding: 1em;
        text-align: center;
        text-transform: uppercase;
    }
    .dayOfMonth {
        background-color: #ddd;
        border-bottom: 2px solid #666;
        font-family: Handlee;
        font-weight: bold;
        padding: 0.8em;
        text-align: center;
        text-transform: uppercase;
    }
    .dayBlock {
        font-family: Handlee;
        font-size: 1.1em;
        padding: 4px;
    }
    .weektable {
        border: 0;
        height: 80vh;
        table-layout: fixed;
        width: 100%;
    }
    .weektable td {
        border: 1px solid black;
        padding: 0;
        vertical-align: top;
    }
    .weektable .tdSaturday {
        background: url('https://path.to/dog<?=rand(1,8);?>.png') no-repeat;
        background-position: bottom -10px center;
        background-size: 80%;
    }
    .entryEvents, .entryHolidays, .entryWork {
        border-radius: 5px;
        margin-bottom: 1em;
        padding: 3px 5px;
    }
    .entryEvents {
        background-color: #BEEFD7;
    }
    .entryHolidays {
        background-color: #BEE3EB;
    }
    .entryWork {
        background-color: #FFC0C0;
    }
    .entryTime {
        font-weight: bold;
    }
    @media print {
        body * {
            visibility: hidden;
        }
        #filterDiv {
            display:none;
        }
        .weektable {
            height: 85vh;
        }
    }
    </style>

</head>
<body>

<?php
date_default_timezone_set('America/New_York');

// get the Sunday of the current week using DateTime day-of-week math
$dtNow = new DateTime();
$dayOfWeek = (int)$dtNow->format('w'); // 0 = Sunday, 1 = Monday, etc.
if ($dayOfWeek > 0) {
    $dtNow->modify("-{$dayOfWeek} days");
}
$thisWeekSunday = $dtNow->format('Y-m-d');

// fix incoming GET parameter to Y-m-d to prevent single/double-digit issues
$passSunday = $thisWeekSunday;
if (isset($_GET['wk']) && !empty($_GET['wk'])) {
    $passSunday = date('Y-m-d', strtotime($_GET['wk']));
}

$firstday = date("m/d/Y h:i:s A T", strtotime($passSunday));
$now = new DateTime($firstday);
$nowPlusDays = clone $now;
$nowPlusDays->modify('+9 days');

// used when building the dropdown, so i can select/print other weeks if desired
function getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber) {
    $current = new DateTime($startDate);
    $end = new DateTime($endDate);
    $dateArr = array();

    // advance day-by-day until i hit the desired weekday (0 = Sunday)
    while ((int)$current->format('w') !== $weekdayNumber) {
        $current->modify('+1 day');
    }
    
    // step forward weekly using DateTime to avoid DST/timezone drift
    while ($current <= $end) {
        $dateArr[] = $current->format('Y-m-d');
        $current->modify('+7 days');
    }

    return($dateArr);
}
?>

<div id="filterDiv">
    <form>
    <table id="filterTable" style="border-bottom:1px solid gray;padding-bottom:2px;" width="100%">
    <?php
    $currdate = new DateTime("now");
    $currMin13 = clone $currdate;
    $currMin13->modify('-15 days');
    $currAdd13 = clone $currdate;
    $currAdd13->modify('+15 days');
    
    $dateArr = getDateForSpecificDayBetweenDates($currMin13->format("Y-m-d"), $currAdd13->format("Y-m-d"), 0);

    // get active comparison date normalized to Y-m-d
    $activeWeek = isset($_GET['wk']) && !empty($_GET['wk']) ? date('Y-m-d', strtotime($_GET['wk'])) : $thisWeekSunday;

    echo '<tr>';
    echo '<td>' . 'Select week: ';
    echo '&nbsp;<select name="wk" onchange="this.form.submit()">';
    foreach ($dateArr as $sunday) {
        echo '<option value="' . $sunday . '"';
        if ($sunday === $activeWeek) { 
            echo ' selected'; 
        }
        echo '>' . $sunday . '</option>';
    }
    echo '</select>' . '&nbsp; &nbsp;</td>';
    
    // toggle specific calendars
    echo '<td>' . 'Select calendars: &nbsp;';
    echo '<input onChange="this.form.submit()" name="frmcal[]" type="checkbox" value="Events"';
        if ((isset($_GET['frmcal']) and in_array('Events',$_GET['frmcal'])) || !isset($_GET['wk'])) { echo ' checked'; }
        echo '> <span style="background-color:#BEEFD7">&nbsp;Events&nbsp;</span>';
    echo '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;';
    echo '<input onChange="this.form.submit()" name="frmcal[]" type="checkbox" value="Holidays"';
        if ((isset($_GET['frmcal']) and in_array('Holidays',$_GET['frmcal'])) || !isset($_GET['wk'])) { echo ' checked'; }
        echo '> <span style="background-color:#BEE3EB">&nbsp;Holidays&nbsp;</span>';
    echo '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;';
    echo '<input onChange="this.form.submit()" name="frmcal[]" type="checkbox" value="Work"';
        if ((isset($_GET['frmcal']) and in_array('Work',$_GET['frmcal'])) || !isset($_GET['wk'])) { echo ' checked'; }
        echo '> <span style="background-color:#FFC0C0">&nbsp;Work&nbsp;</span>';
    echo '</td>';
    echo '<td align="right">' . '<a href="javascript:window.print();">Click here to print</a> (landscape orientation suggested)' . '</td>';
    echo '</tr>';
    ?>
    </table>
    </form>
</div>

<div id="print-wrapper">
    <div>
        <div id="print-week-heading"><span style="font-size:0.8em;">📅</span> &nbsp; Family Week At-a-Glance for <?=$now->format("M j");?> - <?=$nowPlusDays->modify('-3 day')->format("M j");?></div>
        <table class="weektable">
        <tr>

        <?php
        // get extracted calendar data from my outlook api read
        $calArrayAll = json_decode(file_get_contents('/path/to/calendarView-batch.json'), true);

        for ($i = 0; $i < 7; $i++) {
            $nowPlusDay = clone $now;
            $nowPlusDay->modify('+'.$i.' days');

            $tdClass = '';
            if ($i == 6) { $tdClass = 'tdSaturday'; }

            echo '<td class="' . $tdClass . '">' . "\n";
            echo '<div class="dayOfWeek">' . $nowPlusDay->format("l") . '</div>' . "\n";
            echo '<div class="dayOfMonth">' . $nowPlusDay->format("M j") . '</div>' . "\n";
            
            echo '<div class="dayBlock">' . "\n";

            foreach ($calArrayAll as $date => $events) {
                foreach ($events as $event) {
                    switch($event['calendarId']) {
                        case 'H':
                            $whichCal = 'Holidays';
                            break;
                        case 'W':
                            $whichCal = 'Work';
                            break;
                        default:
                            $whichCal = 'Events';
                            break;
                    }

                    $runningDate = new DateTime($date, new DateTimeZone('America/New_York'));
                    if ($runningDate->format('Y-m-d') == $nowPlusDay->format("Y-m-d")) {
                        if ((isset($_GET['frmcal']) && in_array($whichCal, $_GET['frmcal'])) || !isset($_GET['wk'])) {
                            echo '<div class="entry' . $whichCal . '">';
                                if (!$event['allDay']) {
                                    $displayTime = new DateTime($event['time'], new DateTimeZone('America/New_York'));
                                    $displayTime = $displayTime->format('g:i a');

                                    echo '<span class="entryTime">' . $displayTime . '</span>';
                                    echo '<br>';
                                }
                                echo $event['title'];
                            echo '</div>' . "\n";
                        }
                    }
                }
            }

            echo '</div>' . "\n";
            
            echo '</td>' . "\n";
        }
        ?>

        </tr>
        </table>
        <div id="print-week-footer">
            Coming up soon:
            <?php
            for ($i = 7; $i < 9; $i++) {
                $nowPlusDay = clone $now;
                $nowPlusDay->modify('+'.$i.' days');
                
                foreach ($calArrayAll as $date => $events) {
                    foreach ($events as $event) {
                        $runningDate = new DateTime($date, new DateTimeZone('America/New_York'));
                        if ($runningDate->format('Y-m-d') == $nowPlusDay->format("Y-m-d")) {
                            $displayDate = $runningDate->format('n/j');

                            echo '<span class="entryTime">' . $displayDate . ':</span> ' . $event['title'] . ' &nbsp;';
                        }
                    }
                }
            }
            ?>
        </div>
    </div>
</div>

</body>
</html>