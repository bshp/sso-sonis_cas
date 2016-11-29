# CAS Auth for SonisWeb
Use CAS SSO for Faculty/Staff and Students in SonisWeb

This project was created to support SSO functionality in SonisWeb without modification to any directory or database. This is meant to be a starting point so that you can get an idea of how easily you could connect an SSO system to Sonis.

# Requirements
PHP 5.4+

MSSQL Driver for PHP using pdo

LDAP IDs in SonisWeb for Faculty/Staff/Students, can be any other unique id instead

#Installtion

Copy auth folder and cas_login_chk.cfm to your Sonis directory root

Set Database,CAS URL in config.php

Change CACert.pem to match your CAS Certificate CA

Change Login links for Student sections to point to https://sonisweb.example.com/auth

You can also set the following IIS Rewrite rules in the rewrite.config file to redirect to CAS immediately, notice we do not want to rewrite if a query string is appended.