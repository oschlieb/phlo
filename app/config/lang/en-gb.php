<?php

/*----------------------------------------------------------------------------------------
	General Notifications
----------------------------------------------------------------------------------------*/
define("FEEDBACK_UNKNOWN_ERROR", "Unknown error occurred!");

/*----------------------------------------------------------------------------------------
	Email Notifications
----------------------------------------------------------------------------------------*/
define("EMAIL_PASSWORD_RESET_FROM_NAME", "Werkphlo");
define("EMAIL_PASSWORD_RESET_SUBJECT", "Password reset for Werkphlo");
define("EMAIL_PASSWORD_RESET_CONTENT", "Please click on this link to reset your password: ");
define("EMAIL_VERIFICATION_FROM_NAME", "Werkphlo");
define("EMAIL_VERIFICATION_SUBJECT", "Account activation for Werkphlo");
define("EMAIL_VERIFICATION_CONTENT", "Please click on this link to activate your account: ");

/*----------------------------------------------------------------------------------------
	Sign in Notifications
----------------------------------------------------------------------------------------*/
define("FEEDBACK_USERNAME_FIELD_EMPTY", "Username field was empty.");
define("FEEDBACK_PASSWORD_FIELD_EMPTY", "Password field was empty.");
// The "login failed"-message is a security improved feedback that doesn't show a potential attacker if the user exists or not
define("FEEDBACK_LOGIN_FAILED", "Login failed.");
define("FEEDBACK_ACCOUNT_NOT_ACTIVATED_YET", "Your account is not activated yet. Please click on the confirm by clicking on the link in the email that was sent.");
define("FEEDBACK_COOKIE_INVALID", "Your remember-me-cookie is invalid.");
define("FEEDBACK_COOKIE_LOGIN_SUCCESSFUL", "You were successfully logged in via the remember-me-cookie.");
define("FEEDBACK_USER_DOES_NOT_EXIST", "This user does not exist.");
/* Password changes */
define("FEEDBACK_PASSWORD_WRONG", "Password was wrong.");
define("FEEDBACK_PASSWORD_WRONG_3_TIMES", "You have typed in a wrong password 3 or more times already. Please wait 30 seconds to try again.");
define("FEEDBACK_PASSWORD_REPEAT_WRONG", "Password and password repeat are not the same.");
define("FEEDBACK_PASSWORD_TOO_SHORT", "Password has a minimum length of 6 characters.");
define("FEEDBACK_PASSWORD_RESET_TOKEN_FAIL", "Could not write token to database.");
define("FEEDBACK_PASSWORD_RESET_MAIL_SENDING_SUCCESSFUL", "A password reset mail has been sent successfully.");
define("FEEDBACK_PASSWORD_RESET_MAIL_SENDING_ERROR", "Password reset mail could not be sent due to: ");
define("FEEDBACK_PASSWORD_RESET_COMBINATION_DOES_NOT_EXIST", "Username/Verification code combination does not exist.");
define("FEEDBACK_PASSWORD_RESET_LINK_VALID", "Password reset validation link is valid. Please change the password now.");
define("FEEDBACK_PASSWORD_RESET_LINK_EXPIRED", "Your reset link has expired. Please use the reset link within one hour.");
define("FEEDBACK_PASSWORD_RESET_TOKEN_MISSING", "No password reset token.");
define("FEEDBACK_PASSWORD_CHANGE_SUCCESSFUL", "Password successfully changed.");
define("FEEDBACK_PASSWORD_CHANGE_FAILED", "Sorry, your password changing failed.");
/* Account changes */
define("FEEDBACK_ACCOUNT_UPGRADE_SUCCESSFUL", "Account upgrade was successful.");
define("FEEDBACK_ACCOUNT_UPGRADE_FAILED", "Account upgrade failed.");
define("FEEDBACK_ACCOUNT_DOWNGRADE_SUCCESSFUL", "Account downgrade was successful.");
define("FEEDBACK_ACCOUNT_DOWNGRADE_FAILED", "Account downgrade failed.");

/*----------------------------------------------------------------------------------------
	Register Notifications
----------------------------------------------------------------------------------------*/
define("FEEDBACK_EMAIL_FIELD_EMPTY", "Email field was empty.");
define("FEEDBACK_EMAIL_AND_PASSWORD_FIELDS_EMPTY", "Email and passwords fields were empty.");
define("FEEDBACK_USERNAME_SAME_AS_OLD_ONE", "Sorry, that username is the same as your current one. Please choose another one.");
define("FEEDBACK_USERNAME_ALREADY_TAKEN", "Sorry, that username is already taken. Please choose another one.");
define("FEEDBACK_USER_EMAIL_ALREADY_TAKEN", "Sorry, that email is already in use. Please choose another one.");
define("FEEDBACK_USERNAME_CHANGE_SUCCESSFUL", "Your username has been changed successfully.");
define("FEEDBACK_USERNAME_AND_PASSWORD_FIELD_EMPTY", "Username and password fields were empty.");
define("FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN", "Username does not fit the name scheme: only a-Z and numbers are allowed, 2 to 64 characters.");
define("FEEDBACK_EMAIL_DOES_NOT_FIT_PATTERN", "Sorry, your chosen email does not fit into the email naming pattern.");
define("FEEDBACK_EMAIL_SAME_AS_OLD_ONE", "Sorry, that email address is the same as your current one. Please choose another one.");
define("FEEDBACK_EMAIL_CHANGE_SUCCESSFUL", "Your email address has been changed successfully.");
define("FEEDBACK_CAPTCHA_WRONG", "The entered captcha security characters were wrong.");
define("FEEDBACK_USERNAME_TOO_SHORT_OR_TOO_LONG", "Username cannot be shorter than 2 or longer than 64 characters.");
define("FEEDBACK_EMAIL_TOO_LONG", "Email cannot be longer than 64 characters.");
define("FEEDBACK_ACCOUNT_SUCCESSFULLY_CREATED", "Your account has been created successfully and we have sent you an email. Please click the VERIFICATION LINK within that mail.");
define("FEEDBACK_PROFILE_CREATION_FAILED", "Couldn't create new user profile.");
define("FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED", "Sorry, we could not send you an verification mail. Your account has NOT been created.");
define("FEEDBACK_ACCOUNT_CREATION_FAILED", "Sorry, your registration failed. Please go back and try again.");
define("FEEDBACK_VERIFICATION_MAIL_SENDING_ERROR", "Verification mail could not be sent due to: ");
define("FEEDBACK_VERIFICATION_MAIL_SENDING_SUCCESSFUL", "A verification mail has been sent successfully.");
define("FEEDBACK_ACCOUNT_ACTIVATION_SUCCESSFUL", "Activation was successful! You can now sign in.");
define("FEEDBACK_ACCOUNT_ACTIVATION_FAILED", "Sorry, no such id/verification code combination here...");

