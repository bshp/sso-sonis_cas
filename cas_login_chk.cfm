<!--- modified for SSO Login from PHP CAS Client --->

<CFINCLUDE template="./common/curpage.cfm">
<CFINCLUDE template="./common/ldap_chk.cfm">
	<cfset session.PID = '#form.PID#'>
	<cfset session.PIN = '#form.PIN#'>
	<cfset session.MoDSTAT = '#form.Modstat#'>
    <cfset session.useappldap = '0'>
    <cfset session.useSTUDldap = '#ldap_chk.stud_ldap#'>
    <cfset session.useADMiNldap = '#ldap_chk.admin_ldap#'>
    <cfset session.useALUMldap = '#ldap_chk.alum_ldap#'>
	<cfset session.useFacldap = '#ldap_chk.fac_ldap#'>

<cfinvoke component = "CFC.login_chk" method = "login" returnvariable = "getresults">
 <cfinvokeargument name = "sonis_ds" value = '#sonis.ds#'> 	<!--- required, do not edit this value --->
 <cfinvokeargument name = "MainDir" value = '#MainDir#'> 	<!--- required, do not edit this value --->
 <cfinvokeargument name = "PID"  value = '#session.PID#'>
 <cfinvokeargument name = "PIN"  value = '#session.PIN#'>
 <cfinvokeargument name = "modstat"  value = '#session.modstat#'>
 <cfinvokeargument name = "loginid"  value = '#webopt.loginid#'>
 <cfinvokeargument name = "key"  value = '#webopt.key_#'>
 <cfinvokeargument name = "schyr"  value = '#webopt.regyr#'>
 <cfinvokeargument name = "sem"  value = '#webopt.regsem#'>

</cfinvoke>
<CFINCLUDE template="./common/randomizer.cfm">
<cfset sc=#randnum#>
<cfset Session.sec_id=#SC#>

<cfif '#getresults#' IS "NOMATCHES" OR '#getresults#' IS "DISABLED">

		<cfif '#getresults#' IS "NOMATCHES">
			<cfif '#trim(right(cgi.HTTP_REFERER,9))#' eq 'NOMATCHES'>
                <cfset session.rtn_url = '#cgi.HTTP_REFERER#'>
            <cfelse>
                <cfset session.rtn_url = '#cgi.HTTP_REFERER#'&'?m=#getResults#'>
            </cfif>
        <cfelse>
        	<cfif '#listcontains(cgi.HTTP_REFERER,"?m=NOMATCHES")#'>
				<cfset rtn = '#listfirst(cgi.HTTP_REFERER,"?")#'>
             <cfelse>
             	<cfset rtn = '#cgi.HTTP_REFERER#'>
			 </cfif>
			<cfif '#trim(right(cgi.HTTP_REFERER,8))#' eq 'DISABLED'>
				<cfset session.rtn_url = '#rtn#'>
            <cfelse>
                <cfset session.rtn_url = '#rtn#'&'?m=#getResults#'>
            </cfif>
        </cfif>
	<!---<cfelse>
		<cfset session.rtn_url = '#cgi.HTTP_REFERER#'&'?m=#getResults#'>
	</cfif>--->
		<cflocation url="#session.rtn_url#" addtoken="No">
<cfelse>
	<cfset ret = 0>
	<!-- Faculty Login -->
	<cfif '#session.modstat#' eq 'FA'>
		<CFINCLUDE template="./common/qry_login_reset_UP.cfm">
			<cfset session.module = 'FAC'>
			<cfset session.cur_module = 'faculty'>
			<cfset client.accessstring = 'H O M E'>
			<cfset client.loginpage = 'facsect.cfm'>
			<cfset client.sepcolor = '81b7ff'>
			<cfset form.pin = '#session.pin#'>
			<cfset form.pid = '#session.pid#'>
			<cfset act='LI'>
			<cfset DESC='Faculty Access LI Succeeded'>
		<CFINCLUDE template="./common/log.cfm">
		<cflocation url="facopts.cfm" addtoken="No">
	</cfif>
	<!-- Student Login -->
	<cfif '#session.modstat#' eq 'ST'>
		<CFINCLUDE template="./common/qry_login_reset_UP.cfm">
			<cfset session.module = 'STUD'>
			<cfset session.cur_module = 'student'>
			<cfset client.accessstring = 'H O M E'>
			<cfset client.loginpage = 'studsect.cfm'>
			<cfset client.sepcolor = '81b7ff'>
			<cfset form.pin = '#session.pin#'>
			<cfset form.pid = '#session.pid#'>
			<cfset act='LI'>
			<cfset DESC='Student Access LI Succeeded'>
		<CFINCLUDE template="./common/log.cfm">
		<cflocation url="studhome.cfm" addtoken="No">
	</cfif>
	<!-- Staff Login -->
	<cfif '#session.modstat#' eq 'ADMN'>
		<CFINCLUDE template="./common/qry_login_reset_UP.cfm">
			<cfset session.module = 'ADMIN'>
			<cfset session.cur_module = 'administrator'>
			<cfset client.accessstring = 'A D M I N I S T R A T I O N'>
			<cfset session.PREF_LANG = 'en_US'>
			<cfset client.loginpage = 'admnsect.cfm'>
		<CFINCLUDE template="./common/randomizer.cfm">
			<cfset sc=#randnum#>
			<cfset Session.sec_id=#SC#>
		<form action="LoginProc.cfm" method="post" name="admnForm" id="admnForm">
			<input type="hidden" name="USER_ID" value="<cfoutput>#form.PID#</cfoutput>">
			<input type="hidden" name="PASSWORD" value="<cfoutput>#form.PIN#</cfoutput>">
		</form>
		<script>
			document.admnForm.submit();
		</script>
	</cfif>
</cfif>
