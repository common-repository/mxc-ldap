<?php
/*
Plugin Name: MXC LDAP
Plugin URI: http://www.maxicours.com
Description:  Authenticates Wordpress users against LDAP.
Version: 1.1
Author: Olivier FONTES
Author URI: http://wp.famille-fontes.net
*/
define ('LDAP_HOST', 'localhost');
define ('LDAP_PORT', 389);
define ('LDAP_VERSION', 3);
define ('BASE_DN', 'ou=users,dc=example,dc=com');
define ('ADMIN_DN', 'cn=admin,dc=example,dc=com');
define ('ADMIN_PW', 'password');

define ('LOGIN', 'uid');


define ('USER_EMPTY', '<b>Erreur</b>: Le login est vide.');
define ('PASS_EMPTY', '<b>Erreur</b>: Le mot de passe est vide.');
define ('INVALID_USERNAME', '<b>Erreur</b>: Nom d\'utilisateur invalide.');
define ('INVALID_PASS', '<b>Erreur</b>: Mot de passe invalide.');
define ('NO_HOST', '<b>Erreur</b>: Erreur &agrave; la connexion au serveur ldap.');

require_once( ABSPATH . WPINC . '/registration.php');
//Redefine wp_authenticate
if ( !function_exists('wp_authenticate') ) :
function wp_authenticate($username, $password) {
    $username = sanitize_user($username);

    if ( '' == $username )
        return new WP_Error('empty_username', __(USER_EMPTY));

    if ( '' == $password )
        return new WP_Error('empty_password', __(PASS_EMPTY));

    //Check if user exists
    $user = get_userdatabylogin($username);
    if ( !$user || ($user->user_login != $username) ) {
        // Search into LDAP
        $ldap = ldap_connect(LDAP_HOST, LDAP_PORT)
            or die(NO_HOST);
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, LDAP_VERSION);
        $ldapbind = ldap_bind($ldap, ADMIN_DN, ADMIN_PW);

        $result = ldap_search($ldap, BASE_DN, '(' . LOGIN . '=' . $username . ')', array(LOGIN, 'sn', 'givenname', 'mail'));
        $ldapuser = ldap_get_entries($ldap, $result);

        if ($ldapuser['count'] == 1) {
            //Create user using wp standard include
            $userData = array(
                'user_pass'     => microtime(),
                'user_login'    => $ldapuser[0][LOGIN][0],
                'user_nicename' => $ldapuser[0]['givenname'][0] . ' ' . $ldapuser[0]['sn'][0],
                'user_email'    => $ldapuser[0]['mail'][0],
                'display_name'  => $ldapuser[0]['givenname'][0] . ' ' . $ldapuser[0]['sn'][0],
                'first_name'    => $ldapuser[0]['givenname'][0],
                'last_name'     => $ldapuser[0]['sn'][0]
                );
            wp_insert_user($userData);
            $user = get_userdatabylogin($username);
        } else {
            do_action( 'wp_login_failed', $username );
            return new WP_Error('invalid_username', __(INVALID_USERNAME));
        }
    }

    $user = apply_filters('wp_authenticate_user', $user, $password);

    // LDAP Auth
    $ldap = ldap_connect(LDAP_HOST, LDAP_PORT)
        or die(NO_HOST);
    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, LDAP_VERSION);
    $ldapbind = @ldap_bind($ldap, LOGIN .'=' . $username . ',' . BASE_DN, $password);

    if ($ldapbind == true) {
        $userData = array(
            'ID'        => $user->ID,
            'user_pass' => $password
        );
        wp_update_user($userData);
        return new WP_User($user->ID);
    } else {
        // Try mysql auth
        if ( is_wp_error($user) ) {
            do_action( 'wp_login_failed', $username );
            return $user;
        }
        if ( !wp_check_password($password, $user->user_pass, $user->ID) ) {
            do_action( 'wp_login_failed', $username );
            return new WP_Error('incorrect_password', __(INVALID_PASS));
        }
        return new WP_User($user->ID);
    }
}
endif;
