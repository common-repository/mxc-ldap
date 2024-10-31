=== Plugin Name ===
Contributors: olivier.fontes
Donate link: http://www.maxicours.com/
Tags: OpenLDAP, authentication, login
Requires at least: 2.0
Tested up to: 2.6.3
Stable tag: trunk

Authenticates wordpress users on Open LDAP.

== Description ==
This plugin provides LDAP authentification on Open LDAP server.

When a ldap user is submited for the first time, it is created into wordpress database en authenticated trying LDAP and Database auth.

Version History:
1.2 - Correct some bugs (thanks to Duffy for report;)
1.1 - Add password sync LDAP to Mysql
1.0 - Original release.

This plugin is based on simple-ldap-login by Clif GRIFIN
For the needs of our company, we wanted to use our openldap directory to authenticate our users on wordpress. In place of modify simple-ldap-plugin, i decided to do a new one making all we want, but based on simple-ldap-login that seduced me by its simplicity.



== Installation ==

1. Upload the directory "mxc-ldap" to the `/wp-content/plugins/` directory
2. Customize settings by modifying mxc-ldap.php
3. For the moment messages are in french. Maybe a localization will come. For the moment you have to customize by hand.
3. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Howto add a feature ? =
If you feel able to ... do it! If not contact me, i could spend time if you don't want this to be done in the next minute ... i 'm not really able to have more than 24 hours a day.

E-mail me: olivier.fontes[at]maxicours.com

= How passwd is syncrchonized to mysql =
Some users of this plugin asked me to have ability to authenticate user if LDAP is unavailable, using mysql as a password cache. I added a patch that update mysql password every time a successful LDAP auth is done.

== Screenshots ==

There is no screenshots for the moment
