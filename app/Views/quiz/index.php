<!DOCTYPE html>
<html>
<head>
    <title>Security Awareness Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body { background-color: #f8f9fa; padding: 40px; } </style>
</head>
<body>

<div class="container" style="max-width: 600px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <h2 class="text-center mb-4">Security Awareness Quiz</h2>
    
    <form action="<?= base_url('quiz/submit') ?>" method="post">
        
        <div class="mb-3">
            <label class="form-label fw-bold">1. You receive an email from "IT-Support" asking for your password. What should you do?</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="q1" value="reply">
                <label class="form-check-label">Reply with the password immediately.</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="q1" value="report" required>
                <label class="form-check-label">Do not reply. Report it to IT Security.</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="q1" value="forward">
                <label class="form-check-label">Forward it to your manager.</label>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">2. How can you check where a link really goes before clicking it?</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="q2" value="click">
                <label class="form-check-label">Click it and see.</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="q2" value="hover" required>
                <label class="form-check-label">Hover your mouse over the link.</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="q2" value="ask">
                <label class="form-check-label">Ask a colleague to click it.</label>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">3. You see a "Urgent Action Required" invoice you didn't expect. What do you do?</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="q3" value="pay">
                <label class="form-check-label">Pay it immediately to avoid fees.</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="q3" value="ignore">
                <label class="form-check-label">Delete it and ignore it.</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="q3" value="it" required>
                <label class="form-check-label">Verify the sender and report to IT/Finance.</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Submit Answers</button>

    </form>
</div>

</body>
</html>
