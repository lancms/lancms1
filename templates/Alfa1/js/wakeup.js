/**
 * Created by edvin on 04.01.15.
 */

var wakeupHandler = new WakeupHandler();

function WakeupHandler() {
    this.sleepers = [];
    this.lang = [];
    this.wakeupMessage = "%USER% skal vekkes nå.";

    /**
     * Adds a new sleeper and starts countdown on it.
     *
     * @param sleeperObj Sleeper
     * @returns {boolean}
     */
    this.addSleeper = function (sleeperObj) {
        if (typeof sleeperObj != 'object') {
            window&&console.log("sleeperObj was not Sleeper.");
            return false;
        }

        window&&console.log("Adding sleeper " + sleeperObj.userID);

        // Timeout?
        var current     = this._currentTimestamp() / 1000;
        var target      = sleeperObj.wakeupTime;

        if (current > target) {
            this.setWakeupMessage(sleeperObj, false);
            return true;
        }

        this.sleepers[sleeperObj.userID] = sleeperObj;
        this.startCountdown(sleeperObj.userID);
        return true;
    };

    /**
     * Set sleeper array.
     * @param arr
     */
    this.setSleepers = function (arr) {
        for	(var index = 0; index < arr.length; index++) {
            wakeupHandler.addSleeper(arr[index]);
        }
    };

    /**
     * Add lang string.
     *
     * @param id
     * @param str
     */
    this.addLangString = function (id,str) {
        this.lang[id] = str;
    };

    /**
     * Remove sleeper from wakeup countdown.
     * @param userID
     */
    this.removeSleeper = function (userID) {
        window&&console.log("Removing sleeper " + userID);
        this.sleepers[userID] = undefined;
    };

    /**
     * Starts a clock countdown for sleeper.
     */
    this.startCountdown = function (userID) {
        setTimeout(function(){
            wakeupHandler._updateCountdown(userID);
        }, 1000);
    };

    this._updateCountdown = function (userID) {
        if (this.sleepers[userID] == undefined) {
            window&&console.log("Updating countdown failed, user not in sleepers.");
            return;
        }

        var l           = this.lang;
        var current     = this._currentTimestamp() / 1000;
        var targetObj   = this.sleepers[userID];
        var targetStamp = targetObj.wakeupTime;

        if (current > targetStamp) {
            window&&console.log("Finish, alert browser.");
            this.setWakeupMessage(targetObj, true);
            return;
        }

        // Calculate secs, mins, hours and days.
        var diff = parseInt(targetStamp - current);
        var days    = parseInt(diff / 86400 % 7);
        var hours   = parseInt(diff / 3600 % 24);
        var minutes = parseInt(diff / 60 % 60);
        var seconds = parseInt(diff % 60);

        // Build string.
        var timers = [];

        if (days > 0) {
            timers.push(days + " " + (days > 1 ? l[4].toLowerCase() : l[0].toLowerCase()));
        }

        if (hours > 0) {
            timers.push(hours + " " + (hours > 1 ? l[5].toLowerCase() : l[1].toLowerCase()));
        }

        if (minutes > 0) {
            timers.push(minutes + " " + (minutes > 1 ? l[6].toLowerCase() : l[2].toLowerCase()));
        }

        timers.push(seconds + " " + (seconds > 1 ? l[7].toLowerCase() : l[3].toLowerCase())); // Always display seconds.

        // Set string on wakeup in col.
        var elm = document.getElementById('wakeup-' + userID);
        if (elm !== undefined) {
            elm.innerHTML = timers.join(", ") + ", <a title='Remove wakeup' href='?module=sleepers&amp;action=rmWake&amp;userID=" + targetObj.userID + "'>" + l[11].toLowerCase() + "</a>";

            // Set new timeout for this countdown.
            this.startCountdown(userID);
        } else {
            window&&console.log("Element to update not found.");
        }
    };

    /**
     * Notify website of a person who needs waking.
     * @param sleeperObj
     * @returns {boolean}
     */
    this.setWakeupMessage = function (sleeperObj, sendAlert) {
        if (typeof sleeperObj != 'object') {
            window&&console.log("sleeperObj was not Sleeper.");
            return false;
        }

        var message = this._formatMessage(this.lang[8], sleeperObj);

        this.removeSleeper(sleeperObj.userID);

        // Alert browser.
        if (sendAlert == true)
            alert(this.lang[9] + "\n\n" + message);

        var td = document.getElementById('wakeup-' + sleeperObj.userID);
        td.style.backgroundColor = "#D20F0F";
        td.innerHTML = '<a style="color:#ffffff;" title="Remove notification" href="?module=sleepers&amp;action=rmWake&amp;userID=' + sleeperObj.userID + '"><strong>' + this.lang[10] + '</strong></a>';
        return true;
    };

    /**
     * Formats user message for waking.
     *
     * %USERNAME% = Username, e.g. pushit
     * %NAME%     = Name of user, e.g. Edvin Hultberg
     *
     * @param str
     * @param obj
     * @returns {string}
     * @private
     */
    this._formatMessage = function (str, obj) {
        str = str.replace("%NAME%", obj.fname);
        str = str.replace("%USERNAME%", obj.username);
        return str;
    };

    /**
     * Provides the current unix timestamp (sec).
     * @returns {number}
     * @private
     */
    this._currentTimestamp = function () {
        var time = 0;

        if (!Date.now) {
            time = new Date().getTime();
        } else {
            time = Date.now();
        }

        return time;
    }
}

/**
 * Simple holder of a sleeper who needs waking.
 *
 * @param userID int
 * @param name string
 * @param nick string
 * @param wakeupTime int
 * @constructor
 */
function Sleeper(userID, name, nick, wakeupTime) {
    this.userID = userID;
    this.username = nick;
    this.fname = name;
    this.wakeupTime = wakeupTime;
}