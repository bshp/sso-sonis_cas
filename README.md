# CAS Auth for SonisWeb
Use CAS SSO for Students in SonisWeb

This project was created to support SSO functionality in SonisWeb without modification to any directory or database.

# Requirements
PHP 5.4+

freetds/pdo or sqlserver driver for PHP

CAS SSO with Attribute Release

#Installtion

Copy project to a web directory, set cas URL and set database settings. Change attributes to match your environment

You can either add a new Directory to your CAS web server or add to your SonisWeb Server.

Change CACert.pem to match your CAS Certificate CA

Change Login links for Student sections to point to https://urltoserver/SSO/ssoPing.php

OR 

Use a URL rewrite rule in IIS to rewrite studsect.cfm to ssoPing.php
