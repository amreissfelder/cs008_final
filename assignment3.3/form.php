<?php
include "top.php";
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
//
// SECTION: 1a.
// variables for the classroom purposes to help find errors.

$debug = false;

//This statement allows us in the classroom to see what our variables are
//This is NEVER done on a live site
if (isset($_GET["debug"])) {
    $debug = true;
}

if ($debug) {
    print "<p>DEBUG MODE IS ON</p>";
}

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1b Security
//
// define security variable to be used in SECTION 2a.

$thisURL = $domain . $phpSelf;


//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1c form variables
//
// Initialize variables one for each form element
// in the order they appear on the form

$firstName = "";
$lastName = "";
$email = "youremail@uvm.edu";
$comments = "";
$gender = "Male";
$cakes = false;
$scones = false;
$cookies = false;
$pies = false;
$muffins = false;
$breads = false;
$frequency = "Daily"; // picks this option

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
//SECTION: 1d form error flags
//
//Initilize Error Flags one for each form element we validate
//in the order they appear in section 1c

$firstNameERROR = false;
$lastNameERROR = false;
$emailERROR = false;
$commentERROR = false;
$genderERROR = false;
$cakesERROR = false;
$sconesERROR = false;
$cookiesERROR = false;
$piesERROR = false;
$muffinsERROR = false;
$breadsERROR = false;
$frequencyERROR = false;

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
//SECTION: 1e misc variables 
//
//create array to hold error messages filled (if any) in 2d displayed in 3c

$errorMsg = array(); 
 
// array used to hold form values that will be written to a CSV file
$dataRecord = array();

        // have we mailed the information to the user?
