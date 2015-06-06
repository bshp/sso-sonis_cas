# SSO Auth for SonisWeb
Use CAS SSO for SonisWeb

This project was created to support SSO functionality in SonisWeb with very little modification to any directory or database.

# Requirements
PHP 5.4+

Freetds or PDO

CAS SSO with Attribute Release

#Installtion

Copy project to a web directory, set cas URL and set database settings. Change attributes to match your environment

You can either add a new <Directory to CAS or add to your SonisWeb Server.

Change CACert.pem to match your CAS SSL Certificate CA

Change Login links for Student sections to point to https://urltoserver/SSO/ssoPing.php
