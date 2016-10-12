<?php

$eventID = $sessioninfo->eventID;
$action = $_GET['action'];
$acl_access = acl_access("wannabeadmin", "", $eventID);

if($acl_access == 'No') die("You don't have access to this");

$wannabeManager = \Wannabe\Manager::getInstance();
$onlineUserID = $sessioninfo->userID;

$request     = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
$requestGet  = $request->query;
$requestPost = $request->request;

switch ($action) {

    // -------------------------------- [LIST QUESTIONS] -------------------------------- //
    case "questions":
        if ($acl_access != "Admin") die("You do not have sufficient privileges.");

        $questions = $wannabeManager->getQuestions(array($eventID));

        $content .= "<h1 class=\"page-title\">" . _("Edit wannabe questions") . "</h1>";
        $content .= "<div class=\"action-toolbar\">
            <div class=\"action\"><a href=\"?module=$module&amp;action=questions\">" . _("List questions") . "</a></div>
            <div class=\"action\"><a href=\"?module=$module&amp;action=editQuestion&amp;questionID=-1\">" . _("New question") . "</a></div>
        </div>";

        // List questions.
        if (count($questions) > 0) {
            $content .= "<div class=\"table\">
                <div class=\"row table-header\">
                    <div class=\"cell\">" . _("Question") . "</div>
                    <div class=\"cell\">" . _("Actions") . "</div>
                </div>";
            foreach ($questions as $question) {
                $content .= "<div class=\"row\">
                    <div class=\"cell\">" . $question->getQuestionData() . "</div>
                    <div class=\"cell\">
                        <a href=\"?module=$module&amp;action=editQuestion&amp;questionID=" . $question->getQuestionID() . "\">" . _("Edit") . "</a>
                        <a href=\"?module=$module&amp;action=editQuestion&amp;questionID=" . $question->getQuestionID() . "&amp;delete=true\">" . _("Delete") . "</a>
                    </div>
                </div>";
            }
            $content .= "</div>";
        } else {
            $content .= "<div>" . _("No questions has been created yet.") . "</div>";
        }

        break;

    // -------------------------------- [EDIT/NEW QUESTION] -------------------------------- //
    case "editQuestion":
        if ($acl_access != "Admin") die("You do not have sufficient privileges.");

        $question = null;
        $editMode = false;
        $errors = array();

        if ($requestGet->has("questionID") && intval($requestGet->get("questionID")) > 0) {
            $question = $wannabeManager->getQuestionByID($requestGet->getDigits("questionID"), array($eventID));
            if ($question instanceof \Wannabe\Question == false) {
                $content .= "<div>The question was not found.</div>";
                return; // Stop file!
            }

            // Delete?
            if ($requestGet->has("delete") && $requestGet->getBoolean("delete") === true) {
                $wannabeManager->deleteQuestion($question); // Logged in method
                header("Location: ?module=$module&action=questions&deletedQuestion=true");
                die();
            }

            $editMode = true;
        }

        // Save the form?
        if ($requestPost->has("save-question")) {
            $questionData = $requestPost->get("questionData", "");
            $questionType = $requestPost->get("questionType", "");
            $progName = $requestPost->get("prog_name", "");

            // Verify that all of the fields are filled in.
            if (mb_strlen($questionData, "UTF-8") < 1 || strlen($questionType) < 1) {
                $errors[] = array("<p>All fields are required!</p>");
            } else {
                // create question?
                if ($editMode == false) {
                    $question = new \Wannabe\Question(-1);
                    $question->setQuestionEventID($eventID);
                }

                $question->setQuestionData($questionData);
                $question->setQuestionType($questionType);
                $question->setProgrammaticName($progName);
                $question->commitChanges();

                // Log this action.
                $log['eventID'] = $eventID;
                log_add ("wannabeadmin", "addWannabeQuestion", serialize($log));

                header("Location: ?module=$module&action=questions&createdNew=true");
                die();
            }
        }

        $content .= "<h1 class=\"page-title\">" . _(($editMode ? "Edit" : "New") . " question") . "</h1>";
        $content .= "<div class=\"action-toolbar\">
            <div class=\"action\"><a href=\"?module=$module&amp;action=questions\">" . _("List questions") . "</a></div>
        </div>";

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $content .= "<div class=\"error\">" . $error . "</div>";
            }
        }

        // Print edit form
        $content .= "<form action=\"?module=$module&amp;action=editQuestion&amp;questionID=" . ($editMode ? $question->getQuestionID() : "-1") . "\" method=\"post\">
            <div class=\"table no-colour\">
                <div class=\"row\">
                    <div class=\"cell\"><strong>" . _("Programmatic name") . "</strong></div>
                    <div class=\"cell\"><input name=\"prog_name\" type=\"text\" value=\"" . ($editMode ? $question->getProgrammaticName() : "") . "\" /></div>
                </div>
                <div class=\"row\">
                    <div class=\"cell\"><strong>" . _("Question") . "</strong></div>
                    <div class=\"cell\"><textarea name=\"questionData\" cols=\"50\" rows=\"5\">" . ($editMode ? $question->getQuestionData() : "") . "</textarea></div>
                </div>
                <div class=\"row\">
                    <div class=\"cell\"><strong>" . _("Type") . "</strong></div>
                    <div class=\"cell\">
                        <select name=\"questionType\">";
                        foreach ($wannabeManager->getQuestionTypes() as $type) {
                            $langString = mb_strtoupper(mb_substr($type, 0, 1)) . mb_substr($type, 1);
                            $content .= "<option value=\"$type\"" . ($editMode ? ($question->getQuestionType() == $type ? " selected" : "") : "") . ">" . _($langString) . "</option>";
                        }
                        $content .= "</select>
                    </div>
                </div>
                <div class=\"row\">
                    <div class=\"cell\">&nbsp;</div>
                    <div class=\"cell\"><input type=\"submit\" name=\"save-question\" value=\"Save\" /></div>
                </div>
            </div>
        </form>";       

        break;

    // -------------------------------- [LIST CREWS] -------------------------------- //
    case "crews":
        if ($acl_access != "Admin") die("You do not have sufficient privileges.");

        $crews = $wannabeManager->getCrews(array($eventID));

        $content .= "<h1 class=\"page-title\">" . _("Edit wannabe crews") . "</h1>";
        $content .= "<div class=\"action-toolbar\">
            <div class=\"action\"><a href=\"?module=$module&amp;action=crews\">" . _("List crews") . "</a></div>
            <div class=\"action\"><a href=\"?module=$module&amp;action=editCrew&amp;crewID=-1\">" . _("New crew") . "</a></div>
        </div>";

        // List crews.
        if (count($crews) > 0) {
            $content .= "<div class=\"table\">
                <div class=\"row table-header\">
                    <div class=\"cell\">" . _("Crew") . "</div>
                    <div class=\"cell\">" . _("Actions") . "</div>
                </div>";
            foreach ($crews as $crew) {
                $content .= "<div class=\"row\">
                    <div class=\"cell\">" . $crew->getName() . "</div>
                    <div class=\"cell\">
                        <a href=\"?module=$module&amp;action=editCrew&amp;crewID=" . $crew->getCrewID() . "\">" . _("Edit") . "</a>
                        <a href=\"?module=$module&amp;action=editCrew&amp;crewID=" . $crew->getCrewID() . "&amp;delete=true\">" . _("Delete") . "</a>
                    </div>
                </div>";
            }
            $content .= "</div>";
        } else {
            $content .= "<div>" . _("No crews has been created yet.") . "</div>";
        }

        break;

    // -------------------------------- [EDIT/NEW CREW] -------------------------------- //
    case "editCrew":
        if ($acl_access != "Admin") die("You do not have sufficient privileges.");

        $crew = null;
        $editMode = false;
        $errors = array();

        if (array_key_exists("crewID", $_GET) && intval($_GET["crewID"]) > 0) {
            $crew = $wannabeManager->getCrewByID($_GET["crewID"], array($eventID));
            if ($crew instanceof \Wannabe\Crew == false) {
                $content .= "<div>The crew was not found.</div>";
                return; // Stop file!
            }

            // Delete?
            if (array_key_exists("delete", $_GET) && $_GET["delete"] === "true") {
                $wannabeManager->deleteCrew($crew); // Logged in method
                header("Location: ?module=$module&action=crews&deletedCrews=true");
                die();
            }

            $editMode = true;
        }

        // Save the form?
        if (array_key_exists("save-crew", $_POST)) {
            $crewName = (array_key_exists("crewName", $_POST) ? $_POST["crewName"] : "");
            $crewDescription = (array_key_exists("description", $_POST) ? $_POST["description"] : "");
            $groupID = (array_key_exists("groupID", $_POST) ? $_POST["groupID"] : 0);

            // Verify that all of the fields are filled in.
            if (mb_strlen($crewName, "UTF-8") < 1) {
                $errors[] = array("<p>All fields are required!</p>");
            } else {
                // create crew?
                if ($editMode == false) {
                    $crew = new \Wannabe\Crew(-1);
                    $crew->setEventID($eventID);
                }

                $crew->setName($crewName);
                $crew->setDescription($crewDescription);
                $crew->setGroupID($groupID);
                $crew->commitChanges();

                // Log this action.
                $log['eventID'] = $eventID;
                log_add ("wannabeadmin", "addWannabeCrew", serialize($log));

                header("Location: ?module=$module&action=crews&createdNew=true");
                die();
            }
        }

        $content .= "<h1 class=\"page-title\">" . _(($editMode ? "Edit" : "New") . " crew") . "</h1>";
        $content .= "<div class=\"action-toolbar\">
            <div class=\"action\"><a href=\"?module=$module&amp;action=crews\">" . _("List crews") . "</a></div>
        </div>";

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $content .= "<div class=\"error\">" . $error . "</div>";
            }
        }

        // Print edit form
        $content .= "<form action=\"?module=$module&amp;action=editCrew&amp;crewID=" . ($editMode ? $crew->getCrewID() : "-1") . "\" method=\"post\">
            <div class=\"table no-colour\">
                <div class=\"row\">
                    <div class=\"cell\"><strong>" . _("Crew Name") . "</strong></div>
                    <div class=\"cell\"><input type=\"text\" name=\"crewName\" value=\"" . ($editMode ? $crew->getName() : "") . "\" /></div>
                </div>
                <div class=\"row\">
                    <div class=\"cell\"><strong>" . _("Crew description") . "</strong></div>
                    <div class=\"cell\"><input type=\"text\" name=\"description\" value=\"" . ($editMode ? $crew->getDescription() : "") . "\" /></div>
                </div>
                <div class=\"row\">
                    <div class=\"cell\"><strong>" . _("Crew group") . "</strong></div>
                    <div class=\"cell\">
                        <select name=\"groupID\">
                            <option value=\"-1\">" . _("Choose group") . "</option>";

                            // List each group
                            $groups = UserGroupManager::getInstance()->getEventGroups();
                            if (count($groups) > 0) {
                                foreach ($groups as $key => $group) {
                                    $selected = ($crew->getGroupID() == $group->getGroupID() ? " selected" : "");
                                    $content .= "<option value=\"" . $group->getGroupID() . "\"$selected>" . $group->getName() . "</option>";
                                }

                            }

                            $content .= "
                        </select>
                    </div>
                </div>
                <div class=\"row\">
                    <div class=\"cell\">&nbsp;</div>
                    <div class=\"cell\"><input type=\"submit\" name=\"save-crew\" value=\"Save\" /></div>
                </div>
            </div>
        </form>";     

        break;

    // -------------------------------- [LIST APPLICATIONS] -------------------------------- //
    case "listApplications":
        if($acl_access != 'Write' && $acl_access != 'Admin') die("You do not have sufficient privileges.");
        $content .= "<h1 class=\"page-title\">" . _("List applications") . "</h1>";

        $applications = $wannabeManager->getApplications();
        $crews = $wannabeManager->getCrews(array($eventID));

        $applicationTableTop = $applicationTableBottom = $waitingApplications = $finishedApplications = "";

        if (count($applications) < 1) {
            $content .= "<div><p>" . _("No applications has been sent.") . "</p></div>";
        } else {
            $applicationTableTop .= "<div class=\"table\">
                <div class=\"row table-header\">
                    <div class=\"cell\">" . _("User") . "</div>";

            // Print table headers for each crew
            if (count($crews) > 0) {
                foreach ($crews as $crew) {
                    $applicationTableTop .= "<div class=\"cell\">" . $crew->getName() . "</div>";
                }
            }

            $applicationTableTop .= "</div>";
            foreach ($applications as $application) {
                $thisLine = "";
                $user = $application->getUser();

                $thisLine .= "<div class=\"row\">
                    <div class=\"cell\">
                        <a href=\"?module=$module&amp;action=viewApplication&amp;userID=" . $user->getUserID() . "\">" . $user->getNick() . "</a>
                    </div>";

                // Print table headers for each crew
                if (count($crews) > 0) {
                    foreach ($crews as $crew) {
                        $thisLine .= "<div class=\"cell crew-approval-" . $application->getAverageScoreFromCrew($crew) . "\"></div>";
                    }
                }

                $thisLine .= "</div>";

                // Append to correct content.
                if (!$application->isFinished()) {
                    $waitingApplications .= $thisLine;
                } else {
                    $finishedApplications .= $thisLine;
                }
            }
            $applicationTableBottom .= "</div>";

            if (mb_strlen($waitingApplications, "UTF-8") > 0) {
                $content .= "<h3>" . _("Waiting applications") . "</h3>";
                $content .= $applicationTableTop . $waitingApplications . $applicationTableBottom;
            }

            if (mb_strlen($finishedApplications, "UTF-8") > 0) {
                $content .= "<h3>" . _("Finished applications") . "</h3>";
                $content .= $applicationTableTop . $finishedApplications . $applicationTableBottom;
            }
        }


        break;

    // -------------------------------- [VIEW APPLICATION] -------------------------------- //
    case "viewApplication":
        if($acl_access != 'Write' && $acl_access != 'Admin') die("You do not have sufficient privileges.");

        if (array_key_exists("userID", $_GET) == false || intval($_GET["userID"]) < 1)
            throw new Exception("Missing userID in GET");

        $crews = $wannabeManager->getCrews(array($eventID));
        $crewIDs = array();

        if (count($crews) > 0) {
            foreach ($crews as $crew) {
                $crewIDs[] = $crew->getCrewID();
            }
        }

        $symRequest = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
        $questions = $wannabeManager->getQuestions(array($eventID));
        $applicationUserID = intval($_GET["userID"]);
        $application = $wannabeManager->getApplication($applicationUserID);
        $applicant = $application->getUser();
        $isAdminOfGroups = UserGroupManager::getInstance()->getUserIsAdminGroups($onlineUserID);
        $crewGroups = UserGroupManager::getInstance()->getCrewsOfGroups($isAdminOfGroups);

        if ($application instanceof \Wannabe\Application == false) {
            throw new Exception("The application was not found.");
        }

        // Handle setPreference
        if ($symRequest->query->has("setPreference") && $symRequest->query->getInt("setPreference") > 0
            && $symRequest->request->has("pref") && $symRequest->request->getInt("pref") > 0) {
            $createComment = true;

            $crewID = $symRequest->query->getInt("setPreference");
            $preference = $symRequest->request->getInt("pref");

            // Has comment? Update it then.
            $comments = $wannabeManager->getApplicationComments($applicant->getUserID(), $crewIDs);
            $commentsByCrewID = array();
            if (count($comments) > 0) {
                foreach ($comments as $comment) {
                    if ($comment->getCrewID() == $crewID && $comment->getAdminUserID() == $onlineUserID) {
                        // Update the comment
                        $createComment = false;
                        $comment->setApproval($preference);
                        $comment->commitChanges();
                    }
                }
            }

            // Create comment if we must.
            if ($createComment) {
                $wannabeManager->createApplicationComment($applicationUserID, $crewID, "", $preference, $onlineUserID, 1);
            }

            header("Location: index.php?module=$module&action=viewApplication&userID=$applicationUserID");
            die();
        }

        // HANDLE COMMENT
        else if ($symRequest->query->has("addComment") && count($crewGroups) > 0) {
            $commentText = $symRequest->request->has("commenttext") ? $symRequest->request->get("commenttext") : "";
            if (mb_strlen(trim($commentText), "UTF-8") > 0) {
                $wannabeManager->createApplicationComment($applicationUserID, $crewGroups[0]->getCrewID(), $commentText, 0, $onlineUserID, 2);
            }

            header("Location: index.php?module=$module&action=viewApplication&userID=$applicationUserID");
            die();
        }

        $content .= "<div class=\"wannabeadmin-app\"><h1 class=\"page-title\">" . $applicant->getFullName() . " (" . $applicant->getNick() . ")</h1>";
        $content .= "
        <div class=\"left-content\">
            <h3>" . _("Userinfo") . "</h3>
            <div class=\"user-info table\" style=\"width:auto;\">
                <div class=\"row\">
                    <div class=\"cell\"><strong>" . _("Name") . "</strong></div>
                    <div class=\"cell\">" . $applicant->getFullName() . "</div>
                </div>
                <div class=\"row\">
                    <div class=\"cell\"><strong>" . _("Birthday") . "</strong></div>
                    <div class=\"cell\">" . date("d.m.Y", $applicant->getBirthdayTimestamp()) . " (" . $applicant->getAge() . " " . _("years old") . ")</div>
                </div>
                <div class=\"row\">
                    <div class=\"cell\"><strong>" . _("Address") . "</strong></div>
                    <div class=\"cell\">" . $applicant->getStreetAddress() . "<br />" . $applicant->getPostNumber() . "</div>
                </div>
            </div>
        </div>

        <div class=\"right-content\">
            <h3>Crew Prefrences</h3>";

        // Crew preferences.
        if (count($crews) > 0) {
            $content .= "<div class=\"crew-pref\">";
            foreach ($crews as $crew) {
                $score = $application->getCrewPreferenceScore($crew);
                $content .= "<div class=\"pref-row pref-" . $crew->getCrewID() . "\">
                    <div class=\"crewname\">" . $crew->getName() . "</div>
                    <div class=\"pref-icons\">
                        <span>" . $wannabeManager->getScoreLabel($score) . "</span>
                        <div class=\"pref-icon pref-icon-$score\" title=\"" . $wannabeManager->getScoreLabel($score) . "\">&nbsp;</div>
                    </div>
                </div>";
            }
            $content .= "</div>";
        }

        $content .= "</div>";
        $content .= "<div class=\"questions-answer table\">";

        // Application responses.
        if (count($questions) > 0) {
            $content .= "
            <div class=\"row table-header\">
                <div class=\"cell\"><strong>" . _("Question") . "</strong></div>
                <div class=\"cell\"><strong>" . _("Answer") . "</strong></div>
            </div>";
            foreach ($questions as $question) {
                $response = $application->getResponseForQuestion($question);
                $content .= "<div class=\"row\">
                    <div class=\"cell\"><strong>" . $question->getQuestionData() . "</strong></div>
                    <div class=\"cell\">" . ($response === null ? _("No answer given") : $response->getResponse()) . "</div>
                </div>";
            }
        }
        $content .= "</div>";

        // COMMENTS
        $comments = $wannabeManager->getApplicationComments($applicant->getUserID(), $crewIDs);
        $commentsByCrewID = $preferenceByCrewID = array();
        if (count($comments) > 0) {
            foreach ($comments as $comment) {
                if ($comment->getCommentType() == \Wannabe\AdminComment::COMMENT_TYPE_ADMINPREF) {
                    $preferenceByCrewID[$comment->getCrewID()][] = $comment;
                } else {
                    $commentsByCrewID[$comment->getCrewID()][] = $comment;
                }
            }
        }

        $content .= "<h3>" . _("Comments") . "</h3><div class=\"left-content\">";
        // Show preference bar from each crew.
        if (count($crews) > 0) {
            $content .= "<div class=\"crewadmin-pref crew-pref\">";
            foreach ($crews as $crew) {
                $comment =
                $content .= "<div class=\"pref-row pref-" . $crew->getCrewID() . "\">
                    <div class=\"crewname\">" . $crew->getName() . "</div>
                    <div class=\"pref-icons\">";

                $numComments = count($preferenceByCrewID[$crew->getCrewID()]);
                if (isset($preferenceByCrewID[$crew->getCrewID()]) && $numComments > 0) {
                    foreach ($preferenceByCrewID[$crew->getCrewID()] as $comment) {
                        /** @var $comment \Wannabe\AdminComment */

                        if ($numComments == 1) {
                            $content .= "<span>" . $comment->getAdminUser()->getNick() . "</span>";
                        }

                        $content .= "<div class=\"pref-icon pref-icon-" . $comment->getApproval() . "\" title=\"" . $comment->getAdminUser()->getNick() . "\">";

                        if ($numComments > 1) {
                            $content .= "&nbsp;";
                        }

                        $content .= "</div>";
                    }
                } else {
                    $content .= "<div class=\"pref-icon pref-icon-0\" title=\"" . _("No response given") . "\">&nbsp;</div>";
                }

                $content .= "</div>
                </div>";
            }
            $content .= "</div>";
        }

        // Set prefrence form
        if (count($crewGroups) > 0) {
            foreach ($crewGroups as $crew) {
                $content .= "<div class=\"set-preference-form\"><strong>" . _("Set preference for") . " " . $crew->getName() . "</strong>";
                $content .= "<form action=\"?module=$module&amp;action=viewApplication&amp;userID=$applicationUserID&amp;setPreference=" . $crew->getCrewID() . "\" method=\"post\">";
                $content .= "   <input type=\"hidden\" name=\"applicationID\" value=\"$applicationUserID\" />";
                $content .= "   <select name=\"pref\">";
                $content .= "       <option value=\"-1\">" . _("Choose preference") . "</option>";
                $content .= "       <option value=\"1\">" . _("Of course!") . "</option>";
                $content .= "       <option value=\"2\">" . _("Sure") . "</option>";
                $content .= "       <option value=\"3\">" . _("Probably") . "</option>";
                $content .= "       <option value=\"4\">" . _("I'd rather not") . "</option>";
                $content .= "       <option value=\"5\">" . _("Not at all") . "</option>";
                $content .= "   </select>";
                $content .= "   <input type=\"submit\" class=\"btn-small\" value=\"" . _("Set") . "\" />";
                $content .= "</form></div>";
            }
        } else {
            $content .= "<div><em>" . _("You are not admin of any crew and can therefor not set any preference.") . "</em></div>";
        }

        $content .= "</div>";
        $content .= "<div class=\"right-content\">";

        if (count($comments) > 0) {
            foreach ($comments as $comment) {
                if ($comment->getCommentType() == \Wannabe\AdminComment::COMMENT_TYPE_ADMINPREF) continue;
                if (mb_strlen(trim($comment->getComment()), "UTF-8") < 1) continue;

                $commentContent = nl2br(htmlspecialchars($comment->getComment()));
                $content .= "<div class=\"comment\">
                    <div class=\"meta\">
                        <div class=\"name\">" . $comment->getAdminUser()->getNick() . "</div>
                        <div class=\"date\"><span class=\"timeago-js\" datetime=\"" . date("Y-m-d H:i:s") . "\"></span></div>
                    </div>
                    <div class=\"comment-content\">" . $commentContent . "</div>
                </div>";
            }
        }

        // Write new comment form
        $content .= "
        <button class=\"newcomment\" onclick=\"showNewComment();\">" . _("Write a comment") . "</button>
        <div class=\"new-comment\">
        <form action=\"?module=$module&amp;action=viewApplication&amp;userID=$applicationUserID&amp;addComment=true\" method=\"post\">
            <textarea name=\"commenttext\" placeholder=\"" . _("Comment...") . "\"></textarea><br />
            <div class=\"new-comment-button\"><input type=\"submit\" value=\"" . _("Send") . "\" /></div>
        </form>
        </div>";

        $content .= "</div>";

        // Set title
        $content .= "</div>
        <script>document.title = \"" . $applicant->getFullName() . " (" . $applicant->getNick() . ") - " . _("Application") . " - \" + document.title;</script>";

        break;

    // -------------------------------- [EXPORT ALLERGIES] -------------------------------- //
    case "allergies":

        $excel = $wannabeManager->getExcelOfAllergies();

        if (is_null($excel)) {
            $content .= "Oooooopps, en feil skjedde...";
        } else {
            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="lancms_allergies.xls"');
            header('Cache-Control: max-age=0');

            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }

        break;

    // -------------------------------- [INDEX] -------------------------------- //
    default:

        $content .= "<ul class=\"wannabeadmin-menu\">";
        if($acl_access == "Admin")
        {
            // User has wannabe adminrights
            $content .= "<li><a href=\"?module=wannabeadmin&amp;action=questions\">"._("Questions")."</a></li>\n";
            $content .= "<li><a href=\"?module=wannabeadmin&amp;action=crews\">"._("Crews")."</a></li>\n";

        } // End acl_access = Admin

        if($acl_access == 'Write' || $acl_access == 'Admin')
        {
            // User has wannabe write-access (may see and write comments)
            $content .= "<li><a href=\"?module=wannabeadmin&amp;action=listApplications\">"._("View Applications")."</a></li>";
            $content .= "<li><a href=\"?module=wannabeadmin&amp;action=allergies\">"._("Export allergies")."</a></li>\n";

        } // End acl_access > Write
        $content .= "</ul>";

        break;

}
