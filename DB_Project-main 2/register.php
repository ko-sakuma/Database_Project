<?php
// Header
$pageTitle = "Register new account";
include_once("header.php");
?>

<?php
// Add isset statements for error messages from process_registrations
if (isset($_GET["error"])) {
    if ($_GET["error"] == "register_is_empty") {
        print_alert("Your input is empty. Please try to register again", "danger");
    } elseif ($_GET["error"] == "invalid_user_email") {
        print_alert("Your email is not valid. Please try to register again using a valid email.", "danger");
    } elseif ($_GET["error"] == "not_unique_user") {
        print_alert("Your user name or email address was used to register already. Please login using your credentials.", "danger");
    } elseif ($_GET["error"] == "not_matching_user_password") {
        print_alert("Your password input does not match. Please try again.", "danger");
    } elseif ($_GET["error"] == "user_password_length_fail") {
        print_alert("Your password is not between 8 and 16 characters. Please change it accordingly and try again.", "danger");
    }
}

?>

    <!-- Title of Page-->
    <div class="container">
        <h2 style='text-align:center;' class="my-3">Register new account</h2> <!-- here the h2 title is now centered-->
        <p style='color:red; text-align:center; font-size: small;'>Please be aware that you <b>cannot reset</b> your
            password, user name or email <b>after you have registered</b>. <br>
            Furthermore, your account is <b>limited to being either an seller or buyer</b>. If you would like to
            register as Buyer and Seller you need to have two accounts</p>

        <!-- Create auction form -->
        <form method="POST" action="process_registration.php">

            <!-- Buyer or Seller-->
            <div class="form-group row">
                <label for="accountType" class="col-sm-2 col-form-label text-right">Registering as a:</label>
                <div class="col-sm-10">
                    <!-- Buyer -->
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="user_role_id" id="accountBuyer" value="0"
                               checked>
                        <label class="form-check-label" for="accountBuyer">Buyer</label>
                    </div>
                    <!-- Seller -->
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="user_role_id" id="accountSeller" value="1">
                        <label class="form-check-label" for="accountSeller">Seller</label>
                    </div>
                    <small id="accountTypeHelp" class="form-text-inline text-muted"><span class="text-danger">* Required.</span></small>
                </div>
            </div>

            <!-- User name-->
            <div class="form-group row">
                <label for="user_name" class="col-sm-2 col-form-label text-right">User name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="user_name" id="user_name" placeholder="User name">
                    <small id="user_nameHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
                </div>
            </div>

            <!-- Email-->
            <div class="form-group row">
                <label for="email" class="col-sm-2 col-form-label text-right">Email</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="user_email" id="email" placeholder="Email">
                    <small id="emailHelp" class="form-text text-muted"><span
                                class="text-danger">* Required.</span></small>
                </div>
            </div>

            <!-- Password-->
            <div class="form-group row">
                <label for="password" class="col-sm-2 col-form-label text-right">Password</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" name="user_password" id="password"
                           placeholder="Password">
                    <small id="passwordHelp" class="form-text text-muted"><span class="text-danger">* Required: Please use a password between 8 and 40 characters.</span></small>
                </div>
            </div>

            <!-- Repeat Password-->
            <div class="form-group row">
                <label for="passwordConfirmation" class="col-sm-2 col-form-label text-right">Repeat password</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" name="user_password_repeat" id="passwordConfirmation"
                           placeholder="Enter password again">
                    <small id="passwordConfirmationHelp" class="form-text text-muted"><span class="text-danger">* Required: Please repeat the password.</span></small>
                </div>
            </div>

            <!-- Submit Button-->
            <div class="form-group row">
                <button type="submit" name="submit" class="btn btn-primary form-control">Register</button>
            </div>

        </form>

        <!-- Login if user has already an account (gets redirected to login via header.php) -->
        <div class="text-center">Already have an account? <a href="" data-toggle="modal"
                                                             data-target="#loginModal">Login</a>

        </div>

<?php
// Footer
include_once("footer.php");
?>