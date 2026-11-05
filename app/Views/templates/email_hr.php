<!DOCTYPE html>
<html>
<head>
<style>
    body { font-family: Arial, sans-serif; color: #333; }
    .container { max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; }
    .header { background-color: #0056b3; color: white; padding: 10px; text-align: center; }
    .content { padding: 20px; }
    .btn { background-color: #d9534f; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Important: Remote Work Policy Update</h2>
    </div>
    <div class="content">
        <p>Hi Team,</p>
        <p>The HR department has updated the remote work policy effective immediately. All employees are required to review and sign the new document.</p>
        
        <p style="text-align: center; margin: 30px 0;">
            <a href="http://YOUR-SERVER-IP/download/{email}" class="btn">Download Policy PDF</a>
        </p>

        <p>Failure to acknowledge this update may result in temporary suspension of remote work privileges.</p>

        <p>Regards,<br>Human Resources</p>
    </div>
    <!-- Tracking Pixel -->
    <img src="http://YOUR-SERVER-IP/image/{email}" width="1" height="1" style="display:none;">
</div>
</body>
</html>