$mailed=false;

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2 Process for when the form is submitted
//
if (isset($_POST["btnSubmit"])) {

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2a Security
    //
    if (!securityCheck($thisURL)) {
        $msg = "<p>Sorry you cannot access this page. ";
        $msg.= "Security breach detected and reported</p>";
        die($msg);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2b Sanitize (clean) data
    // remove any potential JavaScript or html code from users input oon the
    // form.  Note it is beset to follow the same order as declared in section 1c.

    $firstName = htmlentities($_POST["txtFirstName"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $firstName;
    
    $lastName = htmlentities($_POST["txtLastName"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $lastName;

    $email = filter_var($_POST["txtEmail"], FILTER_SANITIZE_EMAIL);    
    $dataRecord[] = $email;
    
    $comments = htmlentities($_POST["txtComments"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $comments;
    
    $gender = htmlentities($_POST["radGender"], ENT_QUOTES, "UTF-8");
    $genderRecord[] = $gender;
    
if (isset($_POST["chkCakes"])) {
    $cakes = true;
} else {
    $cakes = false;
}
$dataRecord[] = $cakes; 
   
if (isset($_POST["chkScones"])) {
    $scones = true;
} else {
    $scones = false;
}
$dataRecord[] = $scones;

if (isset($_POST["chkCookies"])) {
    $cookies = true;
} else {
    $cookies = false;
}
$dataRecord[] = $cookies;

if (isset($_POST["chkPies"])) {
    $pies = true;
} else {
    $pies = false;
}
$dataRecord[] = $pies;

if (isset($_POST["chkMuffins"])) {
    $muffins = true;
} else {
    $muffins = false;
}
$dataRecord[] = $muffins;

if (isset($_POST["chkBreads"])) {
    $breads = true;
} else {
    $breads = false;
}
$dataRecord[] = $breads;

$frequency = htmlentities($_POST["lstFrequency"], ENT_QUOTES, "UTF-8");
$dataRecord[] = $frequency;

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2c validation
    //
    // Validation section.  Check each value for possible errors, empty or
    // not what we expect.  You will need an IF block for each element you will
    // check (see above section 1c and 1d).  The if blocks should also be in the 
    // order that the elements appear on your form so that the error messages
    // will be in the order they appear. errorMsg will be displayed on the form
    // see section 3b. The error flag ($emailERROR) will be used in section 3c.

    if ($firstName == "") {
        $errorMsg[] = "Please enter your first name";
        $firstNameERROR = true;
    } elseif (!verifyAlphaNum($firstName)) {
        $errorMsg[] = "Your first name appears to have extra characters.";
        $firstNameERROR = true;
    }
    
    if ($lastName == "") {
        $errorMsg[] = "Please enter your last name";
        $lastNameERROR = true;
    } elseif (!verifyAlphaNum($lastName)) {
        $errorMsg[] = "Your last name appears to have extra characters.";
        $lastNameERROR = true;
    }

    if ($email == "") {
        $errorMsg[] = "Please enter your email address";
        $emailERROR = true;
    } elseif (!verifyEmail($email)) {
        $errorMsg[] = "Your email address appears to be incorrect.";
        $emailERROR = true;
    }

   // if ($comments == "") {
     //   $errorMsg[] = "Please enter a comment";
       // $commentERROR = true;
   // }
    
    // no error checking if we set a default value

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2d Process Form - Passed Validation
    //
    // Process for when the form passes validation (the errorMsg array is empty)
    //
    if (!$errorMsg) {
        if ($debug)
            print "<p>Form is valid</p>"; 


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2e Save Dat
    //
    // This block saves teh data to a CSV file.
    $fileExt = ".csv";
    $myFileName = "data/registration"; //make folder
    
    $filename = $myFileName . $fileExt;
    
    if ($debug){
        print "\n\n<p>filename is " . $filename;
    }
    
    // open file for append
    $file = fopen($filename, 'a');
    
    // write forms informations 
    fputcsv($file, $dataRecord);
    
    // close file
    fclose($file);


    //@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    //SECTION: 2f Create message
    //
    // build a message to display on the screen in section 3a and mail
    // to the person filling out the form (section 2g).

$message = '<h2>Your information:</h2>';

foreach ($_POST as $key => $value) {
    if($key!="btnSubmit") {
        $message .= "<p class=confirm>";

        // breaks up the form names into words. for example
        // txtFirstName becomes First Name
    $camelCase = preg_split('/(?=[A-Z])/', substr($key, 3));

    foreach ($camelCase as $one) {
        $message .= $one . " ";
    }
    }
    $message .= " = " . htmlentities($value, ENT_QUOTES, "UTF-8") . "</p>";
}


        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2g Mail to user
        //
        // Process for mailing a message which contains the forms data
        // the message was built in section 2f.
        $to = $email; // the person who filled out the form
        $cc = "";
        $bcc = "";

        $from = "You successfully registered! <areissfe@uvm.edu>";

        // subject of mail should make sense to your form
        $todaysDate = strftime("%x");   
        $subject = "Email Registered: " . $todaysDate;

        $mailed = sendMail($to, $cc, $bcc, $from, $subject, $message);

    } // end form is valid

}    // ends if form was submitted.


//#############################################################################
//
// SECTION 3 Display Form
//
?>

<article id="main">
    
    <?php
    //####################################
    //
    // SECTION 3a. 
    // 
    // If its the first time coming to the form or there are errors we are going
    // to display the form.
    if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit 
        print "<h1>Your request has ";
    
        if (!$mailed) {
            print "not ";
        }
    
        print "been processed</h1>";
    
        print "<p>A copy of this message has ";
    
        if (!$mailed) {
            print "not ";
        }
        print "been sent</p>";
        print "<p>To: " . $email . "</p>";
    
        print "<p>Mail Message: Thank you for registering! You have now "
        . "been added to our mailing list and will shortly start receiving your "
                . "recipes.  Contact us at areissfe@uvm.edu to unsubscribe from"
                . " our services at any time.</p>";
    
        print $message;
    } else {
    
    
     //####################################
    //
    // SECTION 3b Error Messages
    //
    // display any error messages before we print out the form
    
    if ($errorMsg) {
        print '<div id="errors">' . "\n";
        print "<h2>Your form has the following mistakes that need to be fixed</h2>\n";
        print "<ol>\n";
        
        foreach ($errorMsg as $err) {
            print "<li>" . $err . "</li>\n";
        }
        
        print "</ol>\n";
        print "</div>\n";
    }
    
        //####################################
        //
        // SECTION 3c html Form
        //
        /* Display the HTML form. note that the action is to this same page. $phpSelf
          is defined in top.php
          NOTE the line:
          value="<?php print $email; ?>
          this makes the form sticky by displaying either the initial default value (line ??)
          or the value they typed in (line ??)
          NOTE this line:
          <?php if($emailERROR) print 'class="mistake"'; ?>
          this prints out a css class so that we can highlight the background etc. to
          make it stand out that a mistake happened here.
         */
        ?>

        <form action="<?php print $phpSelf; ?>"
              method="post"
              id="frmRegister">
            
            <fieldset class="wrapper">
                <legend>Register Today</legend>
                <p>Your personal information will not be shared with third parties.</p>

                <fieldset class="wrapperTwo">
                    <legend>Please complete the following form</legend>

                    <fieldset class="contact">
                        <legend>Contact Information</legend>
                        
                        <label for="txtFirstName" class="required">First Name
                            <input type="text"
                                   id="txtFirstName"
                                   name="txtFirstName"
                                   value="<?php print $firstName; ?>"
                                   tabindex="100"
                                   maxlength="45"
                                   placeholder="Enter your first name"
                                   <?php if ($firstNameERROR) print 
                                       'class="mistake"';?>
                                   onfocus="this.select()"
                                   autofocus>
                        </label>
                        
                        <label for="txtLastName" class="required">Last Name
                            <input type="text"
                                   id="txtLastName"
                                   name="txtLastName"
                                   value="<?php print $lastName; ?>"
                                   tabindex="100"
                                   maxlength="45"
                                   placeholder="Enter your last name"
                                   <?php if ($lastNameERROR) print 
                                       'class="mistake"';?>
                                   onfocus="this.select()"
                                   >
                        </label>
                        
                        
                    <label for="txtEmail" class="required">Email
                        <input type="text" 
                               id="txtEmail" 
                               name="txtEmail"
                               value="<?php print $email; ?>"
                               tabindex="120" 
                               maxlength="45" 
                               placeholder="Enter a valid email address"
                               <?php if ($emailERROR) print 'class="mistake"'; ?>
                               onfocus="this.select()"
                               >
                    </label>
                </fieldset> <!-- ends contact -->
                
                <fieldset  class="textarea">					
                    <label for="txtComments" class="textboxarea">Comments</label>
                    <textarea id="txtComments" 
                              name="txtComments" 
                              tabindex="200"
                              <?php if($commentERROR) print 'class="mistake"'; ?>
                              onfocus="this.select()" 
                              ><?php print $comments; ?></textarea>
                </fieldset> <!-- ends comments -->
                
                <fieldset class="radio">
                    <legend>What is your gender?</legend>
                    <label><input type="radio"
                                  id="radGenderMale"
                                  name="radGender"
                                  value="Male"
                                  <?php if($gender=="Male")print'checked'?>
                                  tabindex="330">Male</label>
                    <label><input type="radio"
                                  id="radGenderFemale"
                                  name="radGender"
                                  value="Female"
                                  <?php if($gender=="Female")print'checked'?>
                                  tabindex="340">Female</label>
                    <label><input type="radio"
                                  id="radGenderOther"
                                  name="radGender"
                                  value="Other"
                                  <?php if($gender=="Other")print'checked'?>
                                  tabindex="340">Other</label>
                </fieldset> <!-- ends radio button -->
                
                <fieldset class="checkbox">
                    <legend>Do you like (check all that apply):</legend>
                    <label><input type="checkbox"
                                  id="chkCakes"
                                  name="chkCakes"
                                  value="Cakes"
                                  <?php if ($cakes) print " checked "; ?>
                                  tabindex="420"> Cakes </label>
                    
                    <label><input type="checkbox" 
                                  id="chkScones"
                                  name="chkScones"
                                  value="Scones"
                                  <?php if ($scones)  print " checked "; ?>
                                  tabindex="430"> Scones </label>
                    <label><input type="checkbox"
                                  id="chkCookies"
                                  name="chkCookies"
                                  value="Cookies"
                                  <?php if ($cookies) print " checked "; ?>
                                  tabindex="430"> Cookies </label>
                    <label><input type="checkbox"
                                  id="chkPies"
                                  name="chkPies"
                                  value="Pies"
                                  <?php if ($pies) print " checked "; ?>
                                  tabindex="430"> Pies </label>
                    <label><input type="checkbox"
                                  id="chkMuffins"
                                  name="chkMuffins"
                                  value="Muffins"
                                  <?php if ($muffins) print " checked "; ?>
                                  tabindex="430"> Muffins </label>
                    <label><input type="checkbox"
                                  id="chkBread"
                                  name="chkBread"
                                  value="Bread"
                                  <?php if ($breads) print " checked "; ?>
                                  tabindex="430"> Bread </label>
                </fieldset> <!-- ends checkboxes -->
                
                <fieldset class="listbox">
                    <label for="lstFrequency">Frequency of email's</label>
                    <select id="lstFrequency"
                            name="lstFrequency"
                            tabindex="520">
                        <option <?php if($frequency=="Daily")print " selected"; ?>
                            value="Daily">Daily</option>
                        
                        <option <?php if($frequency=="Weekly") print " selected"; ?>
                            value="Weekly">Weekly</option>
                        
                        <option <?php if($frequency=="Monthly") print " selected"; ?>
                            value="Monthly">Monthly</option>
                        <option <?php if ($frequency=="Yearly") print " selected"; ?>
                            value="Yearly">Yearly</option>
                    </select>
                </fieldset> <!-- ends list boxes -->

            </fieldset> <!-- ends wrapper Two -->
                
                <fieldset class="buttons">
                    <legend></legend>
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Register" tabindex="900" class="button">
                </fieldset> <!-- ends buttons -->
                
            </fieldset> <!-- Ends Wrapper -->
        </form>
    
    <?php
    } // end body submit
    ?>

</article>

<?php include "footer.php"; ?>

</body>
</html>