/*----------------------------------------------------------------------------------------
	Profile Notifications
----------------------------------------------------------------------------------------*/
define("FEEDBACK_PROFILE_UPDATED", "Hello, yes you have updated your profile.");
define("FEEDBACK_WERKPHLO_DELETE_PROFILE_SUCCESS", "Your profile was deleted successfully.");
define("FEEDBACK_WERKPHLO_DELETE_PROFILE_FAILED", "There was an issue with deleting your profile, please try again later.");
define("FEEDBACK_WERKPHLO_DELETE_PROFILE_DATA_FAILED", "There was an issue with deleting your profile data, please try again later.");

/*----------------------------------------------------------------------------------------
	Avatar Notifications [not currently in use]
----------------------------------------------------------------------------------------*/
define("FEEDBACK_AVATAR_UPLOAD_SUCCESSFUL", "Avatar upload was successful.");
define("FEEDBACK_AVATAR_UPLOAD_WRONG_TYPE", "Only JPEG and PNG files are supported.");
define("FEEDBACK_AVATAR_UPLOAD_TOO_SMALL", "Avatar source file's width/height is too small. Needs to be 100x100 pixel minimum.");
define("FEEDBACK_AVATAR_UPLOAD_TOO_BIG", "Avatar source file is too big. 5 Megabyte is the maximum.");
define("FEEDBACK_AVATAR_FOLDER_DOES_NOT_EXIST_OR_NOT_WRITABLE", "Avatar folder does not exist or is not writable. Please change this via chmod 775 or 777.");
define("FEEDBACK_AVATAR_IMAGE_UPLOAD_FAILED", "Something went wrong with the image upload.");

/*----------------------------------------------------------------------------------------
	Note Notifications [not currently in use]
----------------------------------------------------------------------------------------*/
define("FEEDBACK_NOTE_CREATION_FAILED", "Note creation failed.");
define("FEEDBACK_NOTE_EDITING_FAILED", "Note editing failed.");
define("FEEDBACK_NOTE_DELETION_FAILED", "Note deletion failed.");

/*----------------------------------------------------------------------------------------
	Facebook Notifications
----------------------------------------------------------------------------------------*/
define("FEEDBACK_FACEBOOK_LOGIN_NOT_REGISTERED", "Sorry, you don't have an account here. Please register first.");
define("FEEDBACK_FACEBOOK_EMAIL_NEEDED", "Sorry, but you need to allow us to see your email address to register.");
define("FEEDBACK_FACEBOOK_UID_ALREADY_EXISTS", "Sorry, but you have already registered here (your Facebook ID exists in our database).");
define("FEEDBACK_FACEBOOK_EMAIL_ALREADY_EXISTS", "Sorry, but you have already registered here (your Facebook email exists in our database).");
define("FEEDBACK_FACEBOOK_USERNAME_ALREADY_EXISTS", "Sorry, but you have already registered here (your Facebook username exists in our database).");
define("FEEDBACK_FACEBOOK_REGISTER_SUCCESSFUL", "You have been successfully registered with Facebook.");
define("FEEDBACK_FACEBOOK_OFFLINE", "We could not reach the Facebook servers. Maybe Facebook is offline (that really happens sometimes).");

/*----------------------------------------------------------------------------------------
	Account Notifications
----------------------------------------------------------------------------------------*/
define("FEEDBACK_WERKPHLO_ADD_NAME_MISSING", "Sorry, Werkphlo name is missing.");
define("FEEDBACK_WERKPHLO_ADD_TYPE_MISSING", "Sorry, Werkphlo type is missing.");
define("FEEDBACK_WERKPHLO_ADD_DUPLCIATE_NAME", "Sorry, this name is already taken please use an alternative.");
define("FEEDBACK_WERKPHLO_ADD_SUCCESS", "Your new Werkphlo has been added successfully.");
define("FEEDBACK_WERKPHLO_ADD_FAILURE", "Sorry, there has been an issue adding a new Werkphlo, please try again.");
define("FEEDBACK_WERKPHLO_DELETE_SUCCESS", "Your Werkphlo has now been deleted.");
define("FEEDBACK_WERKPHLO_DELETE_FAILURE", "Sorry, your Werkphlo has not been deleted please check your details and try again.");
define("FEEDBACK_WERKPHLO_SEARCH_FAILURE", "Sorry, no Werkphlos match your search criteria.");
define("FEEDBACK_WERKPHLO_NOT_CREATED", "Hey, it doesn't look like you have any Werkphlos yet. Add a new Werkphlo below.");