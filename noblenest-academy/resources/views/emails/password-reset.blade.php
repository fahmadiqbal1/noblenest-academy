<h2>Reset Your Password</h2>
<p>Click the link below to reset your password:</p>
<p><a href="{{ url('/reset-password/' . $token . '?email=' . urlencode($email)) }}">Reset Password</a></p>
<p>This link will expire in 60 minutes.</p>
<p>If you did not request a password reset, no action is needed.</p>
<p>&mdash; NobleNest Global Academy</p>
