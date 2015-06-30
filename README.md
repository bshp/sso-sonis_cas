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

You can also set the following IIS Rewrite rules to redirect to CAS immediately, notice we do not want to rewrite if a query string is appended.

<rewrite>
	<rules>
		<clear />
		<rule name="Redirect Admin Logins" enabled="true" patternSyntax="ECMAScript" stopProcessing="true">
			<match url="(.*)" />
				<conditions logicalGrouping="MatchAll" trackAllCaptures="false">
					<add input="{QUERY_STRING}" pattern="auth=1" negate="true" />
					<add input="{REQUEST_URI}" pattern="^/admnsect\.cfm" />
				</conditions>
			<action type="Redirect" url="https://sonisweb.example.com/auth/staff" redirectType="Temporary" />
		</rule>
		<rule name="Redirect Faculty Logins" enabled="true" patternSyntax="ECMAScript" stopProcessing="true">
			<match url="(.*)" />
				<conditions logicalGrouping="MatchAll" trackAllCaptures="false">
					<add input="{QUERY_STRING}" pattern="auth=1" negate="true" />
					<add input="{REQUEST_URI}" pattern="^/facsect\.cfm" />
				</conditions>
			<action type="Redirect" url="https://sonisweb.example.com/auth/faculty" redirectType="Temporary" />
		</rule>
		<rule name="Redirect Student Logins" enabled="true" patternSyntax="ECMAScript" stopProcessing="true">
			<match url="(.*)" />
				<conditions logicalGrouping="MatchAll" trackAllCaptures="false">
					<add input="{QUERY_STRING}" pattern="auth=1" negate="true" />
					<add input="{REQUEST_URI}" pattern="^/studsect\.cfm" />
				</conditions>
			<action type="Redirect" url="https://sonisweb.example.com/auth/students" redirectType="Temporary" />
		</rule>
	</rules>
</rewrite>
