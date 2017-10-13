<?php
/**
 * @author edvin
 */

$userinfo = user_info($userID);

if ($userinfo == false) {
    echo "<p>" . _("User not found") . "</p>";
    return;
}

?>
<p><?php echo _("Set waking on user") . " <strong>" . $userinfo['allName'] . " (<em>" . $userinfo['nick'] . "</em>)</strong>:"; ?></p>
<form action="?module=sleepers&action=setWake&userID=<?php echo $userinfo['ID']; ?>" method="post">
    <table>
        <tbody>
        <!-- START date -->
        <tr>
            <th>&nbsp;</th>
            <th><?php echo _("Day"); ?></th>
            <th><?php echo _("Month"); ?></th>
            <th><?php echo _("Year"); ?></th>
        </tr>
        <tr>
            <td><strong><?php echo _("Date"); ?>:</strong></td>
            <td><select name="day"><option value="invalid" disabled="disabled"><?php echo _("Day"); ?></option>
                    <?php
                    for ($day=0; $day <= 31; $day++) {
                        echo "<option value=\"$day\"" . (date("j") == $day ? ' selected="selected"' : '') . ">$day</option>\n";
                    }
                    ?></select></td>
            <td><select name="month"><option value="invalid" disabled="disabled"><?php echo _("Month"); ?></option>
                    <?php
                    for ($month=0; $month <= 12; $month++) {
                        echo "<option value=\"$month\"" . (date("n") == $month ? ' selected="selected"' : '') . ">$month</option>\n";
                    }
                    ?></select></td>
            <td><select name="year"><option value="invalid" disabled="disabled"><?php echo _("Year"); ?></option>
                    <?php
                    for ($year=date("Y"); $year < (date("Y") + 10); $year++) {
                        echo "<option value=\"$year\"" . (date("Y") == $year ? ' selected="selected"' : '') . ">$year</option>\n";
                    }
                    ?></select></td>
        </tr>
        <!-- END date -->

        <!-- START time -->
        <tr>
            <th>&nbsp;</th>
            <th><?php echo _("Hours"); ?></th>
            <th><?php echo _("Minutes"); ?></th>
            <th><?php echo _("Seconds"); ?></th>
        </tr>
        <tr>
            <td><strong><?php echo _("Time"); ?>:</strong></td>
            <td><select name="hours"><option value="invalid" disabled="disabled"><?php echo _("Hours"); ?></option>
                    <?php
                    for ($hours=0; $hours <= 23; $hours++) {
                        echo "<option value=\"$hours\"" . (date("G") == $hours ? ' selected="selected"' : '') . ">$hours</option>\n";
                    }
                    ?></select></td>
            <td><select name="minutes"><option value="invalid" disabled="disabled"><?php echo _("Minutes"); ?></option>
                    <?php
                    for ($minutes=0; $minutes <= 59; $minutes++) {
                        echo "<option value=\"$minutes\"" . (date("i") == $minutes ? ' selected="selected"' : '') . ">$minutes</option>\n";
                    }
                    ?></select></td>
            <td><select name="seconds"><option value="invalid" disabled="disabled"><?php echo _("Seconds"); ?></option>
                    <?php
                    for ($seconds=0; $seconds < 60; $seconds++) {
                        echo "<option value=\"$seconds\"" . (date("s") == $seconds ? ' selected="selected"' : '') . ">$seconds</option>\n";
                    }
                    ?></select></td>
        </tr>
        <!-- END time -->
        <tr>
            <td><input type="submit" class="btn" name="submit" value="<?php echo _("Submit"); ?>"></td>
        </tr>
        </tbody>
    </table>
</form>
