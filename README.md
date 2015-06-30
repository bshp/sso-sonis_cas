# CAS Auth for SonisWeb
Use CAS SSO for Students in SonisWeb

This project was created to support SSO functionality in SonisWeb without modification to any directory or database.

# Requirements
PHP 5.4+

Freetds mssql driver for PHP

CAS SSO with Attribute Release

#Installtion

Copy project to a Sonisweb directory, rename to "auth"

Set Database,CAS URL, SonisWeb URLs and Client Cookie in config.php

Edit ./students/ssoPing.php phpCAS Attribute to match your Students ID attribute

Change CACert.pem to match your CAS Certificate CA

Change Login links for Student sections to point to https://sonisweb.example.com/auth/staff , faculty, or students

You can also set the following IIS Rewrite rules in the rewrite.config file to redirect to CAS immediately, notice we do not want to rewrite if a query string is appended.