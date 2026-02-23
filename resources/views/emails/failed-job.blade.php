<!DOCTYPE html>
<html>

<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:20px;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1); overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#dc3545; padding:20px; text-align:center; color:#ffffff;">
                            <h2 style="margin:0;">🚨 Laravel Failed Job Alert</h2>
                            <p style="margin:5px 0 0 0; font-size:12px;">Action Required</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:25px;">

                            <p style="font-weight:bold; margin-bottom:5px;">Job Name:</p>
                            <div style="background:#f8f9fa; padding:10px; border-radius:5px; margin-bottom:15px;">
                                {{ $jobName }}
                            </div>

                            <p style="font-weight:bold; margin-bottom:5px;">Error Message:</p>
                            <div
                                style="background:#fff3f3; border-left:5px solid #dc3545; padding:15px; border-radius:5px; color:#721c24;">
                                {{ $exception->getMessage() }}
                            </div>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="text-align:center; padding:15px; font-size:12px; color:#888; background:#f8f9fa;">
                            This is an automated message from Laravel Failed Job Monitor.<br>
                            Please check your logs and fix the issue.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>