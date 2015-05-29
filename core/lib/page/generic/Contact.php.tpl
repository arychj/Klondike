<form method = "post" action = "/?id=main:contact">
	<input type = "hidden" name = "submitted" value = "true"/>
    <br/>
    <table style = "width: 100%;" cellpadding = "0" cellspacing = "0">
        <tr>
            <td style = "width: 15%;">&nbsp;</td>
            <td style = "width: 70%;">
                <table style = "width: 100%;" cellpadding = "0" cellspacing = "0">
                    <tr>
                        <td style = "width: 27%; text-align: right;">&nbsp;</td>
                        <td style = "width: 3%;">&nbsp;</td>
                        <td class = "head" style = "width: 70%; text-align: left;">Contact Me</td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td style = "text-align: right;">Name: </td>
                        <td>&nbsp;</td>
                        <td style = "text-align: left;"><input type = "text" name = "name" value = "{name}"/></td>
                    </tr>
                    <tr>
                        <td style = "text-align: right;">E-mail Address: </td>
                        <td>&nbsp;</td>
                        <td style = "text-align: left;"><input type = "text" name = "email" value = "{email}"/></td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td style = "text-align: right;">Subject: </td>
                        <td>&nbsp;</td>
                        <td style = "text-align: left;"><input type = "text" name = "subject" style = "width: 400px;" value = "{subject}"/></td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td style = "text-align: right;">Comments: </td>
                        <td>&nbsp;</td>
                        <td style = "text-align: left;"><textarea name = "body" style = "width: 400px; height: 150px;">{body}</textarea></td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td style = "text-align: left;"><input type = "submit" value = "Submit" onclick = ""/>&nbsp;&nbsp;&nbsp;<input type = "reset" value = "Clear"/></td>
                    </tr>
                </table>
            </td>
            <td style = "width: 15%;">&nbsp;</td>
        </tr>
    </table>
</form>