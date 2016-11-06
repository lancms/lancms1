<?php

if(!config("users_may_register")) {
    $content .= "<p>users may not register yet</p>";
    return; // Terminate this file.
}

if (!($sessioninfo->userID <= 1 or acl_access ("userAdmin", "", $sessioninfo->eventID) != 'No')) {
    header ('Location: index.php');
    die ();
}

$userManager = UserManager::getInstance();
$errors = $userManager->getFormInSession("register_errors");
$formData = $userManager->getFormInSession("register");


switch ($action) {

    //==================================================================================
    // Handle verifyMail
    case "verifymail":

        $userCode = (array_key_exists('verifycode', $_GET) ? $_GET['verifycode'] : null);

        if ($userCode == null) {
            $content .= "<p>Invalid verification code.</p>";
        } else {
            $user = $userManager->getUserByVerificationCode($userCode);
            if ($user == null || $user->isEmailConfirmed() == true) {
                $content .= "<p>Invalid verification code.</p>";
            } else {
                $user->setEmailConfirmed(true);
                $user->setEmailVerifyCode("");
                $user->commitChanges();

                $content .= "<h1 class=\"page-title\">" . _("User account verified") . "</h1>
                <p>" . _("User account has been verified. You can now login.") . "</p>";
            }
        }

        break;

    //==================================================================================
    // Print a success page
    case "success":

        $content .= "<h1 class=\"page-title\">" . _("User created") . "</h1>
        <p>" . _("Your new user account has been created. Now you need to verify your email address. Please check your email index or spam folder to find the verification URL.") . "</p>";

        break;

    //==================================================================================
    // Handle register
    case "doRegister":
        // Validate all fields.

        $requiredFields = array("username", "firstName", "lastName", "password", "repassword", "email");
        if(config("userinfo_gender_required"))
            $requiredFields[] = "gender";

        if(config("userinfo_birthday_required")) {
            $requiredFields[] = "day";
            $requiredFields[] = "month";
        }

        if (config("userinfo_birthyear_required"))
            $requiredFields[] = "year";

        if (config("userinfo_cellphone_required"))
            $requiredFields[] = "cellphone";

        $errors = array();
        $saveFormData = array();
        foreach ($requiredFields as $key => $value) {
            if (isset($_POST[$value]) == false || strlen(trim($_POST[$value])) < 1) {
                $errors[] = $value;
            } else {
                $saveFormData[$value] = $_POST[$value];
            }
        }

        // Do not save password and repassword in form data.
        unset($saveFormData['password']);
        unset($saveFormData['repassword']);

        //===========================================================
        $username   = $_POST["username"];
        $password   = $_POST["password"];
        $repassword = $_POST["repassword"];
        $email      = $_POST["email"];
        $firstName  = '';
        $lastName   = '';
        $address    = null;
        $postCode   = null;
        $gender     = null;
        $birthDay   = null;
        $birthMonth = null;
        $birthYear  = null;
        $cellPhone  = null;

        if(config("register_firstname_required")) {
            $firstName  = $_POST["firstName"];        
        }

        if(config("register_lastname_required")) {
            $lastName   = $_POST["lastName"];        
        }

        if(config("userinfo_gender_required")) {
            $gender = $_POST["gender"];        
        }

        if(config("userinfo_birthday_required")) {
            $birthDay = $_POST["day"];
            $birthMonth = $_POST["month"];
        }

        if (config("userinfo_birthyear_required")) {
            $birthYear = $_POST["year"];        
        }

        if (config("userinfo_cellphone_required")) {
            $cellPhone = $_POST["cellphone"];
        }

        if (config("userinfo_address_required")) {
            $address = $_POST["address"];
            $postCode = $_POST["postCode"];
        }
        
        //===========================================================
        // Username exists?
        if ($userManager->getUserByNick($username) instanceof User) {
            $errors[] = 'Username is taken.';
            $userManager->saveFormInSession("register_errors", $errors);

            header("Location: index.php?module=register&error=6");
            die();
        }

        //===========================================================
        // Valid email address?
        if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
            $errors[] = 'Email is not valid.';
            unset($saveFormData['email']);
        }

        //===========================================================
        // Email exists?
        if ($userManager->userExistsByEmail($email)) {
            $errors[] = 'Email already in use.';
            $userManager->saveFormInSession("register_errors", $errors);

            header("Location: index.php?module=register&error=7");
            die();
        }

        //===========================================================
        // Retyped correct password?
        if ($password != $repassword) {
            $errors[] = 'Wrong re-typed password';
        }
        //===========================================================
        // Validate gender if set.
        if ($gender !== null && $gender === -1) {
            $errors[] = 'Gender is required';
            unset($saveFormData['gender']);
        }
        //===========================================================
        // Validate birthday if set.
        if ($birthDay !== null && $birthDay < 1) {
            $errors[] = 'Day is required';
            unset($saveFormData['day']);
        }

        if ($birthMonth !== null && $birthMonth < 1) {
            $errors[] = 'Month is required';
            unset($saveFormData['month']);
        }

        if ($birthYear !== null && $birthYear < 1900) {
            $errors[] = 'Year is required';
            unset($saveFormData['year']);
        }
        //===========================================================
        // Validate cell phone if configured.
        if ($cellPhone !== null && is_string($cellPhone) == false) {
            $errors[] = 'Cellphone is required and must be numeric';
            unset($saveFormData['cellphone']);
        }
        //===========================================================
        // Save both errors and form data in session.
        if (count($saveFormData) > 0)
            $userManager->saveFormInSession("register", $saveFormData);

        if (count($errors) > 0) {
            $userManager->saveFormInSession("register_errors", $errors);
            header("Location: index.php?module=register&error=true");
            die();
        }
        //===========================================================

        $createdUser = $userManager->createUser($username, $firstName, $lastName, $email, $password);

        if ($createdUser === null) {
            header("Location: index.php?module=register&error=5");
            die();
        }

        for($i=0;$i<count($userpersonalprefs);$i++) {
            if($userpersonalprefs[$i]['default_register'] == 1) {
                $prefname = $userpersonalprefs[$i]['name'];
                switch ($userpersonalprefs[$i]['type']) {

                    case "checkbox":
                        db_query("INSERT INTO ".$sql_prefix."_userPreferences
                            SET userID = '" . $createdUser->getUserID() . "',
                            name = '$prefname',
                            value = 'on'");
//                      echo "FOO?";
//                      die();
                        break;
                } // End switch
            } // End if userpersonalprefs_default_register == 1
        } // end for

        $userManager->sendEmailVerification($createdUser);
        $userManager->resetFormInSession("register");
        $userManager->resetFormInSession("register_errors");

        //===========================================================
        if ($gender !== null) {
            $createdUser->setGender($gender);
        }

        if ($birthDay !== null) {
            $createdUser->setBirthDay($birthDay);
        }

        if ($birthMonth !== null) {
            $createdUser->setBirthMonth($birthMonth);
        }

        if ($birthYear !== null) {
            $createdUser->setBirthYear($birthYear);
        }

        if ($cellPhone !== null) {
            $createdUser->setCellPhone($cellPhone);
        }

        if ($address !== null) {
            $createdUser->setStreetAddress($address);
        }

        if ($postCode !== null) {
            $createdUser->setPostNumber($postCode);
        }

        $createdUser->commitChanges();
        //===========================================================

        header("Location: index.php?module=register&action=success&userID=" . $createdUser->getUserID());
        die();

        break;

    //==================================================================================
    // Display register form
    default:
        $content .= "<h1 class=\"page-title\">" . _("Register user") . "</h1>";

        // Has errors? Print an alert.
        if (isset($_GET['error']) && intval($_GET['error']) == 6) {
            $content .= "<div class=\"alert alert-danger\">" . _("Username is taken, try another one.") . "</div>";
        } else if (isset($_GET['error']) && intval($_GET['error']) == 7) {
            $content .= "<div class=\"alert alert-danger\">" . _("Email is taken, try another one.") . "</div>";
        } else if (count($errors) > 0 && isset($_GET['error'])) {
            $content .= "<div class=\"alert alert-danger\"><strong>" . _("Form did not validate, check for errors below") . "</strong><br /><ul>";
            foreach ($errors as $error) {
                $content .= "<li>" . $error . "</li>";
            }
            $content .= "</ul></div>";
        }

        $content .= "<div class=\"register-form\">
            <form class=\"normal form\" action=\"index.php?module=register&amp;action=doRegister\" method=\"post\">
                <div class=\"form-group\">
                    <label for=\"username\">" . _("Username") . "</label>

                    <div class=\"elements\">
                        <input type=\"text\" id=\"username\" class=\"input " . checkForErrorsInput("username") . "\" name=\"username\" value=\"" . getInputValue("username") . "\" required />
                    </div>
                </div>";

                if(config("register_firstname_required")) {
                    $content .= "<div class=\"form-group\">
                        <label for=\"firstName\">" . _("Firstname") . "</label>

                        <div class=\"elements\">
                            <input type=\"text\" id=\"firstName\" class=\"input " . checkForErrorsInput("firstName") . "\" name=\"firstName\" value=\"" . getInputValue("firstName") . "\" required />
                        </div>
                    </div>";
                }

                if(config("register_lastname_required")) {
                    $content .= "<div class=\"form-group\">
                        <label for=\"lastName\">" . _("Lastname") . "</label>

                        <div class=\"elements\">
                            <input type=\"text\" id=\"lastName\" class=\"input " . checkForErrorsInput("lastName") . "\" name=\"lastName\" value=\"" . getInputValue("lastName") . "\" required />
                        </div>
                    </div>";
                }

                $content .= "<div class=\"form-group\">
                    <label for=\"password\">" . _("Password") . "</label>

                    <div class=\"elements\">
                        <input type=\"password\" id=\"password\" class=\"input" . checkForErrorsInput("password") . "\" name=\"password\" required />
                    </div>
                </div>
                <div class=\"form-group\">
                    <label for=\"repassword\">" . _("Repeat password") . "</label>

                    <div class=\"elements\">
                        <input type=\"password\" id=\"repassword\" class=\"input" . checkForErrorsInput("repassword") . "\" name=\"repassword\" required />
                    </div>
                </div>
                <div class=\"form-group\">
                    <label for=\"email\">" . _("E-mail address") . "</label>

                    <div class=\"elements\">
                        <input type=\"text\" id=\"email\" class=\"input\" name=\"email" . checkForErrorsInput("email") . "\" value=\"" . getInputValue("email") . "\" required />
                    </div>
                </div>";

                if (config('userinfo_address_required')) {
                    $content .= "<div class=\"form-group\">
                        <label for=\"address\">" . _("Address") . "</label>

                        <div class=\"elements\">
                            <input type=\"text\" id=\"address\" class=\"input " . checkForErrorsInput("address") . "\" name=\"address\" value=\"" . getInputValue("address") . "\" required />
                        </div>
                    </div><div class=\"form-group\">
                        <label for=\"postCode\">" . _("Postcode") . "</label>

                        <div class=\"elements\">
                            <input type=\"text\" id=\"postCode\" class=\"input " . checkForErrorsInput("postCode") . "\" width=\"width:50px;\" name=\"postCode\" value=\"" . getInputValue("postCode") . "\" required />
                        </div>
                    </div>";
                }

                if(config("userinfo_gender_required")) {
                    $content .= "<div class=\"form-group\">
                        <label for=\"gender\">" . _("Gender") . "</label>

                        <div class=\"elements\">
                            <select name=\"gender\" class=\"input " . checkForErrorsInput("gender") . "\" id=\"gender\">
                                <option value=\"-1\">" . _("Gender") . "</option>
                                <option" . (getInputValue("gender") == 'Male' ? ' selected' : '') . " value=\"Male\">" . lang("Male", "register") . "</option>
                                <option" . (getInputValue("gender") == 'Female' ? ' selected' : '') . " value=\"Female\">" . lang("Female", "register") . "</option>
										  <option" . (getInputValue("gender") == 'Other' ? ' selected' : '') . " value=\"Other\">" . lang("Other", "register") . "</option>
                            </select>
                        </div>
                    </div>";
                }

                if(config("userinfo_birthday_required") || config("userinfo_birthyear_required")) {
                    // Determine the first label ID. UU-stuff.
                    $firstLabel = "";
                    if(config("userinfo_birthday_required")) {
                        $firstLabel = "day";
                    } else if (config("userinfo_birthyear_required")) {
                        $firstLabel = "year";
                    }

                    $content .= "<div class=\"form-group\"><label for=\"$firstLabel\">" . _("Birthday") . "</label>
                        <div class=\"elements\">";

                    if (config("userinfo_birthday_required")) {
                        // Days
                        $content .= "<select id=\"day\" class=\"" . checkForErrorsInput("day") . "\" name=\"day\"><option value=\"-1\">" . _("Day") . "</option>";

                        $savedDay = getInputValue('day');
                        for ($day=1; $day <= 31; $day++) { 
                            $content .= "<option" . ($savedDay == $day ? ' selected' : '') . " value=\"$day\">$day</option>";
                        }
                        $content .= "</select>";

                        // Months
                        $content .= "<select id=\"month\" class=\"" . checkForErrorsInput("month") . "\" name=\"month\"><option value=\"-1\">" . _("Month") . "</option>";
                        
                        $savedMonth = getInputValue('month');
                        for ($month=1; $month <= 12; $month++) { 
                            $content .= "<option" . ($savedMonth == $month ? ' selected' : '') . " value=\"$month\">$month</option>";
                        }
                        $content .= "</select>";
                    }

                    if (config("userinfo_birthyear_required")) {
                        // Years
                        $content .= "<select id=\"year\" class=\"" . checkForErrorsInput("year") . "\" style=\"width:99px;\" name=\"year\"><option value=\"-1\">" . _("Year") . "</option>";
                        $savedYear = getInputValue('year');
                        for ($year=1900; $year <= date("Y"); $year++) {
                            $content .= "<option" . ($savedYear == $year ? ' selected' : '') . " value=\"$year\">$year</option>";
                        }
                        $content .= "</select>";
                    }

                    $content .= "</div></div>";
                }

                if (config("userinfo_cellphone_required")) {
                    $content .= "
                    <div class=\"form-group\">
                        <label for=\"cellphone\">" . _("Cell phone") . "</label>

                        <div class=\"elements\">
                            <input type=\"text\" id=\"cellphone\" class=\"input " . checkForErrorsInput("cellphone") . "\" name=\"cellphone\" required value=\"" . getInputValue("cellphone") . "\" />
                        </div>
                    </div>";
                }

                $content .= "<div class=\"form-group\">
                    <input type=\"submit\" name=\"submit\" value=\"" . _("Create user") . "\" />
                </div>
            </form>
        </div>";
        break;

}

/**
 * Checks if $errors has en error in $fieldName. Returns the string "error" if there is an error found.
 * 
 * @param string $fieldName
 * @return string
 */
function checkForErrorsInput($fieldName) {
    global $errors;

    if (in_array($fieldName, $errors)) {
        return "error";
    }

    return "";
}

/**
 * Checks if $formData 
 * 
 * @param string $fieldName
 * @return string
 */
function getInputValue($fieldName, $default="") {
    global $formData;

    if (isset($formData[$fieldName])) {
        return $formData[$fieldName];
    }

    return $default;
}

